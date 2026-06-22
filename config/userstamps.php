<?php

use App\Models\User;

return [
    /*
    |--------------------------------------------------------------------------
    | Morph Relationships
    |--------------------------------------------------------------------------
    | If set to true, the package will generate polymorphic relations
    | with an additional "_type" column.
    |
    | Warning:
    | Please make sure to enable or disable this BEFORE running migrations,
    | as it affects how your relationship columns are generated.
    |
    */
    'is_using_morph' => false,

    /*
    |--------------------------------------------------------------------------
    | Users Table
    |--------------------------------------------------------------------------
    | The name of the table that holds the users.
    |
    | Note:
    | This configuration is used only when "is_using_morph" is false.
    | If "is_using_morph" is true, this value will be ignored because
    | the relationship uses polymorphic "_type" columns instead of a
    | fixed user table reference.
    |
    */
    'users_table' => 'users',

    /*
    |--------------------------------------------------------------------------
    | Users Model
    |--------------------------------------------------------------------------
    | The model that holds the users.
    |
    | Note:
    | This configuration is used only when "is_using_morph" is false.
    | If "is_using_morph" is true, this value will be ignored because
    | the morph relationship dynamically references the related model
    | based on the stored "_type" column.
    |
    */
    'users_model' => User::class,

    /*
    |--------------------------------------------------------------------------
    | User ID Column Type
    |--------------------------------------------------------------------------
    | Specify the column type used in the schema for the user ID.
    |
    | Options: increments, bigIncrements, uuid, ulid
    | Default: bigIncrements
    |
    */
    'users_table_id_column_type' => 'bigIncrements',

    /*
    |--------------------------------------------------------------------------
    | User ID Column Name
    |--------------------------------------------------------------------------
    | Specify the column name used as the foreign key reference for
    | the user ID. Make sure all user tables have an ID column name
    | that matches the one defined below.
    |
    */
    'users_table_id_column_name' => 'id',

    /*
    |--------------------------------------------------------------------------
    | Created By Column
    |--------------------------------------------------------------------------
    | If the column name is set to "created_by", the package will
    | generate:
    |
    | - created_by_id
    | - created_by_type (when morph is enabled)
    |
    */
    'created_by_column' => 'created_by',

    /*
    |--------------------------------------------------------------------------
    | Updated By Column
    |--------------------------------------------------------------------------
    | If the column name is set to "updated_by", the package will
    | generate:
    |
    | - updated_by_id
    | - updated_by_type (when morph is enabled)
    |
    */
    'updated_by_column' => 'updated_by',

    /*
    |--------------------------------------------------------------------------
    | Deleted By Column
    |--------------------------------------------------------------------------
    | If the column name is set to "deleted_by", the package will
    | generate:
    |
    | - deleted_by_id
    | - deleted_by_type (when morph is enabled)
    |
    */
    'deleted_by_column' => 'deleted_by',

    /*
    |--------------------------------------------------------------------------
    | With Trashed Records
    |--------------------------------------------------------------------------
    | Indicate whether soft-deleted records should be included
    | in queries.
    |
    */
    'with_trashed' => false,
];
