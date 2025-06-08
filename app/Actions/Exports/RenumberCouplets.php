<?php

namespace App\Actions\Exports;

use Illuminate\Support\Collection;

class RenumberCouplets
{

    private Collection $leads;

    private Collection $nodes;

    /**
     * Create a new class instance.
     */
    public function __construct($leads)
    {
        $this->leads = $leads;
    }
    
    /**
     * Execute the action
     *
     * @return Collection|null
     */
    public function execute(): ?Collection
    {

        $couplets = $this->getCouplets($this->leads);

        return $this->leads->map(function ($lead) use ($couplets) {
            $lead['from'] = $couplets[$lead['from']];
            if ($lead['to_couplet']) {
                $lead['to_couplet'] = $couplets[$lead['to_couplet']];
            }
            return $lead;
        });
    }
    
    /**
     * getCouplets
     *
     * @param  Collection $leads
     * @return array
     */
    private function getCouplets(Collection $leads): array
    {
        $i = 0;
        $couplets = [];
        foreach ($leads as $lead) {
            if (!in_array($lead['from'], array_keys($couplets))) {
                $i++;
                $couplets[$lead['from']] = $i;
            }
        }
        return $couplets;
    }
}
