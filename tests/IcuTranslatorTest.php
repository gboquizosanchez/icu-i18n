<?php

declare(strict_types=1);

beforeEach(function (): void {
    Config::set('app.fallback_locale', 'en');

    App::setLocale('es_ES');
});

it('finds exact translation in es_ES and formats in es_ES', function (): void {
    Lang::addLines(['test.exact' => 'Exacto: {n, number}'], 'es_ES');

    expect(__('test.exact', ['n' => 1234.56]))
        ->toBe('Exacto: 1.234,56');
});

it('falls back to global en, BUT maintains requested es_ES formatting', function (): void {
    Lang::addLines(['test.fallback' => 'In English: {n, number}'], 'en');

    App::setLocale('es_ES');

    expect(__('test.fallback', ['n' => 1234.56]))
        ->toBe('In English: 1.234,56');
});

it('handles complex ICU plurals correctly', function (): void {
    Lang::addLines([
        'test.apples' => '{n, plural, =0{No hay manzanas} one{1 manzana} other{# manzanas}}',
    ], 'es_ES');

    expect(__('test.apples', ['n' => 0]))->toBe('No hay manzanas')
        ->and(__('test.apples', ['n' => 1]))->toBe('1 manzana')
        ->and(__('test.apples', ['n' => 5]))->toBe('5 manzanas');
});

it('handles complex ICU date formatting correctly', function (): void {
    Lang::addLines([
        'test.date' => 'Fecha: {n, date, long}',
    ], 'es_ES');

    $date = '2025-11-22 02:30:00';

    expect(__('test.date', ['n' => strtotime($date)]))->toBe('Fecha: 22 de noviembre de 2025');
});

it('handles complex ICU languages formatting correctly', function (): void {
    App::setLocale('ru_RU');

    Lang::addLines([
        'test.language' => '{name} {gender, select, male{купил} female{купила} other{купил(а)}} {count, plural, one{# товар} few{# товара} many{# товаров} other{# товара}}.',
    ], 'ru_RU');

    $male = [
        'name' => 'Иван',
        'gender' => 'male',
        'count' => 1,
    ];

    $female = [
        'name' => 'Павлова',
        'gender' => 'female',
        'count' => 2,
    ];

    $other = [
        'name' => 'John',
        'gender' => 'other',
        'count' => 5,
    ];

    expect(__('test.language', $male))->toBe('Иван купил 1 товар.')
        ->and(__('test.language', $female))->toBe('Павлова купила 2 товара.')
        ->and(__('test.language', $other))->toBe('John купил(а) 5 товаров.');
});

it('handles nested select and plural correctly', function (): void {
    App::setLocale('ru_RU');

    Lang::addLines([
        'test.nested' => '{gender, select, male{{count, plural, one{# яблоко} few{# яблока} many{# яблок} other{# яблока}}} female{{count, plural, one{# яблоко} few{# яблока} many{# яблок} other{#яблока}}} other{{count, plural, one{# яблоко} few{# яблока} many{# яблок} other{# яблока}}}}',
    ], 'ru_RU');

    expect(__('test.nested', ['gender' => 'male', 'count' => 1]))->toBe('1 яблоко')
        ->and(__('test.nested', ['gender' => 'male', 'count' => 3]))->toBe('3 яблока')
        ->and(__('test.nested', ['gender' => 'female', 'count' => 5]))->toBe('5 яблок')
        ->and(__('test.nested', ['gender' => 'other', 'count' => 2]))->toBe('2 яблока');
});

it('handles ICU plural, select and numbers correctly for Bengali', function (): void {
    App::setLocale('bn_BD');

    Lang::addLines([
        'test.bengali' => '{gender, select, male{{count, plural, one{{count, number} পণ্য কিনেছে} other{{count, number} পণ্য কিনেছে}}} female{{count, plural, one{{count, number} পণ্য কিনেছে} other{{count, number} পণ্য কিনেছে}}} other{{count, plural, one{{count, number} পণ্য কিনেছে} other{{count, number} পণ্য কিনেছে}}}}',
    ], 'bn_BD');

    $maleOne = ['gender' => 'male', 'count' => 1];
    $maleMany = ['gender' => 'male', 'count' => 5];
    $femaleOne = ['gender' => 'female', 'count' => 1];
    $otherTwo = ['gender' => 'other', 'count' => 2];

    expect(__('test.bengali', $maleOne))->toBe('১ পণ্য কিনেছে') // ১ → 1
        ->and(__('test.bengali', $maleMany))->toBe('৫ পণ্য কিনেছে') // ৫ → 5
        ->and(__('test.bengali', $femaleOne))->toBe('১ পণ্য কিনেছে') // ১ → 1
        ->and(__('test.bengali', $otherTwo))->toBe('২ পণ্য কিনেছে'); // ২ → 2
});

it('handles ICU plural, select and numbers correctly for Arabic', function (): void {
    App::setLocale('ar_SA');

    Lang::addLines([
        'test.arabic' => '{gender, select, male{{count, plural, zero{لم يشتر أي منتج} one{# منتج} two{# منتجان} few{# منتجات} many{# منتجًا} other{# منتج}}} female{{count, plural, zero{لم تشتر أي منتج} one{# منتج} two{# منتجان} few{# منتجات} many{# منتجًا} other{# منتج}}} other{{count, plural, zero{لم يشتر أي منتج} one{# منتج} two{# منتجان} few{# منتجات} many{# منتجًا} other{# منتج}}}}',
    ], 'ar_SA');

    $maleZero = ['gender' => 'male', 'count' => 0];
    $maleOne = ['gender' => 'male', 'count' => 1];
    $maleTwo = ['gender' => 'male', 'count' => 2];
    $maleFew = ['gender' => 'male', 'count' => 3];
    $maleMany = ['gender' => 'male', 'count' => 11];

    expect(__('test.arabic', $maleZero))->toBe('لم يشتر أي منتج')
        ->and(__('test.arabic', $maleOne))->toBe('١ منتج')
        ->and(__('test.arabic', $maleTwo))->toBe('٢ منتجان')
        ->and(__('test.arabic', $maleFew))->toBe('٣ منتجات')
        ->and(__('test.arabic', $maleMany))->toBe('١١ منتجًا');
});

it('handles plural ranges correctly', function (): void {
    App::setLocale('es_ES');

    Lang::addLines([
        'test.plural_ranges' => '{n, plural, =0{No hay elementos} one{Solo 1 elemento} other{Muchos elementos}}',
    ], 'es_ES');

    expect(__('test.plural_ranges', ['n' => 0]))->toBe('No hay elementos')
        ->and(__('test.plural_ranges', ['n' => 1]))->toBe('Solo 1 elemento')
        ->and(__('test.plural_ranges', ['n' => 3]))->toBe('Muchos elementos')
        ->and(__('test.plural_ranges', ['n' => 10]))->toBe('Muchos elementos');
});

it('formats currency correctly using the region from the requested locale', function (): void {
    Lang::addLines(['test.price' => 'Price: {n, number, currency}'], 'en');

    App::setLocale('es_ES');

    expect(__('test.price', ['n' => 50]))
        ->toContain('50,00')
        ->toContain('€');
});

it('formats currency as Mexican Pesos if locale is es_MX', function (): void {
    Lang::addLines(['test.price' => 'Price: {n, number, currency}'], 'en');

    App::setLocale('es_MX');

    expect(__('test.price', ['n' => 50]))
        ->toContain('50.00')
        ->toContain('$');
});

it('formats currency as US Dollars if locale is en_US', function (): void {
    Lang::addLines(['test.price' => 'Price: {n, number, currency}'], 'en');

    App::setLocale('en_US');

    expect(__('test.price', ['n' => 50]))
        ->toContain('$50.00')
        ->toContain('$');
});

it('handles percent formatting correctly', function (): void {
    Lang::addLines([
        'test.percent' => 'Progreso: {n, number, percent}',
    ], 'es_ES');

    expect(__('test.percent', ['n' => 0.75]))->toBe('Progreso: 75 %');
});

it('escapes special characters correctly', function (): void {
    App::setLocale('es_ES');

    Lang::addLines([
        'test.escaped' => "Muestra de llaves: '{' y '}' y comillas simples: ''",
    ], 'es_ES');

    expect(__('test.escaped'))->toBe("Muestra de llaves: { y } y comillas simples: '");
});
