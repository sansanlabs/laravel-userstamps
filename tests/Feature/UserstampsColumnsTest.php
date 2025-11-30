<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

afterEach(function (): void {
    Schema::dropIfExists('test_products');
});

it('creates only id columns when morph is disabled', function (): void {
    Config::set('userstamps.is_using_morph', false);

    Schema::create('test_products', function (Blueprint $table): void {
        $table->id();
        $table->userstamps();
        $table->softUserstamps();
    });

    $columns = Schema::getColumnListing('test_products');

    // Should have ID columns
    expect($columns)->toContain('created_by_id');
    expect($columns)->toContain('updated_by_id');
    expect($columns)->toContain('deleted_by_id');

    // Should NOT have type columns
    expect($columns)->not->toContain('created_by_type');
    expect($columns)->not->toContain('updated_by_type');
    expect($columns)->not->toContain('deleted_by_type');
});

it('creates both id and type columns when morph is enabled', function (): void {
    Config::set('userstamps.is_using_morph', true);

    Schema::create('test_products', function (Blueprint $table): void {
        $table->id();
        $table->userstamps();
        $table->softUserstamps();
    });

    $columns = Schema::getColumnListing('test_products');

    // Should have ID columns
    expect($columns)->toContain('created_by_id');
    expect($columns)->toContain('updated_by_id');
    expect($columns)->toContain('deleted_by_id');

    // Should have type columns
    expect($columns)->toContain('created_by_type');
    expect($columns)->toContain('updated_by_type');
    expect($columns)->toContain('deleted_by_type');
});

it('creates custom named columns correctly with morph disabled', function (): void {
    Config::set('userstamps.is_using_morph', false);
    Config::set('userstamps.created_by_column', 'author');
    Config::set('userstamps.updated_by_column', 'modifier');
    Config::set('userstamps.deleted_by_column', 'remover');

    Schema::create('test_products', function (Blueprint $table): void {
        $table->id();
        $table->userstamps();
        $table->softUserstamps();
    });

    $columns = Schema::getColumnListing('test_products');

    // Should have custom ID columns
    expect($columns)->toContain('author_id');
    expect($columns)->toContain('modifier_id');
    expect($columns)->toContain('remover_id');

    // Should NOT have type columns
    expect($columns)->not->toContain('author_type');
    expect($columns)->not->toContain('modifier_type');
    expect($columns)->not->toContain('remover_type');
});

it('creates custom named columns correctly with morph enabled', function (): void {
    Config::set('userstamps.is_using_morph', true);
    Config::set('userstamps.created_by_column', 'author');
    Config::set('userstamps.updated_by_column', 'modifier');
    Config::set('userstamps.deleted_by_column', 'remover');

    Schema::create('test_products', function (Blueprint $table): void {
        $table->id();
        $table->userstamps();
        $table->softUserstamps();
    });

    $columns = Schema::getColumnListing('test_products');

    // Should have custom ID columns
    expect($columns)->toContain('author_id');
    expect($columns)->toContain('modifier_id');
    expect($columns)->toContain('remover_id');

    // Should have custom type columns
    expect($columns)->toContain('author_type');
    expect($columns)->toContain('modifier_type');
    expect($columns)->toContain('remover_type');
});

it('drops only id columns when morph is disabled', function (): void {
    Config::set('userstamps.is_using_morph', false);

    Schema::create('test_products', function (Blueprint $table): void {
        $table->id();
        $table->userstamps();
        $table->softUserstamps();
    });

    Schema::table('test_products', function (Blueprint $table): void {
        $table->dropUserstamps();
        $table->dropSoftUserstamps();
    });

    $columns = Schema::getColumnListing('test_products');

    // Should NOT have any userstamps columns
    expect($columns)->not->toContain('created_by_id');
    expect($columns)->not->toContain('updated_by_id');
    expect($columns)->not->toContain('deleted_by_id');
    expect($columns)->not->toContain('created_by_type');
    expect($columns)->not->toContain('updated_by_type');
    expect($columns)->not->toContain('deleted_by_type');
});

it('drops both id and type columns when morph is enabled', function (): void {
    Config::set('userstamps.is_using_morph', true);

    Schema::create('test_products', function (Blueprint $table): void {
        $table->id();
        $table->userstamps();
        $table->softUserstamps();
    });

    Schema::table('test_products', function (Blueprint $table): void {
        $table->dropUserstamps();
        $table->dropSoftUserstamps();
    });

    $columns = Schema::getColumnListing('test_products');

    // Should NOT have any userstamps columns
    expect($columns)->not->toContain('created_by_id');
    expect($columns)->not->toContain('updated_by_id');
    expect($columns)->not->toContain('deleted_by_id');
    expect($columns)->not->toContain('created_by_type');
    expect($columns)->not->toContain('updated_by_type');
    expect($columns)->not->toContain('deleted_by_type');
});
