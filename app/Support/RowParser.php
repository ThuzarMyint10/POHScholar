<?php

namespace App\Support;

class RowParser
{
    /**
     * Turn parallel POST arrays (e.g. fam_name[], fam_relationship[]) into
     * a list of associative rows, dropping any row where every field is
     * blank. $fieldMap maps each POST field name to the key it should use
     * in the resulting row array, e.g.:
     *
     *   RowParser::fromRequest($request, [
     *       'fam_name' => 'name',
     *       'fam_relationship' => 'relationship',
     *   ]);
     *
     * Mirrors the original app's student/includes/row_helpers.php
     * parse_rows_from_post() so every portal form section behaves
     * identically.
     */
    public static function fromRequest(\Illuminate\Http\Request $request, array $fieldMap): array
    {
        $columns = [];
        $rowCount = 0;

        foreach ($fieldMap as $postField => $rowKey) {
            $values = (array) $request->input($postField, []);
            $columns[$rowKey] = $values;
            $rowCount = max($rowCount, count($values));
        }

        $rows = [];
        for ($i = 0; $i < $rowCount; $i++) {
            $row = [];
            $allBlank = true;
            foreach ($columns as $rowKey => $values) {
                $value = trim((string) ($values[$i] ?? ''));
                $row[$rowKey] = $value;
                if ($value !== '') {
                    $allBlank = false;
                }
            }
            if (! $allBlank) {
                $rows[] = $row;
            }
        }

        return $rows;
    }
}
