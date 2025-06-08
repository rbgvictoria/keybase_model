<?php

namespace App\Actions\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GetKeys
{
    /**
     * @var int
     */
    private int $project;

    /**
     * Create a new class instance.
     */
    public function __construct($project)
    {
        $this->project = $project;
    }

    public function execute(): Collection
    {
        $keys = DB::connection('keybase_old')
            ->query()
            ->select(
                'k.KeysID as id',
                'k.TimestampCreated as created_at',
                'k.TimestampModified as updated_at',
                'k.Title as title',
                'k.Author as author',
                'k.Description as description',
                'i.Name as item',
                'k.GeographicScope AS spatial', 
                'k.Notes AS remarks',
                'k.SourcesID AS source_id',
                'u.Email AS created_by',
                'u2.Email as updated_by'
            )
            ->from('keys as k')
            ->leftJoin('items as i', 'k.TaxonomicScopeID', '=', 'i.ItemsID')
            ->leftJoin('users as u', 'k.CreatedByID', '=', 'u.UsersID')
            ->leftJoin('users as u2', 'k.ModifiedByID', '=', 'u2.UsersID')
            ->where('k.ProjectsID', $this->project)
            ->get();

        $keys = $keys->map(function ($key) {
            $key->key_file = Str::slug('key-' . $key->id . '-' . $key->title) . '.tsv';
            return $key;
        });

        return $keys->map(fn ($key) => (array) $key);
    }
}
