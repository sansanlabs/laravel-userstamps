<?php

namespace SanSanLabs\Userstamps\Observers;

use Illuminate\Database\Eloquent\Model;

class UserstampsObserver {
  public function creating(Model $model): void {
    if ($model->isClean(config("userstamps.created_by_column"))) {
      $model->{config("userstamps.created_by_column") . "_id"} = $this->getUserPrimaryValueAndClass()["id"];
      $model->{config("userstamps.created_by_column") . "_type"} = $this->getUserPrimaryValueAndClass()["type"];
    }

    if ($model->isClean(config("userstamps.updated_by_column"))) {
      $model->{config("userstamps.updated_by_column") . "_id"} = $this->getUserPrimaryValueAndClass()["id"];
      $model->{config("userstamps.updated_by_column") . "_type"} = $this->getUserPrimaryValueAndClass()["type"];
    }
  }

  public function updating(Model $model): void {
    if ($model->isClean(config("userstamps.updated_by_column"))) {
      $model->{config("userstamps.updated_by_column") . "_id"} = $this->getUserPrimaryValueAndClass()["id"];
      $model->{config("userstamps.updated_by_column") . "_type"} = $this->getUserPrimaryValueAndClass()["type"];
    }
  }

  public function deleting(Model $model): void {
    if ($model->usingSoftDeletes()) {
      $model->{config("userstamps.deleted_by_column") . "_id"} = $this->getUserPrimaryValueAndClass()["id"];
      $model->{config("userstamps.deleted_by_column") . "_type"} = $this->getUserPrimaryValueAndClass()["type"];
      $model->{config("userstamps.updated_by_column") . "_id"} = $this->getUserPrimaryValueAndClass()["id"];
      $model->{config("userstamps.updated_by_column") . "_type"} = $this->getUserPrimaryValueAndClass()["type"];
      $this->saveWithoutEventDispatching($model);
    }
  }

  public function restoring(Model $model): void {
    if ($model->usingSoftDeletes()) {
      $model->{config("userstamps.deleted_by_column") . "_id"} = null;
      $model->{config("userstamps.deleted_by_column") . "_type"} = null;
      $model->{config("userstamps.updated_by_column") . "_id"} = $this->getUserPrimaryValueAndClass()["id"];
      $model->{config("userstamps.updated_by_column") . "_type"} = $this->getUserPrimaryValueAndClass()["type"];
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

  private function getUserPrimaryValueAndClass(): array {
    if (!auth()->check()) {
      return [
        "id" => null,
        "type" => null,
      ];
    }

    if (config("userstamps.users_table_id_column_name") !== "id") {
      return [
        "id" => auth()->user()->{config("userstamps.users_table_id_column_name")},
        "type" => get_class(auth()->user()),
      ];
    }

    return [
      "id" => auth()->id(),
      "type" => get_class(auth()->user()),
    ];
  }
}
