# Stage para referenciar os artefatos gerados no app
FROM saas-app:latest AS app

# Imagem Nginx
FROM nginx:alpine

# Copia configuração base e site
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/sites/default.prod.conf /etc/nginx/conf.d/default.conf

# Copia artefatos públicos já construídos na imagem do app
COPY --from=app /var/www/html/public /var/www/html/public

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]