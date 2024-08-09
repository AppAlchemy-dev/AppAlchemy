# AppAlchemy

[![Latest Version on Packagist](https://img.shields.io/packagist/v/appalchemy-dev/appalchemy.svg?style=flat-square)](https://packagist.org/packages/appalchemy-dev/appalchemy)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/appalchemy-dev/appalchemy/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/appalchemy-dev/appalchemy/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/appalchemy-dev/appalchemy/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/appalchemy-dev/appalchemy/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/appalchemy-dev/appalchemy.svg?style=flat-square)](https://packagist.org/packages/appalchemy-dev/appalchemy)

AppAlchemy is a Laravel package that helps you transform your web applications into mobile-ready experiences. It provides tools and utilities to detect mobile app requests, conditionally render content, and apply mobile-specific styling.

## Installation

You can install the package via composer:

```bash
composer require appalchemy-dev/appalchemy
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="appalchemy-config"
```

This is the contents of the published config file:

```php
return [
    'user_agent' => 'AppAlchemy',
];
```

## Usage

### Detecting AppAlchemy Requests

AppAlchemy provides a middleware to detect requests coming from your mobile app. To use it, add the middleware to your routes or route groups:

```php
Route::middleware('detect-appalchemy')->group(function () {
    // Your routes here
});
```

Or add it to the `$middleware` array in your `app/Http/Kernel.php` to apply it globally:

```php
protected $middleware = [
    // Other middleware...
    \AppAlchemy\Http\Middleware\DetectAppAlchemy::class,
];
```

### Conditional Rendering

You can use Blade directives to conditionally render content for your mobile app:

```php
@alchemyapp
    This content will only be visible in the AppAlchemy mobile app.
@endalchemyapp

@nonalchemyapp
    This content will be visible everywhere except the AppAlchemy mobile app.
@endnonalchemyapp
```

### Styling

AppAlchemy automatically adds an `appalchemy-app` class to the `<html>` element when the request comes from your mobile app. You can use this class to apply mobile-specific styles:

```css
.appalchemy-app .some-element {
    /* Mobile-specific styles */
}
```

#### Tailwind CSS Integration

AppAlchemy provides a custom Tailwind CSS variant for easy mobile-specific styling. You can add it to your `tailwind.config.js` file manually or use our Artisan command:

```bash
php artisan appalchemy:tailwind-config
```

This command will automatically add the following plugin to your Tailwind configuration:

```javascript
plugins: [
    plugin(function ({addVariant}) {
        addVariant('appalchemy-app', ['&.appalchemy-app', '.appalchemy-app &']);
    }),
    // ... other plugins
],
```

This allows you to use the `appalchemy-app:` variant in your Tailwind classes

You can use this variant with any Tailwind utility classes to create mobile-specific styles easily.

## Custom Styling

You can add custom styles that will only be applied in the AppAlchemy app:

```php
use AppAlchemy\Facades\AppAlchemy;

AppAlchemy::addCustomStyle('.my-class { color: red; }');
```

These styles will be automatically injected when the app is detected.

## JavaScript Bridge

AppAlchemy provides a JavaScript bridge for communication between your web app and the native app features. To use it, make sure you've included the `appAlchemyBridge` variable in your main layout:

```html

    
    {!! $appAlchemyStyles ?? '' !!}
    {!! $appAlchemyBridge ?? '' !!}

```

You can then use the bridge in your JavaScript:

```javascript
// Send a message to the native app
AppAlchemy.sendToNative('someAction', { key: 'value' });

// Listen for messages from the native app
window.addEventListener('appalchemy', function(event) {
    console.log('Received from native:', event.detail.action, event.detail.data);
});
```

Make sure to implement the corresponding native code in your iOS and Android apps to handle these messages.

### Using the AppAlchemy Facade

You can use the AppAlchemy facade to access various helper methods:

```php
use AppAlchemy\Facades\AppAlchemy;

if (AppAlchemy::isAppAlchemyApp()) {
    // Do something for AppAlchemy app
}

$wrappedContent = AppAlchemy::wrapContent($content, 'custom-wrapper-class');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Andrew Weir](https://github.com/andruu)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
