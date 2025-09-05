<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;

it("add column userstamps", function (): void {
  testUserstampsWorkflow();
});

it("add custom column userstamps", function (): void {
  Config::set("userstamps.created_by_column", "creator");
  Config::set("userstamps.updated_by_column", "editor");
  Config::set("userstamps.deleted_by_column", "destroyer");

  testUserstampsWorkflow();
});

function testUserstampsWorkflow(): void {
  $columns = getColumnNames();

  // Test userstamps creation
  createTableWithUserstamps();
  assertUserstampsExist($columns["created"], $columns["updated"]);

  // Test soft userstamps
  addSoftUserstamps();
  assertSoftUserstampsExist($columns["deleted"]);

  // Test userstamps removal
  dropUserstamps();
  assertUserstampsNotExist($columns["created"], $columns["updated"]);

  // Test soft userstamps removal
  dropSoftUserstamps();
  assertSoftUserstampsNotExist($columns["deleted"]);
}

function getColumnNames(): array {
  return [
    "created" => Config::get("userstamps.created_by_column"),
    "updated" => Config::get("userstamps.updated_by_column"),
    "deleted" => Config::get("userstamps.deleted_by_column"),
  ];
}

function createTableWithUserstamps(): void {
  Schema::create("products", function (Blueprint $table): void {
    $table->increments("id");
    $table->userstamps();
  });
}

function addSoftUserstamps(): void {
  Schema::table("products", function (Blueprint $table): void {
    $table->softUserstamps();
  });
}

function dropUserstamps(): void {
  Schema::table("products", function (Blueprint $table): void {
    $table->dropUserstamps();
  });
}

function dropSoftUserstamps(): void {
  Schema::table("products", function (Blueprint $table): void {
    $table->dropSoftUserstamps();
  });
}

function assertUserstampsExist(string $createdColumn, string $updatedColumn): void {
  $columns = Schema::getColumnlisting("products");
  expect($columns)->toContain("{$createdColumn}_id", "{$createdColumn}_type");
  expect($columns)->toContain("{$updatedColumn}_id", "{$updatedColumn}_type");
}

function assertSoftUserstampsExist(string $deletedColumn): void {
  $columns = Schema::getColumnlisting("products");
  expect($columns)->toContain("{$deletedColumn}_id", "{$deletedColumn}_type");
}

function assertUserstampsNotExist(string $createdColumn, string $updatedColumn): void {
  $columns = Schema::getColumnlisting("products");
  expect($columns)->not->toContain("{$createdColumn}_id", "{$createdColumn}_type");
  expect($columns)->not->toContain("{$updatedColumn}_id", "{$updatedColumn}_type");
}

function assertSoftUserstampsNotExist(string $deletedColumn): void {
  $columns = Schema::getColumnlisting("products");
  expect($columns)->not->toContain("{$deletedColumn}_id", "{$deletedColumn}_type");
}
