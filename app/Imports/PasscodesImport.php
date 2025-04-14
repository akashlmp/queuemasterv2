<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class PasscodesImport implements ToCollection
{
    protected $bypass_tamp_id;

    public function __construct($bypass_tamp_id)
    {
        $this->bypass_tamp_id = $bypass_tamp_id;
    }

    public function collection(Collection $rows)
    {
        $bypass_tamp_id = $this->bypass_tamp_id;

        $rowCount = count($rows);

        for ($i = 1; $i < $rowCount; $i++) {
            $passcode = $rows[$i][0];

            // Execute raw SQL insert query
            $query = "INSERT INTO bypass_pass_codes (pass_code, bypass_tamp_id) VALUES ('{$passcode}', '{$bypass_tamp_id}')";
            DB::insert($query);
        }
    }
}
