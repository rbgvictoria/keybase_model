<?php

namespace App\Actions;

class ProcessKey
{
    private $keyId;
    private $inKey;

    private $from;
    private $to;
    private $toCouplets;
    private $toItems;
    private $subkeyLabels;
    private $reticulations;

    /**
     * Create a new class instance.
     */
    public function __construct(int $keyId, array $inKey)
    {
        $this->keyId = $keyId;
        $this->inKey = $inKey;

        $this->from = collect($this->inKey)->map(fn ($lead) => $lead['from'])->toArray();
        $this->to = collect($this->inKey)->map(fn ($lead) => $lead['to'])->toArray();
        $this->toCouplets = collect($this->to)->filter(fn ($value) => is_numeric($value))->toArray();
        $this->reticulations = collect(array_unique($this->toCouplets))->filter(fn ($value) => $value > 1)->toArray();
        $this->subkeyLabels = [];
        if (collect($this->inKey)->filter(fn ($lead) => isset($lead['subkey']))->count()) {
            $this->subkeyLabels = collect($this->inkey)->map(fn ($lead) => $lead['subkey'])->unique()->values()->all();
        }
        $this->toItems = array_diff(collect($this->to)->filter(fn ($value) => is_numeric($value))->toArray(), $this->subkeyLabels);
    }

    public function execute()
    {

    }

    private function traverseKey()


}
