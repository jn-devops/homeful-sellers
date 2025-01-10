<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Homeful\Contacts\Models\Contact;
use App\Traits\HasSellerAttributes;
use Homeful\Common\Traits\HasMeta;

/**
 * Class ContactUser
 *
 * @property User $user
 * @property Contact $contact
 * @property string $seller_commission_code
 * @property string $provisioning_uri
 *
 * @method int getKey()
 */
class ContactUser extends Pivot
{
    use HasSellerAttributes;
    use HasMeta;

    protected $fillable = [
        'invited_at',
        'validated_at',
        'provisioning_uri'
    ];

    public function contact(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function setProvisioningUriAttribute(string $value): self
    {
        $this->getAttribute('meta')->set('provisioning_uri', $value);

        return $this;
    }

    public function getProvisioningUriAttribute(): ?string
    {
        return $this->getAttribute('meta')->get('provisioning_uri');
    }
}
