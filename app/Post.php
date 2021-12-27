<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title', 'description'
    ];

    protected $guarded = []; // for not crud

    protected $hidden = [
        'created_at', 'updated_at'
    ]; // to hide
}
