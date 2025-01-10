<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Homeful\Properties\Models\Project;
use App\Traits\HasSellerAttributes;
use Homeful\Common\Traits\HasMeta;

/**
 * Class ProjectUser
 *
 * @property User $user
 * @property Project $project
 * @property string $seller_commission_code
 *
 * @method int getKey()
 */
class ProjectUser extends Pivot
{
    use HasSellerAttributes;
    use HasMeta;

    public function project(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
