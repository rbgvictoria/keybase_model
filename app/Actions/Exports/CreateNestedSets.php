<?php

namespace App\Actions\Exports;

use Illuminate\Support\Collection;

class CreateNestedSets
{

    /**
     * @var Collection
     */
    private Collection $leads;

    /**
     * @var Collection
     */
    private Collection $nodes;

    /**
     * @var int
     */
    private int $left;

    /**
     * Create a new class instance.
     */
    public function __construct(Collection $leads)
    {
        $this->leads = $leads;
        $this->nodes = new Collection();
        $this->left = 0;
    }

    public function execute()
    {
        $coupletNumber = $this->leads->first()['from'];

        $this->getNodes(['to_couplet' => $coupletNumber]);

        return $this->nodes->sortBy('left');
    }

    /**
     * @param mixed $lead
     * @return void
     */
    private function getNodes($lead)
    {

        $leads = $this->leads->filter(fn ($item) => $item['from'] == $lead['to_couplet'])->sortBy('size');

        foreach ($leads as $next) {
            $this->left++;
            $node = $next;
            $node['left'] = $this->left;

            if ($next['to_couplet']) {
                $this->getNodes($next);
            }

            $this->left++;
            $node['right'] = $this->left;
            $this->nodes->push($node);
        }
    }

}
