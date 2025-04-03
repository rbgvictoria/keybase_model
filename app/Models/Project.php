<?php

namespace App\Models;

use App\Traits\Blamable;
use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Project
 * 
 * @property int $id
 * @property DateTime $created_at
 * @property DateTime $updated_at
 * @property string $title
 * @property string $description
 * @property string $project_icon
 * @property int $taxonomic_scope_id
 * @property int $created_by_id
 * @property int $updated_at_id
 * @property Item $taxonomicScope
 * @property Key[] $keys
 * @property Item[] $items
 * @property Filter[] $filters
 * @property Filter[] $projectFilters
 * @property Filter[] $userFilters
 * @property Agent $createdBy
 * @property Agent $updatedBy *
 */
class Project extends Model
{
    use Blamable;

    /**
     * Highest taxon of which all Items that are keyed out by all Keys in the
     * Project belong are members
     * 
     * @return BelongsTo<Item, Project>
     */
    public function taxonomicScope(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Keys belonging to the project
     * 
     * @return HasMany<Key, Project>
     */
    public function keys(): HasMany
    {
        return $this->hasMany(Key::class);
    }

    /**
     * Items belonging to the project
     * 
     * @return HasMany<Item, Project>
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    /**
     * Filters belonging to the project
     * 
     * @return HasMany<Filter, Project>
     */
    public function filters(): HasMany
    {
        return $this->hasMany(Filter::class);
    }

    /**
     * Project filters belonging to the project
     *
     * Project filters are filters that are available to all users. They have to
     * be created by project managers.
     * 
     * @return HasMany<Filter, Project>
     */
    public function getProjectFiltersAttribute(): Collection
    {
        return Filter::where('project_id', '=', $this->id)
            ->orWhere('is_project_filter', '=', true)
            ->get();
    }

    /**
     * User filters belonging to the project
     *
     * User filters are filters that are available nly to the user that created
     * them.
     * 
     * @return HasMany<Filter, Project>
     */
    public function getUserFiltersAttribute(): Collection
    {
        return Filter::where('project_id', '=', $this->id)
            ->whereNull('is_project_filter')
            ->orWhere('is_project_filter', '=', false)
            ->get();
    }
}
