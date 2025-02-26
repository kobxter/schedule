<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'start', 'end', 'description', 'status', 'color'];

    public function files()
    {
        return $this->hasMany(EventFile::class, 'schedule_id');
    }
}
