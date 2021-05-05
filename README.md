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

### Charges

Lists all users:
```php
use AlexisRiot\Yousign\Facades\Yousign;

$users = Yousign::getUsers();
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.


## Creator

- [@alexis-riot](https://github.com/alexis-riot)
