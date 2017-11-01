#!/bin/sh
bin/console cache:clear --no-warmup
bin/console doctrine:schema:drop --force
bin/console doctrine:schema:update -f
phpunit
