<?php

namespace App\Actions\Exports;

class GetKey
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
        $leads = (new GetLeads($this->key))->execute();

        // validate leads: make sure there are no dead ends
        $from = $leads->map(fn ($lead) => $lead['from']);
        $toCouplets = $leads->filter(fn ($lead) => $lead['to_couplet'])
            ->map(fn ($lead) => $lead['to_couplet']);
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

        // ckeck for shortcuts
        $shortcuts = $leads->filter(fn ($lead) => $lead['shortcut'])->map(fn ($lead) => $lead['shortcut']);

        return $leads->map(function ($lead) use ($shortcuts) {
            if ($lead['to_couplet']) {
                $to = $lead['to_couplet'];
            }
            else {
                $to = $lead['to_item'];
            }

            $ret = [
                'from' => $lead['from'],
                'statement' => html_entity_decode($lead['statement']),
                'to' => $to,
            ];

            if ($shortcuts->count() > 0) {
                $ret['shortcut'] = $lead['shortcut'];
            }

            return $ret;
        })
        ->sortBy('from');
    }
}
