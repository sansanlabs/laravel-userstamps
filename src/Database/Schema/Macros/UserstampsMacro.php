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
    $type = config("userstamps.users_table_id_column_type");

    match ($type) {
      "uuid" => $table->uuid($column)->nullable(),
      "ulid" => $table->ulid($column)->nullable(),
      "bigIncrements" => $table->unsignedBigInteger($column)->nullable(),
      default => $table->unsignedInteger($column)->nullable(),
    };
  }

  private function registerUserstamps(): void {
    Blueprint::macro("userstamps", function (): void {
      app(UserstampsMacro::class)->addUserIdColumn($this, config("userstamps.created_by_column") . "_id");
      $this->string(config("userstamps.created_by_column") . "_type")->nullable();

      app(UserstampsMacro::class)->addUserIdColumn($this, config("userstamps.updated_by_column") . "_id");
      $this->string(config("userstamps.updated_by_column") . "_type")->nullable();
    });
  }

  private function registerSoftUserstamps(): void {
    Blueprint::macro("softUserstamps", function (): void {
      app(UserstampsMacro::class)->addUserIdColumn($this, config("userstamps.deleted_by_column") . "_id");
      $this->string(config("userstamps.deleted_by_column") . "_type")->nullable();
    });
  }

  private function registerDropUserstamps(): void {
    Blueprint::macro("dropUserstamps", function (): void {
      $this->dropColumn([
        config("userstamps.created_by_column") . "_id",
        config("userstamps.created_by_column") . "_type",
        config("userstamps.updated_by_column") . "_id",
        config("userstamps.updated_by_column") . "_type",
      ]);
    });
  }

  private function registerDropSoftUserstamps(): void {
    Blueprint::macro("dropSoftUserstamps", function (): void {
      $this->dropColumn([config("userstamps.deleted_by_column") . "_id", config("userstamps.deleted_by_column") . "_type"]);
    });
  }
}
