<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $keyType = 'string';
    protected $hidden = array('pivot');
    protected $fillable = [
        'id',
        'provider'
    ];

    public function articles() {
        return $this->belongsToMany(
            Article::class,
            'articles_events',
            'event_id',
            'article_id'
        );
    }
}
