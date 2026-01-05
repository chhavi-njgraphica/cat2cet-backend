<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class UserExport implements FromView
{
    public $results;

    public function __construct($results)
    {
        $this->results = $results;
    }

    public function view(): View
    {
        return view('backend.user.export_all_user', [
            'results' => $this->results
        ]);
    }
}
