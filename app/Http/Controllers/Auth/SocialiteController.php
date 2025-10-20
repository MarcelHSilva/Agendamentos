<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class SocialiteController extends Controller
{
    /**
     * Redirect to Google OAuth provider
     */
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Check if user already exists
            $user = User::where('email', $googleUser->getEmail())->first();
            
            if ($user) {
                // Update Google ID if not set
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar(),
                    ]);
                }
                
                // Log the user in
                Auth::login($user);
                
                return redirect()->intended(route('dashboard'));
            }
            
            // Get current tenant or first available tenant
            $tenant = app('current_tenant') ?? \App\Models\Tenant::first();
            
            // Create new user
            $user = User::create([
                'tenant_id' => $tenant ? $tenant->id : null,
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'email_verified_at' => now(),
                'password' => Hash::make(Str::random(24)), // Random password since they'll use OAuth
                'role' => 'admin', // Default role for new OAuth users
                'is_active' => true,
            ]);
            
            // Log the user in
            Auth::login($user);
            
            return redirect()->intended(route('dashboard'));
            
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Erro ao autenticar com Google. Tente novamente.');
        }
    }

    /**
     * Show OAuth login options
     */
    public function show(): Response
    {
        return Inertia::render('Auth/OAuth', [
            'canResetPassword' => true,
            'status' => session('status'),
        ]);
    }

    /**
     * Unlink Google account
     */
    public function unlinkGoogle(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        // Don't allow unlinking if it's the only authentication method
        if (!$user->password && $user->google_id) {
            return back()->with('error', 'Você deve definir uma senha antes de desvincular sua conta Google.');
        }
        
        $user->update([
            'google_id' => null,
            'avatar' => null,
        ]);
        
        return back()->with('success', 'Conta Google desvinculada com sucesso.');
    }

    /**
     * Link Google account to existing user
     */
    public function linkGoogle(): RedirectResponse
    {
        // Store a flag in session to identify this as a link request
        session(['oauth_action' => 'link_account']);
        
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle linking Google account
     */
    public function handleLinkCallback(Request $request): RedirectResponse
    {
        try {
            // Check if this is a link request
            $oauthAction = session('oauth_action');
            
            if ($oauthAction !== 'link_account') {
                // This is a regular login, redirect to normal callback
                return $this->handleGoogleCallback();
            }
            
            // Clear the session flag
            session()->forget('oauth_action');
            
            $googleUser = Socialite::driver('google')->user();
            $user = $request->user();
            
            // Check if Google account is already linked to another user
            $existingUser = User::where('google_id', $googleUser->getId())
                ->where('id', '!=', $user->id)
                ->first();
                
            if ($existingUser) {
                return redirect()->route('profile.edit')
                    ->with('error', 'Esta conta Google já está vinculada a outro usuário.');
            }
            
            // Link the Google account
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
            ]);
            
            return redirect()->route('profile.edit')
                ->with('success', 'Conta Google vinculada com sucesso.');
                
        } catch (\Exception $e) {
            return redirect()->route('profile.edit')
                ->with('error', 'Erro ao vincular conta Google. Tente novamente.');
        }
    }
}
