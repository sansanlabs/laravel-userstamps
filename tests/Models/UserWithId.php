<?php

namespace SanSanLabs\Userstamps\Tests\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UserWithId extends Authenticatable
{
    use SoftDeletes;

    protected $fillable = ['name', 'email'];
}
