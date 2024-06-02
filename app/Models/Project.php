<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name', 'description', 'start_date', 'end_date', 'team_member'
    ];

    public static function getAssignedTeamMembers($project_id=0)
    {
        return self::join('users', function($join) {
            $join->on(self::raw('FIND_IN_SET(users.id, projects.team_member)'), '>', self::raw('0'));
        })
        ->where('projects.id', $project_id)
        ->select('users.id','users.name')
        ->get();
    }
}
