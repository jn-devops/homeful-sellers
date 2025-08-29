<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\UserRole;

class Module extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'description',
        'add',
        'edit',
        'delete',
        'view',
        'import',
        'export'
    ];
    public function Roles()
    {
        return $this->hasMany(UserRole::class, 'module_id');
    }
}
