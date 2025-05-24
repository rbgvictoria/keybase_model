# Processing keys

## Anatomy of an identification key

![alt text](image.png)

<caption>

**Figure 1.** Bracketed key from **KeyBase (2025)**. Flora of Victoria: Key to
_Acaulon_ species. &lt;https://keybase.rbg.vic.gov.au/keys/show/12181&gt; [Seen:
24-05-2025].

</caption>


[[Example CSV import](./examples/key-import-example.csv)]

```php
[
    [
      "from" => 1,
      "text" => "Plants to 1 mm tall; lamellae absent; leaf margins recurved",
      "to" => 2,
    ],
    [
      "from" => 1,
      "text" => "Plants c. 2 mm tall; leaves with 2 or 3 irregular longitudinal lamellae (often inconspicuous) on the adaxial surface of the costa; margins not recurved",
      "to" => 3,
    ],
...,
    [
      "from" => 5,
      "text" => "Mature spores 30-50 µm diam., finely papillose; capsules usually orange; leaf margin usually entire",
      "to" => "Acaulon integrifolium",
    ],
    [
      "from" => 5,
      "text" => "Mature spores 50-65 µm diam., very coarsely granular; capsules ferrugineous to dark brown; leaf margin usually crenulate to irregularly dentate",
      "to" => "Acaulon granulosum",
    ],
  ]
```

![](./media/decision-tree-no-errors.drawio.svg) 

<caption>

**Figure ...** Graph of example key.

</caption>

![](./media/graph-leads.drawio.svg)

```bash
> $from = collect($inkey)->map(fn ($lead) => $lead['from'])->toArray();
= [
    1,
    1,
    2,
    2,
    3,
    3,
    4,
    4,
    5,
    5,
  ]
```

```bash
> array_count_values($from);
= [
    1 => 2,
    2 => 2,
    3 => 2,
    4 => 2,
    5 => 2,
  ]
```

```bash
> $to = collect($inkey)->map(fn ($lead) => $lead['to'])->toArray();
= [
    2,
    3,
    "Acaulon chrysacanthum",
    "Acaulon leucochaete",
    "Acaulon triquetrum",
    4,
    "Acaulon mediterraneum",
    5,
    "Acaulon integrifolium",
    "Acaulon granulosum",
  ]
```

```bash
> $toNodes = collect($to)->filter(fn ($item) => is_numeric($item))->toArray();
= [
    0 => 2,
    1 => 3,
    5 => 4,
    7 => 5,
  ]
```

```bash
> array_count_values($toNodes);
= [
    2 => 1,
    3 => 1,
    4 => 1,
    5 => 1,
  ]
```

```bash
> $toItems = collect($to)->filter(fn ($item) => !is_numeric($item))->toArray();
= [
    2 => "Acaulon chrysacanthum",
    3 => "Acaulon leucochaete",
    4 => "Acaulon triquetrum",
    6 => "Acaulon mediterraneum",
    8 => "Acaulon integrifolium",
    9 => "Acaulon granulosum",
  ]
```




## Deviations from ideal key structure



### Singleton couplets [**Error**]

![](./media/decision-tree-singleton.drawio.svg)

<caption>

**Figure ...** Graph of key with singleton couplet. [[Example CSV import](./examples/key-import-singleton-example.csv)]

</caption>

```bash
> array_count_values($from);
= [
    1 => 2,
    2 => 2,
    3 => 2,
    4 => 1,
    5 => 2,
  ]
```

### Polytomies [**Info**]

![](./media/decision-tree-polytomy.drawio.svg)

<caption>

**Figure ...** Graph of key with polytomy. [[Example CSV import](./media/decision-tree-polytomy.drawio.svg)]

</caption>

```bash
> array_count_values($from);
= [
    1 => 2,
    2 => 2,
    3 => 2,
    4 => 2,
    5 => 3, <--
  ]
```


### Reticulations [**Info**]

![](./media/decision-tree-reticulation.drawio.svg)

<caption>

**Figure ...** Graph of key with reticulation. [[Example CSV import](./examples/key-import-reticulation-example.csv)]

</caption>

```bash
> array_count_values($toNodes);
= [
    2 => 2,
    3 => 1,
    4 => 1,
    5 => 1,
  ]
```

![](./media/graph-reticulation-resolved.drawio.svg)

<caption>

**Figure ...** Graph of key with reticulation resolved by repeating the subgraph.

</caption>

![](./media/graph-reticulation-new-graph.drawio.svg)

<caption>

**Figure ...** Graph of key with reticulation resolved by starting a new graph.

</caption>


### Loops [**Error**]

![](./media/decision-tree-loop.drawio.svg)

<caption>

**Figure ...** Graph of key with loop. [[Example CSV
import](./examples/key-import-loop-example.csv)]

</caption>


### Orphans [**Error**]

![](./media/decision-tree-orphan.drawio.svg)

<caption>

**Figure ....** Graph of key with orphan couplet. [[Example CSV
import](./examples/key-import-orphan-example.csv)]

</caption>

```bash
> array_diff($from, $toNodes);
= [
    0 => 1,
    5 => 6,
  ]
```



### Dead ends [**Error**]

![](./media/decision-tree-dead-end.drawio.svg)

<caption>

**Figure ...** Graph of key with dead end. [[Example CSV import](./examples/key-import-dead-end-example.csv)]

</caption>

```bash
> array_diff($toNodes, $from);
= [
    5 => 7,
  ]
```


## Processing key files
