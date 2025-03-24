<?php

return [
  "users_table" => "users",

  "users_table_id_column_type" => "bigIncrements",

  "users_table_id_column_name" => "id",

  "users_model" => \App\Models\User::class,

  "created_by_column" => "created_by",

  "updated_by_column" => "updated_by",

  "deleted_by_column" => "deleted_by",

  "with_trashed" => false,
];
