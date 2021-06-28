<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    //
    protected $fillable = [ 'name','ISBN','author','publish',
        'pubdate','status','local'
    ];

    protected $casts = [
        'pubdate' => 'datetime',
        'status' => 'boolean'
    ];

    public function record(){
        return $this->hasOne(Record::class);
    }
}
