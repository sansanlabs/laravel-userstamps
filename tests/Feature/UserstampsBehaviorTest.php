<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use SanSanLabs\Userstamps\Tests\Models\Product;
use SanSanLabs\Userstamps\Tests\Models\UserWithId;
use SanSanLabs\Userstamps\Tests\Models\UserWithUlid;
use SanSanLabs\Userstamps\Tests\Models\UserWithUuid;

// Helper function to test userstamps behavior
function testUserstampsBehavior($john, $jane, $alice, $productName): void {
  test()->actingAs($john);

  $product = Product::create(["name" => $productName]);

  // Test creation
  expect($product->createdBy)->not()->toBeNull();
  expect($product->updatedBy)->not()->toBeNull();
  expect($product->deletedBy)->toBeNull();
  expect($product->createdBy->email)->toBe($john->email);
  expect($product->updatedBy->email)->toBe($john->email);

  // Test deletion
  test()->actingAs($jane);
  $product->delete();
  $product->refresh();

  expect($product->deletedBy)->not()->toBeNull();
  expect($product->updatedBy->email)->toBe($jane->email);
  expect($product->deletedBy->email)->toBe($jane->email);

  // Test restoration
  test()->actingAs($alice);
  $product->restore();
  $product->refresh();

  expect($product->deletedBy)->toBeNull();
  expect($product->updatedBy->email)->toBe($alice->email);

  // Test with trashed users
  Config::set("userstamps.with_trashed", true);
  $alice->delete();
  $alice->refresh();
  $product->refresh();

  expect($product->updatedBy->email)->toBe($alice->email);
}

// Tests with morph enabled
it("userstamps use id with morph enabled", function (): void {
  Config::set("userstamps.is_using_morph", true);

  Schema::create("user_with_ids", function (Blueprint $table): void {
    $table->id();
    $table->string("name");
    $table->string("email")->unique();
    $table->timestamps();
    $table->softDeletes();
  });

  Schema::create("products", function (Blueprint $table): void {
    $table->id();
    $table->string("name");
    $table->timestamps();
    $table->softDeletes();
    $table->userstamps();
    $table->softUserstamps();
  });

  $users = [
    UserWithId::create(["name" => "John Doe", "email" => "john@example.com"]),
    UserWithId::create(["name" => "Jane Doe", "email" => "jane@example.com"]),
    UserWithId::create(["name" => "Alice Doe", "email" => "alice@example.com"]),
  ];

  testUserstampsBehavior($users[0], $users[1], $users[2], "Test Product ID");
});

it("userstamps use ulid with morph enabled", function (): void {
  Config::set("userstamps.is_using_morph", true);
  Config::set("userstamps.users_table_id_column_type", "ulid");

  Schema::create("user_with_ulids", function (Blueprint $table): void {
    $table->ulid("id")->primary();
    $table->string("name");
    $table->string("email")->unique();
    $table->timestamps();
    $table->softDeletes();
  });

  Schema::create("products", function (Blueprint $table): void {
    $table->id();
    $table->string("name");
    $table->timestamps();
    $table->softDeletes();
    $table->userstamps();
    $table->softUserstamps();
  });

  $users = [
    UserWithUlid::create(["name" => "John Doe", "email" => "john@example.com"]),
    UserWithUlid::create(["name" => "Jane Doe", "email" => "jane@example.com"]),
    UserWithUlid::create(["name" => "Alice Doe", "email" => "alice@example.com"]),
  ];

  testUserstampsBehavior($users[0], $users[1], $users[2], "Test Product ULID");
});

it("userstamps use uuid with morph enabled", function (): void {
  Config::set("userstamps.is_using_morph", true);
  Config::set("userstamps.users_table_id_column_type", "uuid");

  Schema::create("user_with_uuids", function (Blueprint $table): void {
    $table->uuid("id")->primary();
    $table->string("name");
    $table->string("email")->unique();
    $table->timestamps();
    $table->softDeletes();
  });

  Schema::create("products", function (Blueprint $table): void {
    $table->id();
    $table->string("name");
    $table->timestamps();
    $table->softDeletes();
    $table->userstamps();
    $table->softUserstamps();
  });

  $users = [
    UserWithUuid::create(["name" => "John Doe", "email" => "john@example.com"]),
    UserWithUuid::create(["name" => "Jane Doe", "email" => "jane@example.com"]),
    UserWithUuid::create(["name" => "Alice Doe", "email" => "alice@example.com"]),
  ];

  testUserstampsBehavior($users[0], $users[1], $users[2], "Test Product UUID");
});

// Tests with morph disabled
it("userstamps use id with morph disabled", function (): void {
  Config::set("userstamps.is_using_morph", false);
  Config::set("auth.providers.users.model", UserWithId::class);

  Schema::create("user_with_ids", function (Blueprint $table): void {
    $table->id();
    $table->string("name");
    $table->string("email")->unique();
    $table->timestamps();
    $table->softDeletes();
  });

  Schema::create("products", function (Blueprint $table): void {
    $table->id();
    $table->string("name");
    $table->timestamps();
    $table->softDeletes();
    $table->userstamps();
    $table->softUserstamps();
  });

  $users = [
    UserWithId::create(["name" => "John Doe", "email" => "john@example.com"]),
    UserWithId::create(["name" => "Jane Doe", "email" => "jane@example.com"]),
    UserWithId::create(["name" => "Alice Doe", "email" => "alice@example.com"]),
  ];

  testUserstampsBehavior($users[0], $users[1], $users[2], "Test Product ID Non-Morph");
});

it("userstamps use ulid with morph disabled", function (): void {
  Config::set("userstamps.is_using_morph", false);
  Config::set("userstamps.users_table_id_column_type", "ulid");
  Config::set("auth.providers.users.model", UserWithUlid::class);

  Schema::create("user_with_ulids", function (Blueprint $table): void {
    $table->ulid("id")->primary();
    $table->string("name");
    $table->string("email")->unique();
    $table->timestamps();
    $table->softDeletes();
  });

  Schema::create("products", function (Blueprint $table): void {
    $table->id();
    $table->string("name");
    $table->timestamps();
    $table->softDeletes();
    $table->userstamps();
    $table->softUserstamps();
  });

  $users = [
    UserWithUlid::create(["name" => "John Doe", "email" => "john@example.com"]),
    UserWithUlid::create(["name" => "Jane Doe", "email" => "jane@example.com"]),
    UserWithUlid::create(["name" => "Alice Doe", "email" => "alice@example.com"]),
  ];

  testUserstampsBehavior($users[0], $users[1], $users[2], "Test Product ULID Non-Morph");
});

it("userstamps use uuid with morph disabled", function (): void {
  Config::set("userstamps.is_using_morph", false);
  Config::set("userstamps.users_table_id_column_type", "uuid");
  Config::set("auth.providers.users.model", UserWithUuid::class);

  Schema::create("user_with_uuids", function (Blueprint $table): void {
    $table->uuid("id")->primary();
    $table->string("name");
    $table->string("email")->unique();
    $table->timestamps();
    $table->softDeletes();
  });

  Schema::create("products", function (Blueprint $table): void {
    $table->id();
    $table->string("name");
    $table->timestamps();
    $table->softDeletes();
    $table->userstamps();
    $table->softUserstamps();
  });

  $users = [
    UserWithUuid::create(["name" => "John Doe", "email" => "john@example.com"]),
    UserWithUuid::create(["name" => "Jane Doe", "email" => "jane@example.com"]),
    UserWithUuid::create(["name" => "Alice Doe", "email" => "alice@example.com"]),
  ];

  testUserstampsBehavior($users[0], $users[1], $users[2], "Test Product UUID Non-Morph");
});
