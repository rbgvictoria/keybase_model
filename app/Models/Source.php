<?php

namespace App\Models;

use App\Traits\Blamable;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Source
 * 
 * @property int $id
 * @property DateTime $created_at
 * @property DateTime $updated_at
 * @property string $title
 * @property string $authors
 * @property string $year
 * @property string $in_authors
 * @property string $in_title
 * @property string $edition
 * @property string $journal
 * @property string $series
 * @property string $volume
 * @property string $issue
 * @property string $part
 * @property string $publisher
 * @property string $place_of_publication
 * @property string $pages
 * @property string $url
 * @property int $project_id
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property Project $project
 * @property Agent $createdBy
 * @property Agent $updatedBy
 */
class Source extends Model
{
    use Blamable;

    /**
     * Project the key for which this is the source belongs to
     * 
     * @return BelongsTo<Project, Source>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
