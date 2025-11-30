<?php

namespace SanSanLabs\Userstamps\Observers;

use Illuminate\Database\Eloquent\Model;

class UserstampsObserver
{
    public function creating(Model $model): void
    {
        if ($model->isClean(config('userstamps.created_by_column'))) {
            $userData = $this->getUserPrimaryValueAndClass();
            $model->{config('userstamps.created_by_column').'_id'} = $userData['id'];

            if (config('userstamps.is_using_morph')) {
                $model->{config('userstamps.created_by_column').'_type'} = $userData['type'];
            }
        }

        if ($model->isClean(config('userstamps.updated_by_column'))) {
            $userData = $this->getUserPrimaryValueAndClass();
            $model->{config('userstamps.updated_by_column').'_id'} = $userData['id'];

            if (config('userstamps.is_using_morph')) {
                $model->{config('userstamps.updated_by_column').'_type'} = $userData['type'];
            }
        }
    }

    public function updating(Model $model): void
    {
        if ($model->isClean(config('userstamps.updated_by_column'))) {
            $userData = $this->getUserPrimaryValueAndClass();
            $model->{config('userstamps.updated_by_column').'_id'} = $userData['id'];

            if (config('userstamps.is_using_morph')) {
                $model->{config('userstamps.updated_by_column').'_type'} = $userData['type'];
            }
        }
    }

    public function deleting(Model $model): void
    {
        if ($model->usingSoftDeletes()) {
            $userData = $this->getUserPrimaryValueAndClass();

            $model->{config('userstamps.deleted_by_column').'_id'} = $userData['id'];
            if (config('userstamps.is_using_morph')) {
                $model->{config('userstamps.deleted_by_column').'_type'} = $userData['type'];
            }

            $model->{config('userstamps.updated_by_column').'_id'} = $userData['id'];
            if (config('userstamps.is_using_morph')) {
                $model->{config('userstamps.updated_by_column').'_type'} = $userData['type'];
            }

            $this->saveWithoutEventDispatching($model);
        }
    }

    public function restoring(Model $model): void
    {
        if ($model->usingSoftDeletes()) {
            $userData = $this->getUserPrimaryValueAndClass();

            $model->{config('userstamps.deleted_by_column').'_id'} = null;
            if (config('userstamps.is_using_morph')) {
                $model->{config('userstamps.deleted_by_column').'_type'} = null;
            }

            $model->{config('userstamps.updated_by_column').'_id'} = $userData['id'];
            if (config('userstamps.is_using_morph')) {
                $model->{config('userstamps.updated_by_column').'_type'} = $userData['type'];
            }

            $this->saveWithoutEventDispatching($model);
        }
    }

    private function saveWithoutEventDispatching(Model $model): bool
    {
        $eventDispatcher = $model->getEventDispatcher();

        $model->unsetEventDispatcher();
        $saved = $model->save();
        $model->setEventDispatcher($eventDispatcher);

        return $saved;
    }

    private function getUserPrimaryValueAndClass(): array
    {
        if (! auth()->check()) {
            return [
                'id' => null,
                'type' => null,
            ];
        }

        $user = auth()->user();
        $idColumn = config('userstamps.users_table_id_column_name');

        return [
            'id' => $idColumn !== 'id' ? $user->{$idColumn} : auth()->id(),
            'type' => get_class($user),
        ];
    }
}
