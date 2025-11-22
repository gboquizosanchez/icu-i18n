# Laravel ICU Translation

[![Latest Stable Version](https://img.shields.io/packagist/v/gboquizosanchez/icu-i18n.svg)](https://packagist.org/packages/gboquizosanchez/icu-i18n)
[![Software License](https://img.shields.io/badge/license-MIT-red.svg)](https://packagist.org/packages/gboquizosanchez/icu-i18n)
[![Total Downloads](https://img.shields.io/packagist/dt/gboquizosanchez/icu-i18n.svg)](https://packagist.org/packages/gboquizosanchez/icu-i18n)

**Advanced Internationalization support for Laravel.**

This package extends the native Laravel Translator to provide support for **ICU MessageFormat** (complex plurals, gender selection, localized currency/dates) and implements a smart **Regional Locale Fallback** strategy to handle vendor packages gracefully.

## 🚀 Features

- **ICU MessageFormat Support**: Use standard syntax like `{{count, plural, ...}}` or `{{gender, select, ...}}` directly in your translation files.
- **Smart Regional Fallback**: Automatically degrades specific namespaces or files from a regional locale (e.g., `es_MX`) to a base locale (e.g., `es`).
  - *Problem Solved:* You want your app to use `es_MX` for currency formatting, but your vendor packages (like `laravel/ui` or `spatie/permission`) only publish translations in `es`. This package handles that automatically.
- **Seamless Integration**: Works as a drop-in replacement for `trans()`, `__()`, and `@lang`.
- **Native Performance**: Uses PHP's native `intl` extension and `MessageFormatter`.

## 📦 Installation

You can install the package via composer:

```bash
composer require vendor/icu-translation
```

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Vendor\IcuTranslation\I18nServiceProvider"
```

## 🔧 Configuration

The configuration file `config/icu.php` controls the **Regional Fallback Strategy**.

### How logic works

When you request a translation (e.g., `__('validation.required')`) while your app locale is set to **Regional** (e.g., `es_MX`), the translator decides whether to use `es_MX` or fall back to `es` based on two rules:

1. **Namespace Allowed list**: If the translation namespace is **NOT** in the `namespaces` list, it falls back to the base language (`es`).
2. **File Blocklist**: If the translation file is in the `files` list, it forces the base language (`es`), even if the namespace is allowed listed.

### Default Configuration

```php
// config/icu.php

return [
    'regionals' => [
        /*
        * Allowed list Namespaces.
        * These will use the full regional locale (es_MX).
        * '*' represents your application's local files (lang/es_MX/...).
        * Vendor packages are excluded by default to prevent missing translation errors.
        */
        'namespaces' => [
            '*',
        ],

        /*
         * Excluded Files.
         * These will ALWAYS force the base locale (es).
         * Useful for standard Laravel files that usually come in generic folders.
         */
        'files' => [
            // 'validation',
            // 'auth',
            // 'passwords',
        ],
    ],
];
```

## 📖 Usage

### 1. ICU MessageFormat

You can use standard ICU syntax in your JSON or PHP translation files.

**lang/en/messages.php**
```php
return [
    'welcome' => 'Hello, {name}.',
    'balance' => 'Your balance is {amount, number, currency}',
    'apples'  => '{count, plural, =0{No apples} one{one apple} other{# apples}}',
];
```

**In your Blade views:**

```blade
{{-- Basic variable --}}
{{ __('messages.welcome', ['name' => 'John']) }}
{{-- Output: Hello, John. --}}

{{-- Automatic Currency Formatting (uses app locale) --}}
{{ __('messages.balance', ['amount' => 1250.50]) }}
{{-- Output (en_US): Your balance is $1,250.50 --}}

{{-- Complex Pluralization --}}
{{ __('messages.apples', ['count' => 5]) }}
{{-- Output: 5 apples --}}
```

### 2. Handling Object Parameters

The translator automatically converts `DateTime` objects to timestamps and objects implementing `__toString()` to strings before passing them to the ICU formatter.

```php
$user = new User(['name' => 'Alice']); // implements __toString
$date = new DateTime('2023-10-01');

echo __('messages.audit', ['user' => $user, 'date' => $date]);
```

## 🧩 Regional Fallback Examples

Assume `App::setLocale('es_MX')`.

#### Scenario A: Application Strings
You have a file `lang/en_GB/home.php`.
* **Config**: `namespaces => ['*']`
* **Call**: `__('home.title')`
* **Result**: Loads from `en_GB`. (Matches `*` allowed list).

#### Scenario B: Vendor Package
You use a package `courier` that only has translations in `lang/vendor/courier/es/messages.php`.
* **Config**: `namespaces => ['*']` (Does NOT include 'courier')
* **Call**: `__('courier::messages.error')`
* **Result**: Loads from `en`. (Namespace 'courier' is not allowed listed -> degrades to base locale).

#### Scenario C: Validation Files
You want to use standard Laravel validation messages which typically exist in `lang/en/validation.php`, not `en_US`.
* **Config**: `files => ['validation']`
* **Call**: `__('validation.required')`
* **Result**: Loads from `en`. (File 'validation' is blocklisted -> forces base locale).

## 🧪 Testing

```bash
composer test
```

### Getting Help
If you encounter issues:
1. **Check the logs** - Laravel logs may contain helpful error messages
2. **Verify requirements** - Ensure PHP and Laravel versions meet minimum requirements
3. **Clear cache** - Run `php artisan config:clear` and `php artisan cache:clear`
4. **Open an issue** - [Report bugs or request features](https://github.com/gboquizosanchez/phpstan-report/issues/new)

## Contributing
We welcome contributions! Please feel free to:
- 🐛 **Report bugs** through GitHub issues
- 💡 **Suggest features** or improvements
- 🔧 **Submit pull requests** with bug fixes or enhancements
- 📖 **Improve documentation** or add examples

## Credits 🧑‍💻

- **Author**: [Germán Boquizo Sánchez](mailto:germanboquizosanchez@gmail.com)
- **Built with**: [PHPStan](https://phpstan.org/) - The powerful PHP static analysis tool
- **Framework**: [Laravel](https://laravel.com/) - The PHP framework
- **Contributors**: [View all contributors](../../contributors)

## 📄 License
This package is open-source software licensed under the [MIT License](LICENSE.md).

## 📦 Dependencies

### PHP dependencies 📦
- Spatie Laravel Package Tools [![Latest Stable Version](https://img.shields.io/badge/stable-1.92.7-blue)](https://packagist.org/packages/spatie/laravel-package-tools)
#### Develop dependencies 🔧
- Hermes Dependencies [![Latest Stable Version](https://img.shields.io/badge/stable-1.2.0-blue)](https://packagist.org/packages/hermes/dependencies)
- Larastan Larastan [![Latest Stable Version](https://img.shields.io/badge/stable-v3.8.0-blue)](https://packagist.org/packages/larastan/larastan)
- Laravel Pint [![Latest Stable Version](https://img.shields.io/badge/stable-v1.25.1-blue)](https://packagist.org/packages/laravel/pint)
- Nunomaduro Collision [![Latest Stable Version](https://img.shields.io/badge/stable-v8.8.3-blue)](https://packagist.org/packages/nunomaduro/collision)
- Orchestra Testbench [![Latest Stable Version](https://img.shields.io/badge/stable-v10.7.0-blue)](https://packagist.org/packages/orchestra/testbench)
- Pestphp Pest [![Latest Stable Version](https://img.shields.io/badge/stable-v4.1.4-blue)](https://packagist.org/packages/pestphp/pest)
- Pestphp Pest Plugin Arch [![Latest Stable Version](https://img.shields.io/badge/stable-v4.0.0-blue)](https://packagist.org/packages/pestphp/pest-plugin-arch)
- Pestphp Pest Plugin Laravel [![Latest Stable Version](https://img.shields.io/badge/stable-v4.0.0-blue)](https://packagist.org/packages/pestphp/pest-plugin-laravel)
- Phpstan Extension Installer [![Latest Stable Version](https://img.shields.io/badge/stable-1.4.3-blue)](https://packagist.org/packages/phpstan/extension-installer)
- Phpstan Phpstan Deprecation Rules [![Latest Stable Version](https://img.shields.io/badge/stable-2.0.3-blue)](https://packagist.org/packages/phpstan/phpstan-deprecation-rules)
- Phpstan Phpstan Phpunit [![Latest Stable Version](https://img.shields.io/badge/stable-2.0.8-blue)](https://packagist.org/packages/phpstan/phpstan-phpunit)
- Spatie Laravel Ray [![Latest Stable Version](https://img.shields.io/badge/stable-1.42.0-blue)](https://packagist.org/packages/spatie/laravel-ray)


**Made with ❤️ for the PHP community**
