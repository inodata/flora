#!/bin/bash
app/console assets:install --env=prod --symlink
app/console assetic:dump --env=prod
app/console cache:clear --env=prod

