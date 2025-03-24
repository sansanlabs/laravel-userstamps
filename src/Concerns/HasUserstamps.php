<?php

namespace SanSanLabs\LaravelUserstamps\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use SanSanLabs\LaravelUserstamps\Observers\UserstampObserver;

trait HasUserstamps {
  public static function bootHasUserstamps(): void {
    static::observe(UserstampObserver::class);
  }

  public function creator(): BelongsTo {
    return $this->belongsTo(
      config("userstamps.users_model"),
      config("userstamps.created_by_column"),
      config("userstamps.users_table_id_column_name"),
    )->withTrashed(config("userstamps.with_trashed"));
  }

  public function editor(): BelongsTo {
    return $this->belongsTo(
      config("userstamps.users_model"),
      config("userstamps.updated_by_column"),
      config("userstamps.users_table_id_column_name"),
    )->withTrashed(config("userstamps.with_trashed"));
  }

  public function destroyer(): BelongsTo {
    return $this->belongsTo(
      config("userstamps.users_model"),
      config("userstamps.deleted_by_column"),
      config("userstamps.users_table_id_column_name"),
    )->withTrashed(config("userstamps.with_trashed"));
  }

  public static function usingSoftDeletes(): bool {
    return in_array(SoftDeletes::class, class_uses_recursive(static::class));
  }
}
