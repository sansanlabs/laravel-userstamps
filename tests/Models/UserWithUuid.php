<?php

namespace SanSanLabs\Userstamps\Tests\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UserWithUuid extends Authenticatable
{
    use HasUuids;
    use SoftDeletes;

    protected $fillable = ['name', 'email'];
}
