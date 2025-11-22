<?php

declare(strict_types=1);

namespace Boquizo\I18n;

use Illuminate\Translation\Translator;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class I18nServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('icu-i18n')
            ->hasConfigFile('icu');
    }

    public function registeringPackage(): void
    {
        $this->app->extend('translator', function (Translator $service) {
            $trans = new IcuTranslator(
                $service->getLoader(),
                $service->getLocale(),
            );

            $trans->setFallback($service->getFallback());

            return $trans;
        });
    }
}
