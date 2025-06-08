<?php

namespace App\Actions\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GetSources
{

    /**
     * @var int
     */
    private int $project;

    /**
     * Create a new class instance.
     * 
     * @param int $project
     */
    public function __construct(int $project)
    {
        $this->project = $project;
    }

    /**
     * @return Collection
     */
    public function execute(): Collection
    {
        $sources = DB::connection('keybase_old')
            ->query()
            ->select(
                's.SourcesID as id',
                's.Authors as author',
                's.year',
                's.title',
                's.InAuthors as collection_authors',
                's.InTitle as collection_title',
                's.edition',
                's.series',
                's.volume',
                's.part',
                's.publisher',
                's.PlaceOfPublication as place_of_publication',
                's.pages',
                's.url'
            )
            ->from('sources as s')
            ->where('s.ProjectsID', '=', $this->project)
            ->get();

        return $sources->map(fn ($source) => (array) $source);
    }

}
