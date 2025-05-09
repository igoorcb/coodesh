<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'imported_at',
        'products_imported',
        'status',
        'notes'
    ];

    protected $casts = [
        'imported_at' => 'datetime',
    ];
}