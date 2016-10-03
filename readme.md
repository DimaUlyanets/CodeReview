## Backend install ##
1. composer install
2. php artisan migrate
3. php artisan serve

### Backend requirements ###
1. PHP 5.6.4
2. MYSQL 5.7

### API Endpoints ###
http://159.203.46.95:81/#!/milestone_1/

To clear cache:
composer dump-autoload && php artisan clear-compiled && php artisan optimize