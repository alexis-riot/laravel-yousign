# Laravel wrapper for the YouSign API

## Installation

You can install the package via composer:

```bash
composer require alexis-riot/laravel-yousign
```

The service provider will automatically register itself.

You must publish the config file with:
```bash
php artisan vendor:publish --provider="AlexisRiot\Yousign\YousignServiceProvider" --tag="config"
```

This is the contents of the config file that will be published at `config/yousign.php`:

```php
return [
    'api_key' => env('YOUSIGN_API_KEY', 'production'), // ['production', 'staging']
    'api_env' => env('YOUSIGN_API_ENV'),
];
```

## Usage

### Users

Lists all users:
```php
use AlexisRiot\Yousign\Facades\Yousign;

$users = Yousign::getUsers();
```

### Procedure

Send a file:
```php
use AlexisRiot\Yousign\Facades\Yousign;

$file = Yousign::createFile([
    "name" => "devis.pdf",
    "content" => "JVBERi0xLjUKJb/3ov4KNiA...",
]);
```

Create a procedure:  
_The creation of a procedure is fully dynamic, you can add multiple members and multiple files._
```php
use AlexisRiot\Yousign\Facades\Yousign;
use AlexisRiot\Yousign\YousignProcedure;

$file = Yousign::createFile([
    "name" => "devis.pdf",
    "content" => "JVBERi0xLjUKJb/3ov4KNiA...",
]);

$procedure = new YousignProcedure();
$procedure
    ->withName("My procedure")
    ->withDescription("The description of my procedure")
    ->addMember([
        'firstname' => "Alexis",
        'lastname' => "Riot",
        'email' => "contact@alexisriot.fr",
        'phone' => "+33 600000000",
    ], [$file])
    ->send();
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.


## Creator

- [@alexis-riot](https://github.com/alexis-riot)
