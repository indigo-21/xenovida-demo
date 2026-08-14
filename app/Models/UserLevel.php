<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserLevel extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'description',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'user_level_id');
    }
}
