<?php

namespace App\Actions\Exports;

use Illuminate\Support\Collection;

/**
 * Balances a key by ordering leads by branch size, so that smaller branches are
 * above larger branches
 *   
 *    /------------------
 *    |
 * ---| 1     /----------
 *    |   /---| 3
 *    |   |   \----------
 *    \---| 2
 *        |   /----------
 *        \---| 4 
 *            |   /------
 *            \---| 5
 *                \------
 */
class BalanceKey
{

    /** @var Collection */
    private Collection $leads; 

    /** @var Collection */
    private Collection $nodes;

    /** @var int */
    private int $left;

    /**
     * Create a new class instance.
     */
    public function __construct($leads)
    {
        $this->leads = $leads;
    }

    /**
     * @return Collection
     */
    public function execute(): Collection
    {
        // Create nested sets
        $leads = (new CreateNestedSets($this->leads))->execute();

        // Set a 'size' property, which is the difference between 'right' and
        // 'left', on the leads
        $leads = $leads->map(function ($lead) {
            $lead['size'] = $lead['right'] - $lead['left'];
            return $lead;
        });

        // Create nested sets once more. Now we can sort the leads in a couplet
        // by size, so that smaller branches go before larger ones
        $leads = (new CreateNestedSets($leads))->execute();

        return $leads;
    }
}
