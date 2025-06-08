<?php

namespace App\Console\Commands;

use App\Actions\Exports\GetLeads;
use Illuminate\Console\Command;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Yaml\Yaml;
use ZipArchive;

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

        $projects = $this->getProjects($this->option('project'));

        foreach ($projects as $project) {
            $slug = Str::slug($project->title, '-');

            if (!is_dir(storage_path('app/public/exports/' . $slug))) {
                mkdir(storage_path('app/public/exports/' . $slug));
            }

            Storage::put('exports/' . $slug . '/project_metadata.yaml', Yaml::dump((array) $project));


            $keys = $this->getKeys($project->id);
            $firstKey = true;
            $keysFile = fopen(storage_path('app/public/exports/' . $slug . '/keys.tsv'), 'w');
            foreach ($keys as $key) {
                if ($firstKey) {
                    fputcsv($keysFile, array_keys((array) $key), $separator);
                    $firstKey = false;
                }

                $leads = (new GetLeads($key->id))->execute(balance: $this->option('balance'));
                if ($leads) {
                    $leadsFile = fopen(storage_path('app/public/exports/' . $slug . '/' . $key->key_file), 'w');
                    fputcsv($leadsFile, ['from', 'statement', 'to'], $separator);
                    foreach ($leads as $lead) {
                        fputcsv($leadsFile, array_values($lead), $separator);
                    }
                    fclose($leadsFile);
                }
                else {
                    $key->key_file = null;
                }

                fputcsv($keysFile, array_values((array) $key), $separator);
            }
            fclose($keysFile);


            $sources = $this->getSources($project->id);
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

            $items = $this->getItems($project->id);
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

            $this->createZipArchive($slug, $this->option('delete') !== false);
        }
    }

    private function getProjects($project=null)
    {
        $query = DB::connection('keybase_old')
            ->query()
            ->select(
                'p.ProjectsID as id',
                'p.Name as title',
                'p.description',
                'i.Name as item',
                'p.GeographicScope as spatial',
                'p.ProjectIcon as icon'
            )
            ->from('projects as p')
            ->leftJoin('items as i', 'p.TaxonomicScopeID', '=', 'i.ItemsID');
        
        if ($project) {
            $query->where('p.ProjectsID', $project);
        }

        return $query->get();
    }

    private function getKeys($project) {
        $keys = DB::connection('keybase_old')
            ->query()
            ->select(
                'k.KeysID as id',
                'k.TimestampCreated as created_at',
                'k.TimestampModified as updated_at',
                'k.Title as title',
                'k.Author as author',
                'k.Description as description',
                'i.Name as item',
                'k.GeographicScope AS spatial', 
                'k.Notes AS remarks',
                'k.SourcesID AS source_id',
                'u.Email AS created_by',
                'u2.Email as updated_by'
            )
            ->from('keys as k')
            ->leftJoin('items as i', 'k.TaxonomicScopeID', '=', 'i.ItemsID')
            ->leftJoin('users as u', 'k.CreatedByID', '=', 'u.UsersID')
            ->leftJoin('users as u2', 'k.ModifiedByID', '=', 'u2.UsersID')
            ->where('k.ProjectsID', $project)
            ->get();

        $keys = $keys->map(function ($key) {
            $key->key_file = Str::slug('key-' . $key->id . '-' . $key->title) . '.tsv';
            return $key;
        });

        return $keys;
    }

    private function getSources($project) 
    {
        return DB::connection('keybase_old')
            ->query()
            ->select(
                's.SourcesID as id',
                's.Authors as author',
                's.year',
                's.title',
                's.InAuthors as collection_authors',
                's.InTitle as collection_title',
                's.edition',
                's.series',
                's.volume',
                's.part',
                's.publisher',
                's.PlaceOfPublication as place_of_publication',
                's.pages',
                's.url'
            )
            ->from('sources as s')
            ->where('s.ProjectsID', '=', $project)
            ->get();
    }

    private function getItems($project)
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
            ->where('k.ProjectsID', '=', $project)
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
                    ->where('k.ProjectsID', '=', $project)
            )
            ->orderBy('name')
            ->get();

        return $items->map(fn ($item) => (array) $item);
    }

    private function createZipArchive($slug, $delete=false) 
    {
            $zipFilePath = storage_path('app/public/exports/' . $slug . '.zip');
            $zip = new ZipArchive;
            $zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

            $dir = storage_path('app/public/exports/' . $slug);

            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dir),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $filename = substr($filePath, strlen($dir) + 1);
                    $zip->addFile($filePath, $filename);
                }
            }
            $zip->close();

            if ($delete) {
                foreach ($files as $file) {
                    if (!$file->isDir()) {
                        unlink($file->getRealPath());
                    }
                }
                rmdir($dir);
            }
    }
}
