<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentAlert extends Model
{
    protected $fillable = ['user_id', 'query', 'document_type'];
}
