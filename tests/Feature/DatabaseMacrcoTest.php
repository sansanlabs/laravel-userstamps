<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;

it("add column userstamps with morph enabled", function (): void {
  Config::set("userstamps.is_using_morph", true);
  testUserstampsWorkflow(true);
});

it("add column userstamps with morph disabled", function (): void {
  Config::set("userstamps.is_using_morph", false);
  testUserstampsWorkflow(false);
});

it("add custom column userstamps with morph enabled", function (): void {
  Config::set("userstamps.is_using_morph", true);
  Config::set("userstamps.created_by_column", "creator");
  Config::set("userstamps.updated_by_column", "editor");
  Config::set("userstamps.deleted_by_column", "destroyer");

  testUserstampsWorkflow(true);
});

it("add custom column userstamps with morph disabled", function (): void {
  Config::set("userstamps.is_using_morph", false);
  Config::set("userstamps.created_by_column", "creator");
  Config::set("userstamps.updated_by_column", "editor");
  Config::set("userstamps.deleted_by_column", "destroyer");

  testUserstampsWorkflow(false);
});

function testUserstampsWorkflow(bool $isMorph): void {
  $columns = getColumnNames();

  // Test userstamps creation
  createTableWithUserstamps();
  assertUserstampsExist($columns["created"], $columns["updated"], $isMorph);

  // Test soft userstamps
  addSoftUserstamps();
  assertSoftUserstampsExist($columns["deleted"], $isMorph);

  // Test userstamps removal
  dropUserstamps();
  assertUserstampsNotExist($columns["created"], $columns["updated"], $isMorph);

  // Test soft userstamps removal
  dropSoftUserstamps();
  assertSoftUserstampsNotExist($columns["deleted"], $isMorph);
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

function assertUserstampsExist(string $createdColumn, string $updatedColumn, bool $isMorph): void {
  $columns = Schema::getColumnlisting("products");

  // ID columns should always exist
  expect($columns)->toContain("{$createdColumn}_id");
  expect($columns)->toContain("{$updatedColumn}_id");

  // Type columns only exist if morph is enabled
  if ($isMorph) {
    expect($columns)->toContain("{$createdColumn}_type");
    expect($columns)->toContain("{$updatedColumn}_type");
  } else {
    expect($columns)->not->toContain("{$createdColumn}_type");
    expect($columns)->not->toContain("{$updatedColumn}_type");
  }
}

function assertSoftUserstampsExist(string $deletedColumn, bool $isMorph): void {
  $columns = Schema::getColumnlisting("products");

  // ID column should always exist
  expect($columns)->toContain("{$deletedColumn}_id");

  // Type column only exists if morph is enabled
  if ($isMorph) {
    expect($columns)->toContain("{$deletedColumn}_type");
  } else {
    expect($columns)->not->toContain("{$deletedColumn}_type");
  }
}

function assertUserstampsNotExist(string $createdColumn, string $updatedColumn, bool $isMorph): void {
  $columns = Schema::getColumnlisting("products");

  // ID columns should not exist
  expect($columns)->not->toContain("{$createdColumn}_id");
  expect($columns)->not->toContain("{$updatedColumn}_id");

  // Type columns should also not exist regardless of morph setting
  expect($columns)->not->toContain("{$createdColumn}_type");
  expect($columns)->not->toContain("{$updatedColumn}_type");
}

function assertSoftUserstampsNotExist(string $deletedColumn, bool $isMorph): void {
  $columns = Schema::getColumnlisting("products");

  // ID column should not exist
  expect($columns)->not->toContain("{$deletedColumn}_id");

  // Type column should also not exist regardless of morph setting
  expect($columns)->not->toContain("{$deletedColumn}_type");
}
