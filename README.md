alice-problem
=============

Getting up and running is easy as 123.

1. `composer install`
2. `bin/console database:create:database`
3. `bin/console doctrine:schema:update --force`

and then just test with `bin/console hautelook:fixtures:load`