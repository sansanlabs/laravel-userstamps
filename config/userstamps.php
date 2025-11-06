<?php

return [
  /*
   * If set to true, the package will generate polymorphic relations
   * with an additional "_type" column.
   *
   * ⚠️ Warning:
   * Please make sure to enable or disable this BEFORE running migrations,
   * as it affects how your relationship columns are generated.
   */
  "is_using_morph" => false,

  /*
   * The name of the table that holds the users.
   *
   * ⚠️ Note:
   * This configuration is used **only when "is_using_morph" is false**.
   * If "is_using_morph" is set to true, this value will be ignored
   * because the relationship will use polymorphic "_type" columns instead
   * of a fixed user table reference.
   */
  "users_table" => "users",

  /*
   * The model that holds the users.
   *
   * ⚠️ Note:
   * This configuration is used **only when "is_using_morph" is false**.
   * If "is_using_morph" is set to true, this value will be ignored
   * because the morph relationship dynamically references the related model
   * based on the stored "_type" column.
   */
  "users_model" => \App\Models\User::class,

  /*
   * Specify the column type used in the schema for the account ID
   *
   * Options: increments, bigIncrements, uuid, ulid
   * Default: bigIncrements
   */
  "users_table_id_column_type" => "bigIncrements",

  /*
   * Specify the column name used as the foreign key reference for the account ID.
   * Make sure all user tables have an ID column name that matches the one you define below.
   */
  "users_table_id_column_name" => "id",

  /*
   * If you set the column name to "created_by",
   * it will generate two columns:
   * - created_by_id
   * - created_by_type (if morph is enabled)
   */
  "created_by_column" => "created_by",

  /*
   * If you set the column name to "updated_by",
   * it will generate two columns:
   * - updated_by_id
   * - updated_by_type (if morph is enabled)
   */
  "updated_by_column" => "updated_by",

  /*
   * If you set the column name to "deleted_by",
   * it will generate two columns:
   * - deleted_by_id
   * - deleted_by_type (if morph is enabled)
   */
  "deleted_by_column" => "deleted_by",

  /*
   * Indicate whether to include soft-deleted records in queries
   */
  "with_trashed" => false,
];
