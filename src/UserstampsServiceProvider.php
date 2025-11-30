<?php

namespace SanSanLabs\Userstamps;

use SanSanLabs\Userstamps\Database\Schema\Macros\UserstampsMacro;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class UserstampsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name('laravel-userstamps')->hasConfigFile('userstamps');
    }

    public function bootingPackage(): void
    {
        (new UserstampsMacro)->register();
    }
}
