## Backend install ##
1. composer install
2. php artisan migrate:refresh --seed
3. php artisan key:generate
4. php artisan config:clear
5. Set 755 permissions for /storage
6. set QUEUE_DRIVER=Beanstalkd in env
7. install drivers for php-ffmpeg

 php artisan serve for local development

### Backend requirements ###
1. PHP >= 5.6.4
2. MYSQL 5.7

### API Endpoints ###
http://159.203.46.95:81/#!/milestone_1/

To clear cache:
composer dump-autoload && php artisan clear-compiled && php artisan optimize