# Start migration
php artisan migrate:jwt-auth

## Publish config
```
php artisan vendor:publish --provider="JWTAuth\Providers\JWTAuthServiceProvider" --tag="jwt-auth-config"
php artisan vendor:publish --tag=jwt-auth-config
```

## register config
```php
// config/app.php
'providers' => [
    // ...
    JWTAuth\Providers\TokenStorageServiceProvider::class,
],
```

## Add daily clean db if you use database driver
```php
// App\Console\Kernel
protected function schedule(Schedule $schedule)
{
    $schedule->command('jwt:clear-tokens')->daily();
}
```