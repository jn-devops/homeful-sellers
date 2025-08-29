<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Homeful\Properties\Models\Project;
use Homeful\Contacts\Models\Contact;
use App\Traits\HasSellerAttributes;
use Homeful\Common\Traits\HasMeta;
use Laravel\Sanctum\HasApiTokens;
use App\Models\UserRole;

/**
 * Class User
 *
 * @property string $name
 * @property string $email
 * @property string $seller_commission_code
 *
 * @method int getKey()
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use HasSellerAttributes;
    use HasMeta;
    use HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'first_name',//added ggvivar
        'middle_name',//added ggvivar
        'last_name',//added ggvivar
        'birthdate',
        'password',
        'seller_commission_code',//added ggvivar 05/22/2025
        'contact',//added 06/13/2025
        'seller_code',
        'user_role_id',//added ggvivar 
        'change_password',//added ggvivar
        'active'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function contacts()
    {
        return $this->belongsToMany(Contact::class, ContactUser::class)
            ->withPivot( 'meta', 'invited_at', 'validated_at')
            ->withTimestamps();
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, ProjectUser::class)
            ->withPivot( 'meta')
            ->withTimestamps();
    }
    
    public function role()
    {
        return $this->belongsTo(UserRole::class, 'user_role_id');
    }
    public function getPermissions(): array
{
    $permissions = [];
    $role = $this->role;
    if (!$role) return $permissions;
    
    $modules = $role->module(); 
    foreach ($modules as $module) { 
        if (empty($module->slug)) {
            continue;
        }
        $permissions[$module->slug] = [
            'view' => (bool) $module->view,
            'add' => (bool) $module->add,
            'edit' => (bool) $module->edit,
            'delete' => (bool) $module->delete,
            'import' => (bool) $module->import,
            'export' => (bool) $module->export,
        ];
    }
    return $permissions;
}
}