<?php

namespace App\Core;

class Csv
{
    /** Streams $rows as a CSV download (only $columns, in order) and exits. */
    public static function export(string $filename, array $columns, array $rows): void
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $out = fopen('php://output', 'w');
        fputcsv($out, $columns);
        foreach ($rows as $row) {
            $line = [];
            foreach ($columns as $col) {
                $line[] = $row[$col] ?? '';
            }
            fputcsv($out, $line);
        }
        fclose($out);
        exit;
    }

    /**
     * Parses an uploaded CSV ($_FILES entry) into a list of associative rows
     * keyed by its header row. Returns [] if the file is missing or unreadable.
     */
    public static function parseUpload(?array $file): array
    {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            return [];
        }

        $handle = fopen($file['tmp_name'], 'r');
        if (!$handle) {
            return [];
        }

        $header = fgetcsv($handle);
        if ($header === false) {
            fclose($handle);
            return [];
        }
        $columnCount = count($header);

        $rows = [];
        while (($data = fgetcsv($handle)) !== false) {
            if ($data === [null]) {
                continue; // blank line
            }
            $data = array_slice(array_pad($data, $columnCount, null), 0, $columnCount);
            $rows[] = array_combine($header, $data);
        }
        fclose($handle);

        return $rows;
    }
}
