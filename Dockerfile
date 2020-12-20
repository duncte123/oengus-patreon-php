# Dockerising php is hell
FROM nginx:latest

WORKDIR /oengus-patreon
RUN rm -rf /usr/share/nginx/html/*

COPY ./ /usr/share/nginx/html
