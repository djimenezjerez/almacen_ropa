# Sistema de gestión de distribución de material escolar

# Pasos para la instalación

1. Instalar las dependencias de laravel

```sh
composer install
```

2. Generar la llave de cifrado
```sh
composer run-script post-root-package-install
composer run-script post-create-project-cmd
composer run-script post-autoload-dump
```

3. Instalar las dependencias de javascript
```sh
npm i -g yarn
yarn install
yarn prod
```

4. Si fuera necesario, cambiar los datos de acceso a la base de datos en el archivo _.env_

5. Migrar la base de datos
```sh
php artisan migrate:install
php artisan migrate:fresh
php artisan db:seed --class=DatabaseSeeder
```

6. Ingresar con las credenciales de cualquiera de los usuarios listados en el archivo _database/seeders/UsersSeeder.php_ en la ruta del navegador web, por defecto: *http://localhost*

7. Para el despliegue en producción se debe modificar el archivo de configuración de *Sanctum* ubicado en la ruta _config/sanctum.php_ y establecer el nombre de dominio asignado por el DNS

8. El nombre de dominio también debe establecerse en el la variable *APP_URL* del archivo _.env_, a la vez de establecer la variable *APP_DEBUG* a *production*
