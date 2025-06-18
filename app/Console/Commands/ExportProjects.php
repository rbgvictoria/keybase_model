<?php

namespace App\Console\Commands;

use App\Actions\Exports\CreateZipArchive;
use App\Actions\Exports\GetItems;
use App\Actions\Exports\GetKeyMetadata;
use App\Actions\Exports\GetKey;
use App\Actions\Exports\GetProjects;
use App\Actions\Exports\GetSources;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;


class ExportProjects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:export-projects {--project=} {--balance} {--delete=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $separator = "\t";

        $projects = (new GetProjects($this->option('project')))->execute();

        foreach ($projects as $project) {
            $slug = Str::slug($project['title'], '-');

            if (!is_dir(storage_path('app/public/exports/' . $slug))) {
                mkdir(storage_path('app/public/exports/' . $slug));
            }

            file_put_contents(storage_path('app/public/exports/' . $slug . 
                    '/project_metadata.yaml'), Yaml::dump((array) $project));

            $keys = (new GetKeyMetadata($project['id']))->execute();
            $firstKey = true;
            $keysFile = fopen(storage_path('app/public/exports/' . $slug . '/keys.tsv'), 'w');
            foreach ($keys as $key) {
                if ($firstKey) {
                    fputcsv($keysFile, array_keys($key), $separator);
                    $firstKey = false;
                }

                $leads = (new GetKey($key['id']))->execute(balance: $this->option('balance'));
                if ($leads) {
                    $leadsFile = fopen(storage_path('app/public/exports/' . $slug . '/' . $key['key_file']), 'w');
                    foreach ($leads as $index => $lead) {
                        if ($index == 0) {
                            fputcsv($leadsFile, array_keys($lead), $separator);
                        }
                        fputcsv($leadsFile, array_values($lead), $separator);
                    }
                    fclose($leadsFile);
                }
                else {
                    $key['key_file'] = null;
                }

                fputcsv($keysFile, array_values((array) $key), $separator);
            }
            fclose($keysFile);


            $sources = (new GetSources($project['id']))->execute();
            $firstSource = true;
            $sourcesFile = fopen(storage_path('app/public/exports/' . $slug . '/sources.tsv'), 'w');
            foreach ($sources as $source) {
                if ($firstSource) {
                    fputcsv($sourcesFile, array_keys((array) $source), $separator);
                    $firstSource = false;
                }
                fputcsv($sourcesFile, array_values((array) $source), $separator);
            }
            fclose($sourcesFile);


            $items = (new GetItems($project['id']))->execute();
            $firstItem = true;
            $itemsFile = fopen(storage_path('app/public/exports/' . $slug . '/items.tsv'), 'w');
            foreach ($items as $item) {
                if ($firstItem){
                    fputcsv($itemsFile, array_keys($item), $separator);
                    $firstItem = false;
                }
                fputcsv($itemsFile, array_values($item), $separator);
            }
            fclose($itemsFile);


            (new CreateZipArchive)->execute($slug, $this->option('delete'));
        }
    }
}
