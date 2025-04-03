<?php

namespace App\Models;

use DateTime;
use App\Traits\Blamable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Agent
 * 
 * @property int $id
 * @property DateTime $created_at
 * @property DateTime $updated_at
 * @property string $name
 * @property string $first_name
 * @property string $surname
 * @property string $email
 * @property string $orcid
 * @property int $user_id
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property User $user
 * @property Agent $createdBy
 * @property Agent $updatedBy
 */
class Agent extends Model
{
    use Blamable;

    /**
     * User account the Agent belongs to
     * @return BelongsTo<User, Agent>
     */
    public function user(): BelongsTo
    {
        return $this->belongsto(User::class);
    }
}
