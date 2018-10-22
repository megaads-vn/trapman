# Trapman
This package use for catching laravel exception and send email to notifications.

## Installation

```
composer require megaads/trapman
```

After install package add to `app.php` providers 
```
 Megaads\Trapman\TrapmanServiceProvider::class
```
and run publish config command
```
php artisan publish:vendor --provider="Megaads\Trapman\TrapmanServiceProvider"
```
then add some config to `trapman.php` in project config directory. Add email rest api url, 
email system username and password for package request token when push email notify.

```
return [
    'email_api' => env('TRAP_EMAIL_URL', ''),
    'email_user' => env('TRAP_EMAIL_USER', ''),
    'email_password' => env('TRAP_EMAIL_PASSWORD', '')
];
```

Finally, open file ``app/Exceptions/Handler.php`` change extends class from `ExceptionHandler` to 
`TrapmanHandler` like this: 
```
class Handler extends TrapmanHandler
```