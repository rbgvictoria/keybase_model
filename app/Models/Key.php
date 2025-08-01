<?php

namespace App\Models;

use App\Traits\Blamable;
use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Ramsey\Uuid\Uuid;

/**
 * Key
 * 
 * @property int $id
 * @property DateTime $created_at
 * @property Uuid $guid
 * @property int $version
 * @property string $title
 * @property string $author
 * @property string $description
 * @property string $notes
 * @property bool $modified_from_source
 * @property int $root_id
 * @property int $item_id
 * @property int $project_id
 * @property int $subkey_of_id
 * @property int $source_id
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property Item $item
 * @property Lead $root
 * @property Project $project
 * @property Source $source
 * @property Lead[] $leads
 * @property Key[] $subkeys
 * @property Item[] $items
 * @property Filter[] $filters
 * @property Filter[] $projectFilters
 * @property Filter[] $userFilters
 * @property ChangeNote[] $changeNotes
 * @property Agent $createdBy
 * @property Agent $updatedBy
 */
class Key extends Model
{
    use Blamable;

    /**
     * Item for which the key is a key to its members
     * 
     * @return BelongsTo<Item, Key>
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Project the item belongs to
     * 
     * @return BelongsTo<Project, Key>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Source of the key
     * 
     * @return BelongsTo<Source, Key>
     */
    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    /**
     * First step in the key
     * 
     * @return void
     */
    public function root(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * Leads in the key
     * 
     * @return HasMany<Lead, Key>
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }


    public function subkeys(): HasMany
    {
        return $this->hasMany(Key::class, 'subkey_of_id');
    }

    /**
     * Items keyed out in the key
     * 
     * @return BelongsToMany<Item, Key>
     */
    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'key_item');
    }

    /**
     * Filters for the key
     * 
     * @return Collection<int, Filter>
     */
    public function getFiltersAttribute(): Collection
    {
        return Filter::from('filters as f')
            ->join('filter_item as fi', 'f.id', '=', 'fi.filter_id')
            ->join('leads as l', 'fi.item_id', '=', 'l.item_id')
            ->where('l.key_id', '=', $this->id)
            ->get();
    }

     /**
     * ProjectFilters for the key
     * 
     * @return Collection<int, Filter>
     */
    public function getProjectFiltersAttribute(): Collection
    {
        return Filter::from('filters as f')
            ->join('filter_item as fi', 'f.id', '=', 'fi.filter_id')
            ->join('leads as l', 'fi.item_id', '=', 'l.item_id')
            ->where('l.key_id', '=', $this->id)
            ->where('f.is_project_filter', '=', true)
            ->get();
    }

    /**
     * User filters for the key
     * 
     * @return Collection<int, Filter>
     */
    public function getUserFiltersAttribute(): Collection
    {
        return Filter::from('filters as f')
            ->join('filter_item as fi', 'f.id', '=', 'fi.filter_id')
            ->join('leads as l', 'fi.item_id', '=', 'l.item_id')
            ->where('l.key_id', '=', $this->id)
            ->where(function (QueryBuilder $query) {
                $query->whereNull('f.is_project_filter')
                    ->orWhere('f.is_project_filter', '=', false);
            })
            ->get();
    }

    /**
     * Change notes for the key
     * 
     * @return HasMany<ChangeNote, Key>
     */
    public function changeNotes(): HasMany
    {
        return $this->hasMany(ChangeNote::class);
    }
}
