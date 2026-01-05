<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class StudentResultExport implements FromView
{
    public $results;

    public function __construct($results)
    {
        $this->results = $results;
    }

    public function view(): View
    {
        return view('backend.result.export_all_students_result', [
            'results' => $this->results
        ]);
    }
}
