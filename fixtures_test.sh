#!/bin/sh

CURRENT_FIXTURES=$PWD"/src/Inodata/FloraBundle/DataFixtures/ORM/Test"

# Rebuild doctrine fixtures
php app/console doctrine:fixtures:load --fixtures=$CURRENT_FIXTURES

