<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Feature;
use App\Models\User;

class UsedFeature extends Model
{
    use HasFactory;

    protected $fillable = ['feature_id', 'user_id', 'credits'];

    protected function casts()
    {
        return [
            'data' => 'array',
        ];
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function feature()
    {
        return $this->belongsTo(Feature::class);
    }

}