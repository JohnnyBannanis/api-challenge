<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>


# Ejecución
Luego de clonar el repositorio se necesario generar el archivo ".env" y una APP_KEY:

        cp .env.example .env
        php artisan key:generate
        
Tras esto se ejecutan las migraciones a la base de datos.
        
        php artisan migrate --path=database/migrations/API

Finalmente se sirve la aplicacion

        php artisan serve
        
        > INFO  Server running on [http://127.0.0.1:8000].
        
# Modelo de datos

[Diagrama]

Para este problema se optó por modelar un conjunto de tablas relacionales a ser implementadas mediante migraciones de Elocuent ORM. Inmediatamente complementando el enunciado se decidió añadir una tabla adicional correspondiente a Usuario. Esta permite vincular una persona a un dispositivo de pago y a su vez a una venta, lo encontre práctico como práctica común de los puntos de venta o cajas en donde una persona puede estar a cargo de una venta, dicha acción será manipulada en el respectivo controlador del Dispositivo donde se podrá hacer LogIn y LogOut de este mediante un Endpoint de la API con un RUT de usuario.

Se respeta la nomenclatura dada en el enunciado para los nombres de los campos y tablas, concluyendo con un modelo que relaciona a una Comercio con un Dispositivo desde el cual se generan Ventas. Estas últimas poseen un código de seguridad de la transacción definido como una cadena de texto de 8 dígitos que se almacena encriptado en la base de datos y se puede acceder a él a trave de la API, mostrándolo de forma desencriptada para así ser utilizado en la anulación de una venta acordé a la lógica de negocio del enunciado.

Se opta por la decisión de mantener los registros de ventas anuladas, de modo que estás siguen existiendo en la base de datos y una variable booleana lleva control de si está anulada o no, por defecto este valor será falso.

Se incluye además en cada tabla registro de la fecha de creación y modificación mediante timestamps.

# API

## Rutas/Endpoints

| HTTP verb         | endpoint    | controller    | 
|-------------------|-------------|---------------|
| POST   | api/anular    | API\VentaController@anular            | 
|GET/HEAD|         api/comercios | API\ComercioController@index |
  |POST            |api/comercios |API\ComercioController@store|
 | GET/HEAD        |api/comercios/{comercio} | API\ComercioController@show|
  |PUT/PATCH       |api/comercios/{comercio} |API\ComercioController@update|
 | GET/HEAD       | api/dispositivos |API\DispositivoController@index|
  |POST           | api/dispositivos |API\DispositivoController@store|
  |GET/HEAD       | api/dispositivos/{dispositivo} |API\DispositivoController@show|
 | POST            |api/login | API\DispositivoController@logInUsuario|
  |POST            |api/logout |API\DispositivoController@logOutUsuario|
  |GET/HEAD        |api/usuarios |API\UsuarioController@index|
  |POST           | api/usuarios |API\UsuarioController@store|
  |GET/HEAD        |api/usuarios/{usuario} | API\UsuarioController@show|
  |PUT/PATCH       |api/usuarios/{usuario} |API\UsuarioController@update|
  |GET/HEAD        |api/ventas | API\VentaController@index|
  |POST           | api/ventas |API\VentaController@store|
  |GET/HEAD       | api/ventas/{venta} |API\VentaController@show|


Se toma cuidado en evitar la modificación de datos que afecten a la integridad de la información, como por ejemplo los RUT de comercio y usuarios, modificación del estado de una venta anulada o no, el monto de una venta después de ingresada, los registros de fecha de creación y modificación.

Se omiten los verbos DELETE, no hay eliminaciones directas en la base de datos.

## Complementos al enunciado

- Se incluye la tabla de usuarios para vincularlos a una venta en caso que aplique.
- Se incluyen Endpoint en la API para LogIn y LogOut de un usuario en el dispositivo.
- Se incluye validación de RUT para el Comercio y para el Usuario.
- Se incluyen validaciones de campos para cada Endpoint cuando es requerido.
- Se incluye validación de unicidad para los comercios y usuarios.
- Se válida la lógica de negocio para evitar que una venta sea anulada más de una vez.

## Lógica del problema

El enunciado especificaba que para el sistema de puntos de Haulmer se llevaría registro de estos con un saldo de puntos, dichos son almacenados en la tabla de Comercio y estos se inicializan en 0. Cuando se ejecuta una compra se verifica que el Dispositivo esté vinculado a un comercio buscando con la id del Dispositivo, y si encuenta un Comercio en reelacion con el dispositivo se efectua la venta y se suman 10 puntos.

Cuando una venta es anulada se debe hacer match com dos campos, el id de la venta y el codigo de seguridad, para obtener este ultimo se debe consultar previamente a travez de la api en el endpoint api/ventas/{venta} el cual retorna en su respuesta el codigo de seguridad desencriptado. En caso de ser validos ambos id y codigo de seguridad y pertenecer a la misma venta puede ser anulada, lo que provoca que se resten 10 puntos al comercio asociado al dispositivo por el cual se efectuo dicha venta.

## Buenas prácticas

En general se intentan conservar buenas prácticas en el desarrollo manteniendo un código ordenado, simplificado y coherente, nombres de variables descriptivos, manejo de casos de error por separado, comentarios breves cuando consideré oportuno.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
