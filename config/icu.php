<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Regional Locale Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration determines which resources should be loaded using the
    | full regional locale (e.g., 'en_US', 'es_ES') and which should fall back
    | to the base language locale (e.g., 'en', 'es').
    |
    | By default, usually only the application's own strings ('*') are localized
    | with specific regions, while vendor packages often rely on generic locales.
    |
    */

    'regionals' => [

        /*
        |-----------------------------------------------------------------------
        | Allowed list Namespaces (Use Regional Locale)
        |-----------------------------------------------------------------------
        |
        | List the translation namespaces that support and should use the full
        | regional locale (e.g., 'en_GB').
        |
        | - Use '*' to include your application's main translation files.
        | - Add specific vendor namespaces (e.g., 'courier', 'spark') if they
        | have regional translation files available.
        |
        | Any namespace NOT listed here will automatically fall back to the
        | base language (e.g., 'en_GB' -> 'en').
        |
        */
        'namespaces' => [
            '*',
        ],

        /*
        |-----------------------------------------------------------------------
        | Excluded Files (Force Base Locale)
        |-----------------------------------------------------------------------
        |
        | List specific filenames that should ALWAYS use the base language locale,
        | even if their namespace is allowed above.
        |
        | This is useful for files like 'validation' or 'auth' where you might
        | rely on standard Laravel translations that usually come in generic
        | folders (lang/es/validation.php) instead of regional ones.
        |
        */
        'files' => [
            // 'validation',
            // 'auth',
        ],
    ],
];
