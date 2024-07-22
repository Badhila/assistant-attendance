<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssistantMeet extends Model
{
    use HasFactory;

    public function assistant()
    {
        return $this->belongsTo(Assistant::class);
    }

    public function meet()
    {
        return $this->belongsTo(Meet::class);
    }
}
