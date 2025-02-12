<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Project User
 * 
 * @property int $id
 * @property DateTime $created_at
 * @property DateTime $updated_at
 * @property int $project_id
 * @property int $user_id
 * @property string $role
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property Project $project
 * @property User $user
 * @property Agent $createdBy
 * @property Agent $updatedBy
 */
class ProjectUser extends Model
{
    /**
     * Project the user has a role in
     * 
     * @return BelongsTo<Project, ProjectUser>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * User who has a role in the project
     * 
     * @return BelongsTo<User, ProjectUser>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
