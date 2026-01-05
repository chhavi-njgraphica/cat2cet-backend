<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserCatResult;
use App\Models\College;
use Gate;

class CollegeController extends Controller
{
    public function index()
    {
        $colleges = College::all();
        return view('backend.college.index', compact('colleges'));
    }

    public function create()
    {
        $colleges = College::all();
        return view('backend.college.manage', compact('colleges'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'percentile_between' => 'required|string',
            'college_name' => 'required|string',
            'overall_percentile' => 'nullable|string',
            'fees' => 'nullable|string',
            'highest_package' => 'nullable|string',
            'average_package' => 'nullable|string',
            'deadline' => 'nullable|string',
            'whatsapp_no' => 'nullable|string',
        ]);

        College::create($data);

        return redirect()->route('backend.colleges.index')
            ->with('success','Data submitted successfully');

    }

    public function edit(College $college)
    {
        return view('backend.college.manage', compact('college'));
    }

    public function update($id, Request $request)
    {
        $data = $request->validate([
            'percentile_between' => 'required|string',
            'college_name' => 'required|string',
            'overall_percentile' => 'nullable|string',
            'fees' => 'nullable|string',
            'highest_package' => 'nullable|string',
            'average_package' => 'nullable|string',
            'deadline' => 'nullable|string',
            'whatsapp_no' => 'nullable|string',
        ]);

        $college = College::findOrFail($id);

        $college->update($data);

        return redirect()->route('backend.colleges.index')
            ->with('success','Data updated successfully');
    }

}
