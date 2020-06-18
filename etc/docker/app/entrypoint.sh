#!/bin/bash

set -e

case "$1" in
    "install")
        exec composer install ${@:2} ;;
    "root")
        exec /bin/bash ;;
    "shell")
        "/bin/bash" ;;
    "composer")
        php -d memory_limit=-1 /usr/local/bin/composer ${@:2} ;;
    "wp")
        bin/wp ${@:2} ;;
    ""|"php-fpm")
      tail --pid $$ -n0 -v -F var/logs/admin/*.log var/logs/website/*.log & exec php-fpm ;;
    *)

    "$@" ;;
esac
