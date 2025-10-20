# Deploy do SaaS na AWS (Guia Passo a Passo)

Este guia te leva do zero até a aplicação publicada na AWS usando Docker e ECS (Fargate), com banco no RDS, cache no ElastiCache (Redis), arquivos no S3 e tráfego atrás de um Application Load Balancer (ALB).

## 0. Pré-requisitos
- Conta AWS ativa e acesso ao Console.
- Domínio próprio (idealmente no Route 53). Ex.: `seu-dominio.com`.
- AWS CLI instalado (opcional, mas recomendado).

## 1. Escolha de Região
Use `us-east-1` (compatível com o template `.env.production.example`). Você pode usar outra, ajustando a variável `AWS_DEFAULT_REGION`.

## 2. Preparar o Código para Produção
- Edite `docker/nginx/sites/default.prod.conf` e altere `SEU_DOMINIO` para o seu domínio real.
- Preencha `.env.production.example` com seus endpoints de RDS/Redis/S3 e domínio.
- A imagem do app (Dockerfile) já executa `composer install` e `npm run build` para gerar os assets.

## 3. Criar Serviços Gerenciados
- S3: Crie um bucket para arquivos públicos (ex.: `seu-bucket`).
- RDS MySQL 8: Instância pequena (`db.t3.micro`), defina usuário/senha e anote o endpoint.
- ElastiCache (Redis): Cluster simples (`cache.t3.micro`), anote o endpoint.

## 4. Repositórios de Imagem (ECR)
- Crie 2 repositórios: `saas-app` e `saas-nginx`.
- Use o script `scripts/ecr_build_push.ps1` para criar e enviar as imagens:
  ```powershell
  ./scripts/ecr_build_push.ps1 -Region us-east-1 -ImageTag v1
  ```

## 5. Cluster e Tasks (ECS Fargate)
- Crie um Cluster ECS (Fargate).
- Crie uma Task Definition (compatibilidade: Fargate) com 2 containers:
  - `app` (imagem do ECR `saas-app:v1`), sem portas públicas, usa env do `.env`.
  - `nginx` (imagem do ECR `saas-nginx:v1`), expõe porta 80.
- Variáveis de ambiente:
  - Use SSM Parameter Store ou Secrets Manager para guardar segredos (`APP_KEY`, credenciais do DB, SMTP). Referencie na Task.
  - Configure `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://seu-dominio.com`, `APP_DOMAIN=seu-dominio.com`, `SESSION_DOMAIN=.seu-dominio.com`.

## 6. Load Balancer e Serviço ECS
- Crie um ALB (público) com listener 80 e 443 (SSL via ACM).
- Crie Target Group para `nginx` (porta 80). Health check: `GET /` (HTTP 200).
- Crie o Service ECS (Fargate) apontando para essa Task Definition e Target Group.
- Defina escalar mínimo 1 task para iniciar.

## 7. Certificado SSL (ACM) e Domínio (Route 53)
- Em ACM, crie um certificado para `seu-dominio.com` e `*.seu-dominio.com`.
- No Route 53, crie registros:
  - `A` (Alias) raiz apontando para o ALB.
  - `A` (Alias) wildcard `*.seu-dominio.com` apontando para o mesmo ALB.

## 8. Migrações e Seed (Produção)
- Conecte via ECS Exec no container `app`:
  ```bash
  php artisan key:generate --show
  # copie o APP_KEY gerado para os parâmetros/variáveis da Task e redeploy
  php artisan migrate --force
  # Opcional: seeds somente se você precisa dos dados de demo
  # php artisan db:seed --force
  php artisan storage:link
  ```

## 9. Filas e Agendador
- Filas: adicione um segundo Service ECS rodando `php artisan queue:work --sleep=3 --tries=3` com a mesma Task Definition (ou uma variante `queue`).
- Agendador: use EventBridge para acionar `php artisan schedule:run` a cada 1 minuto (preferível ao `schedule:work` em produção).

## 10. Observabilidade
- Ative logs do `nginx` e `app` no CloudWatch.
- Configure alarmes básicos (CPU/Memory) e health checks no Target Group.

## 11. Custos (estimativa mínima)
- ALB (~US$ 16/mês + data processing).
- Fargate (depende de vCPU/Mem; comece com 0.25 vCPU/0.5GB).
- RDS e ElastiCache (micro) variam por região; comece no menor tamanho.
- S3 custo por armazenamento e requisições.

## 12. Notas Importantes
- Multi-tenancy: `server_name` do Nginx precisa do wildcard: `~^(?<subdomain>.+)\.seu-dominio.com$ seu-dominio.com *.seu-dominio.com;`.
- Vite em produção: já construído dentro da imagem do app; `nginx` serve `public/build`.
- Evite rodar MySQL em contêiner em produção; prefira RDS.
- Segurança: nunca exponha o container `app` diretamente; todo tráfego público entra via `nginx` + ALB.