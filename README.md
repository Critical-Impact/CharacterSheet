Character Sheet
========================

Steps for usage

1. Checkout repo
2. Move to web hosting package with PHP
3. From console run bin/vendors install to install the vendors
4. Set both the app/cache and app/logs folder to 777
5. Set the database configuration in app/config/parameters.yml
6. If running a new DB run app/console doctrine:schema:update --force from SSH
