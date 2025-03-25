<?php

namespace SanSanLabs\LaravelUserstamps\Database\Schema\Macros;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Support\Facades\DB;

class UserstampsMacro implements MacroInterface {
  public function register(): void {
    $this->registerUserstamps();
    $this->registerSoftUserstamps();
    $this->registerDropUserstamps();
    $this->registerDropSoftUserstamps();
  }

  private function registerUserstamps(): void {
    Blueprint::macro("userstamps", function () {
      if (config("userstamps.users_table_id_column_type") === "bigIncrements") {
        $this->unsignedBigInteger(config("userstamps.created_by_column"))->nullable();
        $this->unsignedBigInteger(config("userstamps.updated_by_column"))->nullable();
      } elseif (config("userstamps.users_table_id_column_type") === "uuid") {
        $this->uuid(config("userstamps.created_by_column"))->nullable();
        $this->uuid(config("userstamps.updated_by_column"))->nullable();
      } elseif (config("userstamps.users_table_id_column_type") === "ulid") {
        $this->ulid(config("userstamps.created_by_column"))->nullable();
        $this->ulid(config("userstamps.updated_by_column"))->nullable();
      } else {
        $this->unsignedInteger(config("userstamps.created_by_column"))->nullable();
        $this->unsignedInteger(config("userstamps.updated_by_column"))->nullable();
      }

      $this->foreign(config("userstamps.created_by_column"))
        ->references(config("userstamps.users_table_id_column_name"))
        ->on(config("userstamps.users_table"))
        ->onDelete("set null");

      $this->foreign(config("userstamps.updated_by_column"))
        ->references(config("userstamps.users_table_id_column_name"))
        ->on(config("userstamps.users_table"))
        ->onDelete("set null");

      return $this;
    });
  }

  private function registerSoftUserstamps(): void {
    Blueprint::macro("softUserstamps", function () {
      if (config("userstamps.users_table_id_column_type") === "bigIncrements") {
        $this->unsignedBigInteger(config("userstamps.deleted_by_column"))->nullable();
      } elseif (config("userstamps.users_table_id_column_type") === "uuid") {
        $this->uuid(config("userstamps.deleted_by_column"))->nullable();
      } elseif (config("userstamps.users_table_id_column_type") === "ulid") {
        $this->ulid(config("userstamps.deleted_by_column"))->nullable();
      } else {
        $this->unsignedInteger(config("userstamps.deleted_by_column"))->nullable();
      }

      $this->foreign(config("userstamps.deleted_by_column"))
        ->references(config("userstamps.users_table_id_column_name"))
        ->on(config("userstamps.users_table"))
        ->onDelete("set null");

      return $this;
    });
  }

  private function registerDropUserstamps(): void {
    Blueprint::macro("dropUserstamps", function (): void {
      if (!DB::connection() instanceof SQLiteConnection) {
        $this->dropForeign([config("userstamps.created_by_column")]);
      }

      if (!DB::connection() instanceof SQLiteConnection) {
        $this->dropForeign([config("userstamps.updated_by_column")]);
      }

      $this->dropColumn([config("userstamps.created_by_column"), config("userstamps.updated_by_column")]);
    });
  }

  private function registerDropSoftUserstamps(): void {
    Blueprint::macro("dropSoftUserstamps", function (): void {
      if (!DB::connection() instanceof SQLiteConnection) {
        $this->dropForeign([config("userstamps.deleted_by_column")]);
      }

      $this->dropColumn(config("userstamps.deleted_by_column"));
    });
  }
}
