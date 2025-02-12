<?php

namespace App\Models;

use DateTime;
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
    /**
     * User account the Agent belongs to
     * @return BelongsTo<User, Agent>
     */
    public function user(): BelongsTo
    {
        return $this->belongsto(User::class);
    }

    /**
     * Agent who created the record
     * @return BelongsTo<Agent, Agent>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Agent who last updated the record
     * @return BelongsTo<Agent, Agent>
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }
}
