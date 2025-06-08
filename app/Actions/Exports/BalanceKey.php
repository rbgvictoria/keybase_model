<?php

namespace App\Actions\Exports;

use Illuminate\Support\Collection;

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
     * 
     */
    public function execute(): Collection
    {

        $this->left = 0;
        $this->nodes = collect([]);
        
        $coupletNumber = $this->leads->first()['from'];

        $this->getNodes(['to_couplet' => $coupletNumber]);

        $this->leads = $this->nodes;

        $this->left = 0;
        $this->nodes = collect([]);

        $this->getNodes(['to_couplet' => $coupletNumber]);

        return $this->nodes->sortBy('left');
    }

    private function getNodes($lead)
    {

        $leads = $this->leads->filter(fn ($item) => $item['from'] == $lead['to_couplet'])->sortBy('weight');

        foreach ($leads as $next) {
            $this->left++;
            $node = [
                'from' => $next['from'],
                'statement' => $next['statement'],
                'to_couplet' => $next['to_couplet'],
                'to_item' => $next['to_item'],
                'shortcut' => $next['shortcut'],
                'left' => $this->left,
            ];

            if ($next['to_couplet']) {
                $this->getNodes($next);
            }

            $this->left++;
            $node['right'] = $this->left;
            $node['weight'] = $node['right'] - $node['left'];
            $this->nodes->push($node);
        }
    }

}
