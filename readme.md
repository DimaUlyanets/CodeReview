## Backend install ##
* composer install
* cp .env.example .env
* create database and provide credentials in .env
* php artisan migrate:refresh --seed
* php artisan key:generate
* php artisan config:clear
* chmod -R 755 storage
* set QUEUE_DRIVER=Beanstalkd in .env
* install drivers for php-ffmpeg
* install elasticsearch on server
* ELASTIC_SEARCH_HOST=localhost:9200 in .env

 php artisan serve for local development

### Backend requirements ###
1. PHP >= 5.6.4
2. MYSQL 5.7

### API Endpoints ###
http://159.203.46.95:81/#!/milestone_1/

To clear cache:
composer dump-autoload && php artisan clear-compiled && php artisan optimize