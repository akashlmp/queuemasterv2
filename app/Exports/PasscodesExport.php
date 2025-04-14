<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class PasscodesExport implements FromCollection
{
    protected $passcodes;

    public function __construct(array $passcodes)
    {
        $this->passcodes = $passcodes;
    }
    public function collection()
    {
        // Prepare data including column header
        $data = [];
        $data[] = ['Passcodes']; // Column header
        foreach ($this->passcodes as $passcode) {
            $data[] = [$passcode->pass_code];
        }

        // Format data into a collection
        return collect($data);
    }
}
