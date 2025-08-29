<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Module;

class UserRole extends Model
{
    // use HasFactory;
     use HasFactory;

    protected $fillable = ['name', 'description', 'status_id','module_id'];
    
    protected $casts = [
        'module_id' => 'array',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_role_id');
    }
    
    public function module()
    {
        return Module::whereIn('id', $this->module_id ?? [])->get();
    }
    // public function module()
    // {
    //     return $this->belongsTo(Module::class);
    // }

    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

}
