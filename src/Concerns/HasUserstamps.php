<?php

namespace SanSanLabs\Userstamps\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use SanSanLabs\Userstamps\Observers\UserstampsObserver;

trait HasUserstamps {
  public static function bootHasUserstamps(): void {
    static::observe(UserstampsObserver::class);
  }

  public function createdBy(): MorphTo|BelongsTo {
    if (config("userstamps.is_using_morph")) {
      $relation = $this->morphTo(__FUNCTION__, config("userstamps.created_by_column") . "_type", config("userstamps.created_by_column") . "_id");
    } else {
      $relation = $this->belongsTo(
        config("auth.providers.users.model", "App\Models\User"),
        config("userstamps.created_by_column") . "_id",
        config("userstamps.users_table_id_column_name"),
      );
    }

    return config("userstamps.with_trashed") ? $relation->withTrashed() : $relation;
  }

  public function updatedBy(): MorphTo|BelongsTo {
    if (config("userstamps.is_using_morph")) {
      $relation = $this->morphTo(__FUNCTION__, config("userstamps.updated_by_column") . "_type", config("userstamps.updated_by_column") . "_id");
    } else {
      $relation = $this->belongsTo(
        config("auth.providers.users.model", "App\Models\User"),
        config("userstamps.updated_by_column") . "_id",
        config("userstamps.users_table_id_column_name"),
      );
    }

    return config("userstamps.with_trashed") ? $relation->withTrashed() : $relation;
  }

  public function deletedBy(): MorphTo|BelongsTo {
    if (config("userstamps.is_using_morph")) {
      $relation = $this->morphTo(__FUNCTION__, config("userstamps.deleted_by_column") . "_type", config("userstamps.deleted_by_column") . "_id");
    } else {
      $relation = $this->belongsTo(
        config("auth.providers.users.model", "App\Models\User"),
        config("userstamps.deleted_by_column") . "_id",
        config("userstamps.users_table_id_column_name"),
      );
    }

    return config("userstamps.with_trashed") ? $relation->withTrashed() : $relation;
  }

  public static function usingSoftDeletes(): bool {
    return in_array(SoftDeletes::class, class_uses_recursive(static::class));
  }
}
