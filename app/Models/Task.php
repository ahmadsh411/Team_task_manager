<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Task extends Model
{
    use HasFactory;
    protected $fillable = ["name", "description", "project_id",'assigned_to','title','status','complete_description','complete_time'];

    public function project(){
        return $this->belongsTo(Project::class,'project_id','id');
    }

    public function Groups(): HasMany
    {
        return $this->hasMany(Group::class,'task_id','id');
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class,'imageable');
    }
}
