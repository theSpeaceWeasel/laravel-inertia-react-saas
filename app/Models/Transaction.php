<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['status', 'price', 'credits', 'user_id', 'package_id' , 'session_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}