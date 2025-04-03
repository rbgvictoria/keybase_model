<?php

namespace App\Traits;

use App\Models\Agent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait Blamable
{
    /**
     * Agent who created the resource
     *
     * @return BelongsTo<Agent, Agent>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Agent who last updated the resource
     *
     * @return BelongsTo<Agent, Agent>
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

}
