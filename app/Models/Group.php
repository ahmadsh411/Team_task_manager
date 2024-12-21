<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Group extends Model
{
    use HasFactory;

    protected $fillable=[
        'project_id',
        'project_name',
        'user_id',
        'user_name'
        ,'task_id','task_name',
        'owner',
        'owner_id'
    ];

    public function project(){
        return $this->belongsTo(Project::class,'project_id','id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class,'task_id','id');
    }

}
