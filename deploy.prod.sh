#!/bin/bash
app/console assetic:dump --env=prod
app/console assets:install --env=prod --symlink
app/console cache:clear --env=prod
chmod -R 777 app/cache/ app/logs/
chown -R inodata:inodata *