<?php

namespace App\Actions;

use SplFileObject;

class GetCsvDelimiter
{
    /**
     * https://stackoverflow.com/questions/26717462/php-best-approach-to-detect-csv-delimiter#answer-55521117
     * 
     * @param string $filePath
     * @param int $checkLines
     * @return string
     */
    public function __invoke(string $filePath, int $checkLines = 3): string
    {
        $delimiters =[",", ";", "\t"];

        $default =",";

        $fileObject = new SplFileObject($filePath);
        $results = [];
        $counter = 0;
        while ($fileObject->valid() && $counter <= $checkLines) {
            $line = $fileObject->fgets();
            foreach ($delimiters as $delimiter) {
                $fields = explode($delimiter, $line);
                $totalFields = count($fields);
                if ($totalFields > 1) {
                    if (!empty($results[$delimiter])) {
                        $results[$delimiter] += $totalFields;
                    } else {
                        $results[$delimiter] = $totalFields;
                    }
                }
            }
            $counter++;
        }

        if (!empty($results)) {
            $results = array_keys($results, max($results));

            return $results[0];
        }
        return $default;
    }
}

