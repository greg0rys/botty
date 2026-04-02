<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Coins;

class User extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'first_name' => 'string',
        'last_name'=> 'string',
        'email'=> 'string',
        'password'=> 'string',
    ];

    public function coins(): HasOne
    {
        return $this->hasOne(Coins::class);
    }
}
