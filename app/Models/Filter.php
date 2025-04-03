<?php

namespace App\Models;

use App\Traits\Blamable;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Filter
 * 
 * @property int $id
 * @property DateTime $created_at
 * @property DateTime $updated_at
 * @property string $items_not_found
 * @property bool $is_project_filter
 * @property int $project_id
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property Project $project
 * @property Item[] $items
 * @property Agent $createdBy
 * @property Agent $updatedBy
 */
class Filter extends Model
{
    use Blamable;

    /**
     * Project the filter belongs to
     * 
     * @return BelongsTo<Project, Filter>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Items in the filter
     * 
     * @return BelongsToMany<Item, Filter>
     */
    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'filter_item', 'filter_id', 'item_id');
    }
}
