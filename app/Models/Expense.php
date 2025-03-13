<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Tag;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'description', 'amount', 'date',
    ];

    // Relationship with user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with tags (many-to-many)
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'expense_tag');
    }
}
