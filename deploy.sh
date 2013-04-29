#!/bin/bash
app/console assets:install --env=prod
app/console assetic:dump --env=prod
app/console cache:clear --env=prod

