<?php

namespace SanSanLabs\LaravelUserstamps\Observers;

use Illuminate\Database\Eloquent\Model;

class UserstampObserver {
  public function creating(Model $model): void {
    if ($model->isClean(config("userstamps.created_by_column"))) {
      $model->{config("userstamps.created_by_column")} = $this->getUserPrimaryValue();
    }

    if ($model->isClean(config("userstamps.updated_by_column"))) {
      $model->{config("userstamps.updated_by_column")} = $this->getUserPrimaryValue();
    }
  }

  public function updating(Model $model): void {
    if ($model->isClean(config("userstamps.updated_by_column"))) {
      $model->{config("userstamps.updated_by_column")} = $this->getUserPrimaryValue();
    }
  }

  public function deleting(Model $model): void {
    if ($model->usingSoftDeletes()) {
      $model->{config("userstamps.deleted_by_column")} = $this->getUserPrimaryValue();
      $model->{config("userstamps.updated_by_column")} = $this->getUserPrimaryValue();
      $this->saveWithoutEventDispatching($model);
    }
  }

  public function restoring(Model $model): void {
    if ($model->usingSoftDeletes()) {
      $model->{config("userstamps.deleted_by_column")} = null;
      $model->{config("userstamps.updated_by_column")} = $this->getUserPrimaryValue();
      $this->saveWithoutEventDispatching($model);
    }
  }

  private function saveWithoutEventDispatching(Model $model): bool {
    $eventDispatcher = $model->getEventDispatcher();

    $model->unsetEventDispatcher();
    $saved = $model->save();
    $model->setEventDispatcher($eventDispatcher);

    return $saved;
  }

  private function getUserPrimaryValue(): int|string|null {
    if (!auth()->check()) {
      return null;
    }

    if (config("userstamps.users_table_id_column_name") !== "id") {
      return auth()->user()->{config("userstamps.users_table_id_column_name")};
    }

    return auth()->id();
  }
}
