how use this project

1. clone this project
2. composer install
3. Use "composer dump-autoload" to generate autoload files
4. copy .env.example to .env
5. php artisan key:generate
6. php artisan migrate
7. php artisan serve

how use api

1. register
2. login
3. token bearer, untuk memakainya , tambahkan authorization , type auth (bearer), paste tokennya sebelah kanan
3. create jobs (pemberi kerja) pakai token
4. getjobs (pemberi kerja) pakai token
