# SaaS Beauty Manager

Um sistema SaaS completo para gestão de salões de beleza, clínicas estéticas e barbearias, desenvolvido com Laravel 11, Vue 3, Inertia.js e Tailwind CSS.

## 🚀 Características Principais

- **Multi-tenancy**: Suporte a múltiplos clientes com subdomínios
- **Autenticação OAuth**: Login com Google integrado
- **Gestão de Agendamentos**: Sistema completo de agendamento online
- **Planos e Assinaturas**: Integração com Stripe e MercadoPago
- **WhatsApp Integration**: Notificações via WhatsApp (Evolution API)
- **Dashboard Analytics**: Relatórios e métricas em tempo real
- **Sistema de Permissões**: Controle granular de acesso
- **Auditoria**: Log completo de ações do sistema

## 🛠️ Tecnologias Utilizadas

- **Backend**: Laravel 11, PHP 8.3
- **Frontend**: Vue 3, Inertia.js, Tailwind CSS
- **Database**: MySQL 8.0
- **Cache**: Redis
- **Queue**: Redis
- **Containerização**: Docker & Docker Compose
- **Pagamentos**: Stripe, MercadoPago
- **OAuth**: Laravel Socialite (Google)

## 📋 Pré-requisitos

- Docker & Docker Compose
- Git
- Node.js 18+ (para desenvolvimento local)
- Composer (para desenvolvimento local)

## 🚀 Instalação com Docker

### 1. Clone o repositório

```bash
git clone <repository-url>
cd SaaS
```

### 2. Configure as variáveis de ambiente

```bash
cp .env.example .env
```

Edite o arquivo `.env` com suas configurações:

```env
# Configurações básicas
APP_NAME="SaaS Beauty Manager"
APP_URL=http://localhost
APP_DOMAIN=localhost

# Database
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=saas_beauty
DB_USERNAME=root
DB_PASSWORD=secret

# Google OAuth (opcional)
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret

# Stripe (opcional)
STRIPE_KEY=your_stripe_key
STRIPE_SECRET=your_stripe_secret

# MercadoPago (opcional)
MERCADOPAGO_ACCESS_TOKEN=your_mercadopago_token
MERCADOPAGO_PUBLIC_KEY=your_mercadopago_public_key

# WhatsApp (opcional)
WHATSAPP_API_URL=your_evolution_api_url
WHATSAPP_API_KEY=your_evolution_api_key
```

### 3. Inicie os containers

```bash
docker-compose up -d
```

### 4. Instale as dependências e configure o projeto

```bash
# Entre no container da aplicação
docker-compose exec app bash

# Instale as dependências PHP
composer install

# Gere a chave da aplicação
php artisan key:generate

# Execute as migrações
php artisan migrate

# Execute os seeders (dados de demonstração)
php artisan db:seed

# Instale as dependências Node.js
npm install

# Compile os assets
npm run build
```

### 5. Acesse a aplicação

- **Aplicação Principal**: http://localhost
- **MailHog (emails)**: http://localhost:8025
- **Tenant Demo**: 
  - http://bella-vita.localhost (Salão Bella Vita)
  - http://clinica-silva.localhost (Clínica Dr. Silva)
  - http://barbearia-moderna.localhost (Barbearia Moderna)

## 👥 Usuários de Demonstração

Após executar os seeders, você terá acesso aos seguintes usuários:

### Salão Bella Vita (bella-vita.localhost)
- **Email**: admin@bellavita.com
- **Senha**: password
- **Plano**: Profissional

### Clínica Dr. Silva (clinica-silva.localhost)
- **Email**: admin@clinicasilva.com
- **Senha**: password
- **Plano**: Premium

### Barbearia Moderna (barbearia-moderna.localhost)
- **Email**: admin@barbeariamoderna.com
- **Senha**: password
- **Plano**: Básico

## 🏗️ Estrutura do Projeto

```
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Middleware/
│   │       └── TenantResolver.php
│   ├── Models/
│   └── Traits/
├── database/
│   ├── migrations/
│   └── seeders/
├── docker/
│   ├── nginx/
│   ├── php/
│   └── mysql/
├── resources/
│   ├── js/
│   │   ├── Components/
│   │   ├── Layouts/
│   │   └── Pages/
│   └── css/
└── routes/
```

## 🔧 Comandos Úteis

### Docker

```bash
# Iniciar containers
docker-compose up -d

# Parar containers
docker-compose down

# Ver logs
docker-compose logs -f app

# Entrar no container
docker-compose exec app bash

# Rebuild containers
docker-compose up -d --build
```

### Laravel

```bash
# Executar migrações
php artisan migrate

# Executar seeders
php artisan db:seed

# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Executar queue workers
php artisan queue:work

# Executar scheduler
php artisan schedule:work
```

### Frontend

```bash
# Desenvolvimento
npm run dev

# Build para produção
npm run build

# Watch mode
npm run watch
```

## 🌐 Multi-tenancy

O sistema utiliza subdomínios para separar os tenants:

- Cada tenant possui seu próprio subdomínio
- Dados são isolados por tenant_id
- Middleware `TenantResolver` identifica o tenant automaticamente
- Configuração de DNS local necessária para desenvolvimento

### Configuração DNS Local

Adicione ao seu arquivo hosts (`/etc/hosts` no Linux/Mac ou `C:\Windows\System32\drivers\etc\hosts` no Windows):

```
127.0.0.1 localhost
127.0.0.1 bella-vita.localhost
127.0.0.1 clinica-silva.localhost
127.0.0.1 barbearia-moderna.localhost
```

## 💳 Integração de Pagamentos

### Stripe

1. Crie uma conta no [Stripe](https://stripe.com)
2. Obtenha suas chaves de API
3. Configure no `.env`:

```env
STRIPE_ENABLED=true
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

### MercadoPago

1. Crie uma conta no [MercadoPago Developers](https://developers.mercadopago.com)
2. Obtenha suas credenciais
3. Configure no `.env`:

```env
MERCADOPAGO_ENABLED=true
MERCADOPAGO_ACCESS_TOKEN=TEST-...
MERCADOPAGO_PUBLIC_KEY=TEST-...
```

## 📱 WhatsApp Integration

O sistema suporta integração com WhatsApp via Evolution API:

1. Configure uma instância da Evolution API
2. Configure no `.env`:

```env
WHATSAPP_ENABLED=true
WHATSAPP_API_URL=https://your-evolution-api.com
WHATSAPP_API_KEY=your-api-key
WHATSAPP_INSTANCE_NAME=your-instance
```

## 🔐 OAuth com Google

1. Crie um projeto no [Google Cloud Console](https://console.cloud.google.com)
2. Configure OAuth 2.0
3. Adicione as URLs de callback:
   - `http://localhost/auth/google/callback`
   - `http://your-domain.com/auth/google/callback`
4. Configure no `.env`:

```env
GOOGLE_CLIENT_ID=your-client-id
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

## 📊 Monitoramento

### Logs

```bash
# Logs da aplicação
tail -f storage/logs/laravel.log

# Logs do Docker
docker-compose logs -f app
```

### Queue Monitoring

```bash
# Ver jobs na fila
php artisan queue:monitor

# Processar jobs falhados
php artisan queue:retry all
```

## 🚀 Deploy em Produção

### Preparação

1. Configure variáveis de ambiente de produção
2. Configure SSL/HTTPS
3. Configure DNS para subdomínios
4. Configure backup automático

### Comandos de Deploy

```bash
# Otimizações de produção
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev
npm run build
```

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📝 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## 📞 Suporte

Para suporte e dúvidas:

- 📧 Email: support@saasbeuty.com
- 📖 Documentação: [docs.saasbeuty.com](https://docs.saasbeuty.com)
- 🐛 Issues: [GitHub Issues](https://github.com/your-repo/issues)

---

**Desenvolvido com ❤️ para a comunidade de beleza e estética**
