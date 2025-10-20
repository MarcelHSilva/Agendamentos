Param(
  [string]$Region = "us-east-1",
  [string]$ImageTag = "latest",
  [string]$RepoApp = "saas-app",
  [string]$RepoNginx = "saas-nginx"
)

Write-Host "Obtendo conta AWS…"
$AccountId = (aws sts get-caller-identity --query Account --output text)

Write-Host "Fazendo login no ECR…"
aws ecr get-login-password --region $Region | docker login --username AWS --password-stdin "$AccountId.dkr.ecr.$Region.amazonaws.com"

Write-Host "Criando repositórios (se não existem)…"
aws ecr describe-repositories --repository-names $RepoApp 2>$null | Out-Null
if ($LASTEXITCODE -ne 0) { aws ecr create-repository --repository-name $RepoApp | Out-Null }
aws ecr describe-repositories --repository-names $RepoNginx 2>$null | Out-Null
if ($LASTEXITCODE -ne 0) { aws ecr create-repository --repository-name $RepoNginx | Out-Null }

$AppImage = "$AccountId.dkr.ecr.$Region.amazonaws.com/$RepoApp:$ImageTag"
$NginxImage = "$AccountId.dkr.ecr.$Region.amazonaws.com/$RepoNginx:$ImageTag"

Write-Host "Build da imagem APP (Laravel)…"
docker build -t $AppImage -f ./Dockerfile .

Write-Host "Build da imagem NGINX…"
docker build -t $NginxImage -f ./docker/nginx/Dockerfile ./docker/nginx

Write-Host "Publicando imagens no ECR…"
docker push $AppImage
docker push $NginxImage

Write-Host "Concluído. Imagens publicadas:" $AppImage "," $NginxImage