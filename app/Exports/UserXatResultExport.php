<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class UserXatResultExport implements FromView
{
    protected $results;

    public function __construct($results)
    {
        $this->results = $results;
    }

    public function view(): View
    {
        return view('backend.result.export', [
            'results' => $this->results
        ]);
    }
}
