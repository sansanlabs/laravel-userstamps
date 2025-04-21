<?php

namespace SanSanLabs\Userstamps\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use SanSanLabs\Userstamps\Observers\UserstampsObserver;

trait HasUserstamps {
  public static function bootHasUserstamps(): void {
    static::observe(UserstampsObserver::class);
  }

  public function creator(): MorphTo {
    $relation = $this->morphTo(__FUNCTION__, config("userstamps.created_by_column") . "_type", config("userstamps.created_by_column"));
    return config("userstamps.with_trashed") ? $relation->withTrashed() : $relation;
  }

  public function editor(): MorphTo {
    $relation = $this->morphTo(__FUNCTION__, config("userstamps.updated_by_column") . "_type", config("userstamps.updated_by_column"));
    return config("userstamps.with_trashed") ? $relation->withTrashed() : $relation;
  }

  public function destroyer(): MorphTo {
    $relation = $this->morphTo(__FUNCTION__, config("userstamps.deleted_by_column") . "_type", config("userstamps.deleted_by_column"));
    return config("userstamps.with_trashed") ? $relation->withTrashed() : $relation;
  }

  public static function usingSoftDeletes(): bool {
    return in_array(SoftDeletes::class, class_uses_recursive(static::class));
  }
}
