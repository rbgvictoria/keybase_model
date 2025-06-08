<?php

namespace App\Actions\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GetProjects
{

    /**
     * @var int|null
     */
    private ?int $project;

    /**
     * Create a new class instance.
     */
    public function __construct(?int $project=null)
    {
        $this->project = $project;
    }

    public function execute(): Collection
    {
        $query = DB::connection('keybase_old')
            ->query()
            ->select(
                'p.ProjectsID as id',
                'p.Name as title',
                'p.description',
                'i.Name as item',
                'p.GeographicScope as spatial',
                'p.ProjectIcon as icon'
            )
            ->from('projects as p')
            ->leftJoin('items as i', 'p.TaxonomicScopeID', '=', 'i.ItemsID');
        
        if ($this->project) {
            $query->where('p.ProjectsID', $this->project);
        }

        $projects = $query->get();

        return $projects->map(fn ($project) => (array) $project);
    }

}
