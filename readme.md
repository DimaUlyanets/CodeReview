## Backend install ##
1. composer install
2. cp .env.example .env
2. create database and provide credentials in .env
2. php artisan migrate:refresh --seed
3. php artisan key:generate
4. php artisan config:clear
5. chmod -R 755 storage
6. set QUEUE_DRIVER=Beanstalkd in .env
7. install drivers for php-ffmpeg

 php artisan serve for local development

### Backend requirements ###
1. PHP >= 5.6.4
2. MYSQL 5.7

### API Endpoints ###
http://159.203.46.95:81/#!/milestone_1/

To clear cache:
composer dump-autoload && php artisan clear-compiled && php artisan optimize