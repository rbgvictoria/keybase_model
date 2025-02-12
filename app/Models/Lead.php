<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Lead
 * 
 * @property int $id
 * @property DateTime $created_at
 * @property DateTime $updated_at
 * @property string $node_name
 * @property string $lead_text
 * @property int $parent_id
 * @property int $item_id
 * @property int $reticulation_id
 * @property int $subkey_id
 * @property int $key_id
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property Key $key
 * @property Lead $parent
 * @property Item $item
 * @property Lead $reticulation
 * @property Key $subkey
 * @property Agent $createdBy
 * @property Agent $updatedBy
 */
class Lead extends Model
{
    /**
     * Key the lead is in 
     * 
     * @return BelongsTo<Key, Lead>
     */
    public function key(): BelongsTo
    {
        return $this->belongsTo(Key::class);
    }

    /**
     * Parent of the lead
     * 
     * This is the couplet the lead is in
     * 
     * @return BelongsTo<Lead, Lead>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * Item the lead leads to
     * 
     * @return BelongsTo<item, Lead>
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(item::class);
    }

    /**
     * Reticulation the lead leads to
     * 
     * @return BelongsTo<Lead, Lead>
     */
    public function reticulation(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * Subkey the lead leads to
     * 
     * @return BelongsTo<Key, Lead>
     */
    public function subkey(): BelongsTo
    {
        return $this->belongsTo(Key::class);
    }

   /**
     * Agent who created the record
     * 
     * @return BelongsTo<Agent, Project>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Agent who last updated the record
     * 
     * @return BelongsTo<Agent, Project>
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }
}
