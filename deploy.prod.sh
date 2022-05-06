#!/bin/bash
php72 app/console assetic:dump --env=prod
php72 app/console assets:install --env=prod --symlink
php72 app/console cache:clear --env=prod
chmod -R 777 app/cache/ app/logs/
chown -R inodata:inodata *
