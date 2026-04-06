<div align="center">

<img src="https://raw.githubusercontent.com/twitter/twemoji/master/assets/svg/1f310.svg" width="100" alt="i18n">

# `gboquizosanchez/icu-i18n`

**Advanced ICU Internationalization for Laravel**

[![Latest Stable Version](https://img.shields.io/packagist/v/gboquizosanchez/icu-i18n.svg)](https://packagist.org/packages/gboquizosanchez/icu-i18n)
[![Total Downloads](https://img.shields.io/packagist/dt/gboquizosanchez/icu-i18n.svg)](https://packagist.org/packages/gboquizosanchez/icu-i18n)
[![PHP](https://img.shields.io/badge/PHP-%5E8.3-777BB4?logo=php&logoColor=white)](https://packagist.org/packages/gboquizosanchez/icu-i18n)
[![Laravel](https://img.shields.io/badge/Laravel-11%20%7C%2012-FF2D20?logo=laravel&logoColor=white)](https://packagist.org/packages/gboquizosanchez/icu-i18n)
[![License: MIT](https://img.shields.io/badge/License-MIT-22C55E.svg)](LICENSE.md)
[![PHPStan](https://img.shields.io/badge/PHPStan-Level%20Max-blue)](https://phpstan.org/)
[![Tests](https://img.shields.io/badge/Tests-Pest%20v4-9C27B0)](https://pestphp.com/)

---

*Your app uses `es_MX` but vendor packages only ship `es`? Fixed.*
*Complex plurals for Russian, Arabic, Polish and Swahili with zero extra configuration.*

</div>

---

## Why this package?

Laravel's built-in translation support works great for simple cases — but falls short in real-world multilingual apps:

| Problem | Without this package | With `icu-i18n` |
|---|---|---|
| Plurals in Russian / Arabic / Polish | ❌ Impossible with `:count` | ✅ Native ICU MessageFormat |
| App in `es_MX`, vendor only has `es` | ❌ Missing keys or wrong locale | ✅ Automatic regional fallback |
| Currency formatting by locale | ❌ Manual formatting | ✅ `{amount, number, currency}` |
| Localized dates | ❌ Carbon + extra setup | ✅ `{date, date, long}` |
| Drop-in replacement | — | ✅ Same `__()`, `trans()`, `@lang` |

---

## 🚀 Features

- **ICU MessageFormat Support**: Use standard syntax like `{count, plural, ...}` or `{gender, select, ...}` directly in your translation files.
- **Smart Regional Fallback**: Automatically degrades specific namespaces or files from a regional locale (e.g., `es_MX`) to a base locale (e.g., `es`).
  - *Problem Solved:* You want your app to use `es_MX` for currency formatting, but your vendor packages (like `laravel/ui` or `spatie/permission`) only publish translations in `es`. This package handles that automatically.
- **Seamless Integration**: Works as a drop-in replacement for `trans()`, `__()`, and `@lang`.
- **Native Performance**: Uses PHP's native `intl` extension and `MessageFormatter`.

---

## 📦 Installation

```bash
composer require gboquizosanchez/icu-i18n
```

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Boquizo\IcuI18n\I18nServiceProvider"
```

**Requirements:** PHP ^8.3 · ext-intl · Laravel 11 or 12

---

## 🔧 Configuration

The configuration file `config/icu.php` controls the **Regional Fallback Strategy**.

### How it works

When you request a translation (e.g., `__('validation.required')`) while your app locale is set to **Regional** (e.g., `es_MX`), the translator decides whether to use `es_MX` or fall back to `es` based on two rules:

1. **Namespace Allowlist**: If the translation namespace is **NOT** in the `namespaces` list, it falls back to the base language (`es`).
2. **File Blocklist**: If the translation file is in the `files` list, it forces the base language (`es`), even if the namespace is allowlisted.

### Default Configuration

```php
// config/icu.php

return [
    'regionals' => [
        /*
         * Allowlisted Namespaces.
         * These will use the full regional locale (e.g. es_MX).
         * '*' represents your application's local files (lang/es_MX/...).
         * Vendor packages are excluded by default to prevent missing translation errors.
         */
        'namespaces' => [
            '*',
        ],

        /*
         * Blocklisted Files.
         * These will ALWAYS force the base locale (e.g. es).
         * Useful for standard Laravel files that usually come in generic locale folders.
         */
        'files' => [
            // 'validation',
            // 'auth',
            // 'passwords',
        ],
    ],
];
```

---

## 📖 Usage

### 1. ICU MessageFormat

Use standard ICU syntax in your JSON or PHP translation files.

**`lang/en/messages.php`**
```php
return [
    'welcome' => 'Hello, {name}.',
    'balance' => 'Your balance is {amount, number, currency}',
    'apples'  => '{count, plural, =0{No apples} one{One apple} other{# apples}}',
    'gender'  => '{gender, select, male{Welcome, sir} female{Welcome, ma\'am} other{Welcome}}',
    'audit'   => '{user} performed an action on {date, date, long}',
];
```

**In your Blade views:**

```blade
{{-- Basic variable --}}
{{ __('messages.welcome', ['name' => 'John']) }}
{{-- Output: Hello, John. --}}

{{-- Automatic currency formatting (uses app locale) --}}
{{ __('messages.balance', ['amount' => 1250.50]) }}
{{-- Output (en_US): Your balance is $1,250.50 --}}
{{-- Output (es_ES): Tu saldo es 1.250,50 € --}}

{{-- Complex pluralization --}}
{{ __('messages.apples', ['count' => 1]) }}  {{-- One apple --}}
{{ __('messages.apples', ['count' => 5]) }}  {{-- 5 apples --}}
{{ __('messages.apples', ['count' => 0]) }}  {{-- No apples --}}

{{-- Gender / select --}}
{{ __('messages.gender', ['gender' => 'female']) }}
{{-- Output: Welcome, ma'am --}}
```

### 2. Advanced pluralization per language

ICU supports the pluralization rules of **every language** following the CLDR standard:

**Russian** — 3 plural forms
```php
// lang/ru/messages.php
'items' => 'Dimitri купил {count, plural, one{# товар} few{# товара} other{# товаров}}.',
```
```
count=1   → Dimitri купил 1 товар.
count=2   → Dimitri купил 2 товара.
count=5   → Dimitri купил 5 товаров.
count=105 → Dimitri купил 105 товаров.
```

**Arabic** — 6 forms + dual
```php
// lang/ar/messages.php
'products' => '{count, plural, =1{منتج واحد} =2{منتجان} few{# منتجات} many{# منتجاً} other{# منتج}}',
```

**Polish** — 4 plural forms
```php
// lang/pl/messages.php
'products' => '{count, plural, one{# produkt} few{# produkty} many{# produktów} other{# produktu}}',
```

**Slovenian** — 4 forms including dual
```php
// lang/sl/messages.php
'items' => '{count, plural, one{# izdelek} two{# izdelka} few{# izdelki} other{# izdelkov}}',
```

### 3. Handling Object Parameters

The translator automatically converts `DateTime` objects to timestamps and objects implementing `__toString()` to strings before passing them to the ICU formatter:

```php
$user = new User(['name' => 'Alice']); // implements __toString
$date = new DateTime('2023-10-01');

echo __('messages.audit', ['user' => $user, 'date' => $date]);
// Output (en_US): Alice performed an action on October 1, 2023
// Output (es_ES): Alice realizó una acción el 1 de octubre de 2023
// Output (ar_SA): أجرت Alice إجراءً في ١ أكتوبر ٢٠٢٣
```

---

## 🧩 Regional Fallback Examples

Assume `App::setLocale('es_MX')`.

#### Scenario A: Application Strings

You have a file `lang/es_MX/home.php`.

- **Config**: `namespaces => ['*']`
- **Call**: `__('home.title')`
- **Result**: Loads from `es_MX`. (Matches `*` allowlist ✅)

#### Scenario B: Vendor Package

You use a package `courier` that only has translations in `lang/vendor/courier/es/messages.php`.

- **Config**: `namespaces => ['*']` (does NOT include `courier`)
- **Call**: `__('courier::messages.error')`
- **Result**: Loads from `es`. (Namespace `courier` not allowlisted → degrades to base locale ✅)

#### Scenario C: Validation Files

You want to use standard Laravel validation messages which typically exist in `lang/es/validation.php`, not `es_MX`.

- **Config**: `files => ['validation']`
- **Call**: `__('validation.required')`
- **Result**: Loads from `es`. (File `validation` is blocklisted → forces base locale ✅)

---

## ICU Syntax Reference

| Type | Syntax | Example |
|---|---|---|
| Variable | `{variable}` | `{name}` |
| Number | `{var, number}` | `{count, number}` |
| Currency | `{var, number, currency}` | `{amount, number, currency}` |
| Short date | `{var, date, short}` | `{date, date, short}` |
| Long date | `{var, date, long}` | `{date, date, long}` |
| Plural | `{var, plural, one{} other{}}` | See examples above |
| Select | `{var, select, val1{} other{}}` | `{gender, select, male{} female{} other{}}` |
| `#` in plural | Replaced by the count value | `{count, plural, other{# items}}` |

---

## 🧪 Testing

```bash
composer test
```

This package uses [Pest v4](https://pestphp.com/) with architecture and Laravel plugins. Static analysis runs via [Larastan](https://github.com/larastan/larastan) at the maximum level.

### Troubleshooting

If you encounter issues:

1. **Check the logs** — Laravel logs may contain helpful error messages.
2. **Verify requirements** — Ensure PHP and Laravel versions meet the minimum requirements.
3. **Clear cache** — Run `php artisan config:clear` and `php artisan cache:clear`.
4. **Open an issue** — [Report bugs or request features](https://github.com/gboquizosanchez/icu-i18n/issues/new).

---

## Contributing

Contributions are welcome! Please feel free to:

- 🐛 **Report bugs** via [GitHub Issues](https://github.com/gboquizosanchez/icu-i18n/issues/new)
- 💡 **Suggest features** or improvements
- 🔧 **Submit pull requests** with bug fixes or enhancements
- 📖 **Improve documentation** or add language examples

Please make sure all tests pass and the code follows the style enforced by [Laravel Pint](https://laravel.com/docs/pint) before submitting a PR.

---

## Credits

- **Author**: [Germán Boquizo Sánchez](mailto:germanboquizosanchez@gmail.com)
- **Framework**: [Laravel](https://laravel.com/)
- **Standard**: [ICU MessageFormat](https://unicode-org.github.io/icu/userguide/format_parse/messages/)
- **Pluralization rules**: [CLDR Plural Rules](https://cldr.unicode.org/index/cldr-spec/plural-rules)
- **Contributors**: [View all contributors](../../contributors)

---

## 📄 License

This package is open-source software licensed under the [MIT License](LICENSE.md).

---

<div align="center">

Made with ❤️ for the PHP · Laravel · i18n community

</div>
