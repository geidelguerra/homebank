<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $incrementing = false;

    public $keyType = 'string';

    protected $primaryKey = 'code';

    protected $casts = [
        'base' => 'array',
        'exponent' => 'int'
    ];

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class, 'currency_code');
    }
}
