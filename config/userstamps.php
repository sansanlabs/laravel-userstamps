<?php

return [
  /*
   * Specify the column type used in the schema for the account ID
   *
   * Options: increments, bigIncrements, uuid, ulid
   * Default: bigIncrements
   */
  "users_table_id_column_type" => "bigIncrements",

  /*
   * Specify the column name used as the foreign key reference for the account ID. Make sure all user tables have an ID column name that matches the one you define below.
   */
  "users_table_id_column_name" => "id",

  /*
   * If you set the column name to "created_by",
   * it will generate two columns:
   * - created_by_id
   * - created_by_type
   */
  "created_by_column" => "created_by",

  /*
   * If you set the column name to "updated_by",
   * it will generate two columns:
   * - updated_by_id
   * - updated_by_type
   */
  "updated_by_column" => "updated_by",

  /*
   * If you set the column name to "deleted_by",
   * it will generate two columns:
   * - deleted_by_id
   * - deleted_by_type
   */
  "deleted_by_column" => "deleted_by",

  /*
   * Indicate whether to include soft-deleted records in queries
   */
  "with_trashed" => false,
];
