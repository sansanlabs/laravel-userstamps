<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use SanSanLabs\Userstamps\Tests\Models\Product;
use SanSanLabs\Userstamps\Tests\Models\UserWithId;
use SanSanLabs\Userstamps\Tests\Models\UserWithUlid;
use SanSanLabs\Userstamps\Tests\Models\UserWithUuid;

beforeEach(function (): void {
  Schema::create("products", function (Blueprint $table): void {
    $table->id();
    $table->string("name");
    $table->timestamps();
    $table->softDeletes();
    $table->userstamps();
    $table->softUserstamps();
  });
});

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

it("userstamps use id", function (): void {
  Schema::create("user_with_ids", function (Blueprint $table): void {
    $table->id();
    $table->string("name");
    $table->string("email")->unique();
    $table->timestamps();
    $table->softDeletes();
  });

  $users = [
    UserWithId::create(["name" => "John Doe", "email" => "john@example.com"]),
    UserWithId::create(["name" => "Jane Doe", "email" => "jane@example.com"]),
    UserWithId::create(["name" => "Alice Doe", "email" => "alice@example.com"]),
  ];

  testUserstampsBehavior($users[0], $users[1], $users[2], "Test Product ID");
});

it("userstamps use ulid", function (): void {
  Config::set("userstamps.users_table_id_column_type", "ulid");

  Schema::create("user_with_ulids", function (Blueprint $table): void {
    $table->ulid("id")->primary();
    $table->string("name");
    $table->string("email")->unique();
    $table->timestamps();
    $table->softDeletes();
  });

  $users = [
    UserWithUlid::create(["name" => "John Doe", "email" => "john@example.com"]),
    UserWithUlid::create(["name" => "Jane Doe", "email" => "jane@example.com"]),
    UserWithUlid::create(["name" => "Alice Doe", "email" => "alice@example.com"]),
  ];

  testUserstampsBehavior($users[0], $users[1], $users[2], "Test Product ULID");
});

it("userstamps use uuid", function (): void {
  Config::set("userstamps.users_table_id_column_type", "uuid");

  Schema::create("user_with_uuids", function (Blueprint $table): void {
    $table->uuid("id")->primary();
    $table->string("name");
    $table->string("email")->unique();
    $table->timestamps();
    $table->softDeletes();
  });

  $users = [
    UserWithUuid::create(["name" => "John Doe", "email" => "john@example.com"]),
    UserWithUuid::create(["name" => "Jane Doe", "email" => "jane@example.com"]),
    UserWithUuid::create(["name" => "Alice Doe", "email" => "alice@example.com"]),
  ];

  testUserstampsBehavior($users[0], $users[1], $users[2], "Test Product UUID");
});
