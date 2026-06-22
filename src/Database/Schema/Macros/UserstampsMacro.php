<?php

namespace SanSanLabs\Userstamps\Database\Schema\Macros;

use Illuminate\Database\Schema\Blueprint;

class UserstampsMacro implements MacroInterface
{
    public function register(): void
    {
        $this->registerUserstamps();
        $this->registerSoftUserstamps();
        $this->registerDropUserstamps();
        $this->registerDropSoftUserstamps();
    }

    public function addUserIdColumn(Blueprint $table, string $column): void
    {
        $isMorph = config('userstamps.is_using_morph');
        $type = config('userstamps.users_table_id_column_type');
        $usersTable = config('userstamps.users_table', 'users');
        $usersTableIdColumnName = config('userstamps.users_table_id_column_name', 'id');

        match ($type) {
            'bigIncrements' => $isMorph
                ? $table->unsignedBigInteger($column)->nullable()
                : $table->foreignId($column)->nullable(),

            'uuid' => $isMorph
                ? $table->uuid($column)->nullable()
                : $table->foreignUuid($column)->nullable(),

            'ulid' => $isMorph
                ? $table->ulid($column)->nullable()
                : $table->foreignUlid($column)->nullable(),

            default => $isMorph
                ? $table->unsignedInteger($column)->nullable()
                : $table->unsignedInteger($column)->nullable(),
        };

        if (! $isMorph) {
            $table->foreign($column)
                ->references($usersTableIdColumnName)
                ->on($usersTable)
                ->nullOnDelete();
        }
    }

    private function registerUserstamps(): void
    {
        Blueprint::macro('userstamps', function (): void {
            $createdByColumn = config('userstamps.created_by_column').'_id';
            $updatedByColumn = config('userstamps.updated_by_column').'_id';

            app(UserstampsMacro::class)->addUserIdColumn($this, $createdByColumn);

            if (config('userstamps.is_using_morph')) {
                $this->string(config('userstamps.created_by_column').'_type')->nullable();
            }

            app(UserstampsMacro::class)->addUserIdColumn($this, $updatedByColumn);

            if (config('userstamps.is_using_morph')) {
                $this->string(config('userstamps.updated_by_column').'_type')->nullable();
            }
        });
    }

    private function registerSoftUserstamps(): void
    {
        Blueprint::macro('softUserstamps', function (): void {
            $deletedByColumn = config('userstamps.deleted_by_column').'_id';

            app(UserstampsMacro::class)->addUserIdColumn($this, $deletedByColumn);

            if (config('userstamps.is_using_morph')) {
                $this->string(config('userstamps.deleted_by_column').'_type')->nullable();
            }
        });
    }

    private function registerDropUserstamps(): void
    {
        Blueprint::macro('dropUserstamps', function (): void {
            $createdByColumn = config('userstamps.created_by_column').'_id';
            $updatedByColumn = config('userstamps.updated_by_column').'_id';
            $columns = [$createdByColumn, $updatedByColumn];

            if (config('userstamps.is_using_morph')) {
                $columns[] = config('userstamps.created_by_column').'_type';
                $columns[] = config('userstamps.updated_by_column').'_type';
            } else {
                $this->dropForeign([$createdByColumn]);
                $this->dropForeign([$updatedByColumn]);
            }

            $this->dropColumn($columns);
        });
    }

    private function registerDropSoftUserstamps(): void
    {
        Blueprint::macro('dropSoftUserstamps', function (): void {
            $deletedByColumn = config('userstamps.deleted_by_column').'_id';
            $columns = [$deletedByColumn];

            if (config('userstamps.is_using_morph')) {
                $columns[] = config('userstamps.deleted_by_column').'_type';
            } else {
                $this->dropForeign([$deletedByColumn]);
            }

            $this->dropColumn($columns);
        });
    }
}
