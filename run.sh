#!/bin/bash

echo "Porject is running..."

docker compose up -d
docker compose exec php composer install
docker compose exec php bin/console ask:install

MY_VAR=$(grep DOCKER_NGINX_PORT ./.env | cut -d'=' -f 2-)

echo "Project is ready!!!"
echo "You can see result on http://127.0.0.1:$MY_VAR"
