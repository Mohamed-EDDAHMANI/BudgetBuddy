<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    // Relationship with expenses (many-to-many)
    public function expenses()
    {
        return $this->belongsToMany(Expense::class, 'expense_tag');
    }
}
