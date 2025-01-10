<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Homeful\Contacts\Models\Contact;
use App\Traits\HasSellerAttributes;
use Homeful\Common\Traits\HasMeta;

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

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
//        'seller_commission_code'
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
}
