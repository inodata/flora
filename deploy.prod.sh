#!/bin/bash
app/console assetic:dump --env=prod
app/console assets:install --env=prod --symlink
app/console cache:clear --env=prod