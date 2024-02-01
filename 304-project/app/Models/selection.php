<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class selection extends Model
{
    use HasFactory;

    protected $fillable = [
        'column',
        'condition',
        'value',
    ];
}
