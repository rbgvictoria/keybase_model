<?php

namespace App\Actions\Exports;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;


class CreateZipArchive
{
    public function execute($slug, $delete=false) 
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
