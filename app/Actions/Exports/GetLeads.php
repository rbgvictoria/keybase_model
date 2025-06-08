<?php

namespace App\Actions\Exports;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class GetLeads
{
    /**
     * @var int 
     */
    private int $key;

    /**
     * Create a new class instance.
     */
    public function __construct($key)
    {
        $this->key = $key;
    }

    public function execute($renumber=true, $balance=false)
    {
        $cte = DB::connection('keybase_old')
            ->query()
            ->select(
                'l.LeadsID',
                'l.ParentID'
            )
            ->from('leads as l')
            ->join('keys as k', 'l.KeysID', '=', 'k.KeysID')
            ->where('k.KeysID', '=', $this->key)
            ->whereColumn('k.FirstStepID', '=', 'l.LeadsID')
            ->union(
                DB::connection('keybase_old')
                    ->query()
                    ->select(
                        'l.LeadsID',
                        'l.ParentID'
                    )
                    ->from('leads as l')
                    ->join('cte as c', 'l.ParentID', '=', 'c.LeadsID')
            );

        $leads = DB::connection('keybase_old')
            ->query()
            ->withRecursiveExpression('cte', $cte)
            ->select(
                'l.ParentID as from', 
                'l.LeadText as statement', 
                DB::raw("if(i.Name is null, l.LeadsID, null) as to_couplet"), 
                'i.name as to_item', 
                'i2.Name as shortcut'
            )
            ->from('cte as c')
            ->join('leads as l', 'c.LeadsID', '=', 'l.LeadsID')
            ->leftJoin('leads as l2', function (Builder $query) {
                $query->on('l.LeadsID', '=', 'l2.ParentID')
                    ->whereNotNull('l2.ItemsID');
            })
            ->leftJoin('items as i', 'l2.ItemsID', '=', 'i.ItemsID')
            ->leftJoin('leads as l3', function (Builder $query) {
                $query->on('l2.LeadsID', '=', 'l3.ParentID')
                    ->whereNotNull('l3.ItemsID');
            })
            ->leftJoin('items as i2', 'l3.ItemsID', '=', 'i2.ItemsID')
            ->whereNotNull('l.LeadText')
            ->where('l.LeadText', '<>', '[link through]')
            ->orderBy('l.ParentID')
            ->orderBy('l.LeadsID')
            ->get();

        $leads = $leads->map(fn ($lead) => (array) $lead);
        $from = $leads->map(fn ($lead) => $lead['from']);
        // print_r($from);
        $toCouplets = $leads->filter(fn ($lead) => $lead['to_couplet'])
            ->map(fn ($lead) => $lead['to_couplet']);
        // print_r($toCouplets);

        if ($toCouplets->diff($from)->count()) {
            return false;
        }

        if ($balance) {
            $balanceKey = new BalanceKey($leads);
            $leads = $balanceKey->execute();
        }

        if ($renumber) {
            $renumberCouplets = new RenumberCouplets($leads);
            $leads = $renumberCouplets->execute();
        }

        return $leads->map(function ($lead) {
            if ($lead['to_couplet']) {
                $to = $lead['to_couplet'];
            }
            else {
                $to = $lead['to_item'];
                if ($lead['shortcut']) {
                    $to .= ':' . $lead['shortcut'];
                }
            }

            return [
                'from' => $lead['from'],
                'statement' => html_entity_decode($lead['statement']),
                'to' => $to,
            ];
        })
        ->sortBy('from');
    }
}
