<?php

namespace SanSanLabs\Userstamps\Tests\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UserWithUlid extends Authenticatable
{
    use HasUlids;
    use SoftDeletes;

    protected $fillable = ['name', 'email'];
}
