<?php

namespace App\Actions\Exports;

use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class GetItems
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

    /**
     * @return Collection
     */
    public function execute()
    {
        $items = DB::connection('keybase_old')
            ->query()
            ->select('i.ItemsID as id', 'i.Name as name', 'pi.Url as url')
            ->from('keys as k')
            ->join('leads as l', 'k.KeysID', '=', 'l.KeysID')
            ->join('items as i', 'l.ItemsID', '=', 'i.ItemsID')
            ->leftJoin('projectitems as pi', function (JoinClause $join) {
                $join->on('i.ItemsID', '=', 'pi.ItemsID')
                    ->whereColumn('k.ProjectsID', '=', 'pi.ProjectsID');
            })
            ->where('k.ProjectsID', '=', $this->project)
            ->union(
                DB::connection('keybase_old')
                    ->query()
                    ->select('i.ItemsID as id', 'i.Name as name', 'pi.Url as url')
                    ->from('keys as k')
                    ->join('items as i', 'k.TaxonomicScopeID', '=', 'i.ItemsID')
                    ->leftJoin('projectitems as pi', function (JoinClause $join) {
                        $join->on('i.ItemsID', '=', 'pi.ItemsID')
                            ->whereColumn('k.ProjectsID', '=', 'pi.ProjectsID');
                    })
                    ->where('k.ProjectsID', '=', $this->project)
            )
            ->orderBy('name')
            ->get();

        return $items->map(fn ($item) => (array) $item);
    }

}
