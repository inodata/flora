#!/bin/sh

CURRENT_FIXTURES=$PWD"/src/Inodata/FloraBundle/DataFixtures/ORM/Required"

# Rebuild doctrine fixtures
php app/console doctrine:fixtures:load --fixtures=$CURRENT_FIXTURES 

