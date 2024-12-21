<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ["name", "description","start_date","end_date","user_id"];

    public function manager(){
        return $this->belongsTo(User::class,"user_id",'id');
    }

    public function tasks(){
        return $this->hasMany(Task::class,'project_id','id');
    }

    public function groups():HasMany
    {
        return $this->hasMany(Group::class,'project_id','id');
    }


}
