<?php

namespace App\Models;

use App\Traits\Blamable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ramsey\Uuid\Uuid;

/**
 * Item
 * 
 * @property int $id
 * @property Uuid $guid
 * @property string $name
 * @property string $url
 * @property int $project_id
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property Project $project
 * @property Key[] $keysTo
 * @property Key[] $keysIn
 * @property Filter[] $filters
 * @property Filter[] $projectFilters
 * @property Filter[] $userFilters
 * @property Agent $createdBy
 * @property Agent $updatedBy
 */
class Item extends Model
{
    use Blamable;

    /**
     * Project the item belongs to
     * 
     * @return BelongsTo<Project, Item>
     */
    public function project() : BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Keys to the item
     * 
     * @return HasMany<Key, Item>
     */
    public function keysTo(): HasMany
    {
        return $this->hasMany(Key::class, 'item_id', 'id');
    }

    /**
     * Keys the item keys out in
     * 
     * @return BelongsToMany<Key, Item>
     */
    public function keysIn(): BelongsToMany
    {
        return $this->belongsToMany(Key::class, 'leads', 'item_id', 'key_id')
                ->distinct();
    }

    /**
     * Filters the item is in
     * 
     * @return BelongsToMany<Filter, Item>
     */
    public function filters(): BelongsToMany
    {
        return $this->belongsToMany(Filter::class, 'filter_item');
    }

    /**
     * Project filters the item is in
     * 
     * @return Collection<int, Filter>
     */
    public function projectFilters(): Collection
    {
        return Filter::from('filters as f')
            ->join('filter_items as fi', 'f.id', '=', 'fi.filter_id')
            ->where('fi.item_id', '=', $this->id)
            ->where('f.is_project_filter', '=', true)
            ->get();
    }

    /**
     * User filters the item is in
     * 
     * @return Collection<int, Filter>
     */
    public function userFilters(): Collection
    {
        return Filter::from('filters as f')
            ->join('filter_items as fi', 'f.id', '=', 'fi.filter_id')
            ->where('fi.item_id', '=', $this->id)
            ->where(function ($query) {
                $query->whereNull('f.is_project_filter')
                        ->orWhere('f.is_project_filter', '=', false);
            })
            ->get();
    }
}
