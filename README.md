
Instrucciones a seguir:
1. Clonar repositorio:
```bash
git clone git remote add origin https://github.com/Deqc369/Prueba_Tecnica_Laravel.git

2. Instalar dependencias: composer install
npm install

3. Configurar entorno: cp .env.example .env
php artisan key:generate

4. Configurar base de datos en .env:
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=biblioteca
DB_USERNAME=postgres
DB_PASSWORD=tu_password

5. Ejecutar migraciones y seeders:
php artisan migrate
php artisan db:seed

6. Iniciar servidor:
php artisan serve

7.Iniciar cola para tareas programadas:
php artisan queue:work
