<?php

namespace SanSanLabs\Userstamps\Database\Schema\Macros;

use Illuminate\Database\Schema\Blueprint;

class UserstampsMacro implements MacroInterface {
  public function register(): void {
    $this->registerUserstamps();
    $this->registerSoftUserstamps();
    $this->registerDropUserstamps();
    $this->registerDropSoftUserstamps();
  }

  public function addUserIdColumn(Blueprint $table, string $column): void {
    $isMorph = config("userstamps.is_using_morph");
    $usersIdColumn = config("userstamps.users_table_id_column_name");
    $type = config("userstamps.users_table_id_column_type");
    $usersTable = config("userstamps.users_table");

    match ($type) {
      "bigIncrements" => $isMorph
        ? $table->unsignedBigInteger($column)->nullable()
        : $table->foreignId($column)->nullable()->constrained(table: $usersTable, column: $usersIdColumn)->nullOnDelete(),

      "uuid" => $isMorph
        ? $table->uuid($column)->nullable()
        : $table->foreignUuid($column)->nullable()->constrained(table: $usersTable, column: $usersIdColumn)->nullOnDelete(),

      "ulid" => $isMorph
        ? $table->ulid($column)->nullable()
        : $table->foreignUlid($column)->nullable()->constrained(table: $usersTable, column: $usersIdColumn)->nullOnDelete(),

      default => $isMorph
        ? $table->unsignedInteger($column)->nullable()
        : (function () use ($table, $column, $usersTable, $usersIdColumn): void {
          $table->unsignedInteger($column)->nullable();
          $table->foreign($column)->references($usersIdColumn)->on($usersTable)->nullOnDelete();
        })(),
    };
  }

  private function registerUserstamps(): void {
    Blueprint::macro("userstamps", function (): void {
      $createdByColumn = config("userstamps.created_by_column") . "_id";
      $updatedByColumn = config("userstamps.updated_by_column") . "_id";

      app(UserstampsMacro::class)->addUserIdColumn($this, $createdByColumn);

      if (config("userstamps.is_using_morph")) {
        $this->string(config("userstamps.created_by_column") . "_type")->nullable();
      }

      app(UserstampsMacro::class)->addUserIdColumn($this, $updatedByColumn);

      if (config("userstamps.is_using_morph")) {
        $this->string(config("userstamps.updated_by_column") . "_type")->nullable();
      }
    });
  }

  private function registerSoftUserstamps(): void {
    Blueprint::macro("softUserstamps", function (): void {
      $deletedByColumn = config("userstamps.deleted_by_column") . "_id";

      app(UserstampsMacro::class)->addUserIdColumn($this, $deletedByColumn);

      if (config("userstamps.is_using_morph")) {
        $this->string(config("userstamps.deleted_by_column") . "_type")->nullable();
      }
    });
  }

  private function registerDropUserstamps(): void {
    Blueprint::macro("dropUserstamps", function (): void {
      $createdByColumn = config("userstamps.created_by_column") . "_id";
      $updatedByColumn = config("userstamps.updated_by_column") . "_id";
      $columns = [$createdByColumn, $updatedByColumn];

      if (!config("userstamps.is_using_morph")) {
        $this->dropForeign([$createdByColumn]);
        $this->dropForeign([$updatedByColumn]);
      }

      if (config("userstamps.is_using_morph")) {
        $columns[] = config("userstamps.created_by_column") . "_type";
        $columns[] = config("userstamps.updated_by_column") . "_type";
      }

      $this->dropColumn($columns);
    });
  }

  private function registerDropSoftUserstamps(): void {
    Blueprint::macro("dropSoftUserstamps", function (): void {
      $deletedByColumn = config("userstamps.deleted_by_column") . "_id";
      $columns = [$deletedByColumn];

      if (!config("userstamps.is_using_morph")) {
        $this->dropForeign([$deletedByColumn]);
      }

      if (config("userstamps.is_using_morph")) {
        $columns[] = config("userstamps.deleted_by_column") . "_type";
      }

      $this->dropColumn($columns);
    });
  }
}
