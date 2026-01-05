<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\XatCollege;
use Gate;

class XatCollegeController extends Controller
{
    public function index()
    {
        $colleges = XatCollege::all();
        return view('backend.xat-college.index', compact('colleges'));
    }

    public function create()
    {
        $colleges = XatCollege::all();
        return view('backend.xat-college.manage', compact('colleges'));
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

        XatCollege::create($data);

        return redirect()->route('backend.xat-colleges.index')
            ->with('success','Data submitted successfully');

    }

    public function edit(XatCollege $college, $id)
    {
        $college = XatCollege::where('id', $id)->first();
        return view('backend.xat-college.manage', compact('college'));
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

        $college = XatCollege::findOrFail($id);

        $college->update($data);

        return redirect()->route('backend.xat-colleges.index')
            ->with('success','Data updated successfully');
    }

}
