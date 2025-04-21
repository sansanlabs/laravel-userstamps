# Laravel Userstamps

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sansanlabs/laravel-userstamps.svg?style=flat-square)](https://packagist.org/packages/sansanlabs/laravel-userstamps)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/sansanlabs/laravel-userstamps/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/sansanlabs/laravel-userstamps/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/sansanlabs/laravel-userstamps/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/sansanlabs/laravel-userstamps/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/sansanlabs/laravel-userstamps.svg?style=flat-square)](https://packagist.org/packages/sansanlabs/laravel-userstamps)

## Installation

You can install the package via composer:

```bash
composer require sansanlabs/laravel-userstamps
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="userstamps-config"
```

This is the contents of the published config file:

```php
return [
  /*
   * Specify the column type used in the schema for the account ID
   *
   * Options: increments, bigIncrements, uuid, ulid
   * Default: bigIncrements
   */
  "users_table_id_column_type" => "bigIncrements",

  /*
   * Specify the column name used as the foreign key reference for the account ID. Make sure all user tables have an ID column name that matches the one you define below.
   */
  "users_table_id_column_name" => "id",

  /*
   * Specify the column that stores the ID of the user who initially created the entry
   */
  "created_by_column" => "created_by",

  /*
   * Specify the column that holds the ID of the user who last updated the entry
   */
  "updated_by_column" => "updated_by",

  /*
   * Specify the column that contains the ID of the user who removed the entry
   */
  "deleted_by_column" => "deleted_by",

  /*
   * Indicate whether to include soft-deleted records in queries
   */
  "with_trashed" => false,
];
```

## Usage

Add the macro to your migration of your model

```php
public function up(){
    Schema::create('table_name', function (Blueprint $table) {
        ...
        $table->userstamps();
        $table->softUserstamps();
    });
}
```

Add the macro to your existing table

```php
public function up()
{
    Schema::table('table_name', function($table) {
        ...
        $table->userstamps();
        $table->softUserstamps();
    });
}


public function down()
{
    Schema::table('table_name', function($table) {
        $table->dropUserstamps();
        $table->dropSoftUserstamps();
    });
}
```

Add the Trait to your model

```php
use SanSanLabs\UserStamps\Concerns\HasUserstamps;

class Example extends Model {
  use HasUserstamps;
}
```

There will be methods available to retrieve the user object which performs the action for creating, updating or deleting

```php
$model->creator; // the user who created the model
$model->editor; // the user who last updated the model
$model->destroyer; // the user who deleted the model
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

- [Edi Kurniawan](https://github.com/edikurniawan-dev)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
