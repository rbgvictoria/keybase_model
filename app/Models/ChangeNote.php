<?php

namespace App\Models;

use App\Traits\Blamable;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Change Note
 * 
 * @property int $id
 * @property DateTime $created_at
 * @property DateTime $updated_at
 * @property string $remarks
 * @property int $key_id
 * @property int $version
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property Key $key
 * @property Agent $createdBy
 * @property Agent $updatedBy
 */
class ChangeNote extends Model
{
    use Blamable;

    /**
     * Key the note belongs to
     * 
     * @return BelongsTo<Key, ChangeNote>
     */
    public function key(): BelongsTo
    {
        return $this->belongsTo(Key::class);
    }
}
