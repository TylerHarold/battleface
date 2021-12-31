<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    protected $connection = "mysql";
    protected $table = "quotes";

    protected $fillable = [
        'user_id', 'currency_id', 'total', 'age', 'start_date', 'end_date'
    ];

    /**
     * Returns the user of the quote
     */
    public function user() {
        return $this->hasOne('App\Models\User', 'id', 'user_id')->first();
    }
}
