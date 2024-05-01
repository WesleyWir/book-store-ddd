#!/bin/sh
set -e
envsubst '\$APP_HOST' < /etc/nginx/app.template > /etc/nginx/conf.d/default.conf
exec "${@}"
