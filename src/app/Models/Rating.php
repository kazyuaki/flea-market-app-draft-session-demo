<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'rater_id',
        'ratee_id',
        'score',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
    public function rater()
    {
        return $this->belongsTo(User::class, 'rater_id');
    }
    public function ratee()
    {
        return $this->belongsTo(User::class, 'ratee_id');
    }
}
