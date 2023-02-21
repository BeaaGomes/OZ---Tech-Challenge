<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Launch extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'provider'
    ];

    public function articles() {
        return $this->belongsToMany(
            Article::class,
            'articles_launches',
            'launch_id',
            'article_id'
        );
    }
}
