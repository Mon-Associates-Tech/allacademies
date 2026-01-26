<?php

namespace App\Http\Controllers;

use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SchoolSettingsController extends Controller
{
    public function index()
    {
        $settings = SchoolSetting::getGrouped();

        return view('school-settings.index', compact('settings'));
    }

    public function create()
    {
        return view('school-settings.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|unique:school_settings,key|max:255',
            'type' => 'required|in:text,longtext,image,json,pdf,boolean,number,select,radio',
            'label' => 'required|string|max:255',
            'description' => 'nullable|string',
            'group' => 'required|string|max:255',
            'options' => 'nullable|array',
            'required' => 'boolean',
            'sort_order' => 'integer',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        SchoolSetting::create($request->all());

        return redirect()->route('school-settings.index')
            ->with('success', 'Setting created successfully.');
    }

    public function edit(SchoolSetting $schoolSetting)
    {
        return view('school-settings.edit', compact('schoolSetting'));
    }

    public function update(Request $request, SchoolSetting $schoolSetting)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:255|unique:school_settings,key,'.$schoolSetting->id,
            'type' => 'required|in:text,longtext,image,json,pdf,boolean,number,select,radio',
            'label' => 'required|string|max:255',
            'description' => 'nullable|string',
            'group' => 'required|string|max:255',
            'options' => 'nullable|array',
            'required' => 'boolean',
            'sort_order' => 'integer',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $schoolSetting->update($request->all());

        return redirect()->route('school-settings.index')
            ->with('success', 'Setting updated successfully.');
    }

    public function updateValue(Request $request, SchoolSetting $schoolSetting)
    {
        $rules = [];

        switch ($schoolSetting->type) {
            case 'image':
                $rules['value'] = 'image|mimes:jpeg,png,jpg,gif|max:2048';
                break;
            case 'pdf':
                $rules['value'] = 'mimes:pdf|max:10240';
                break;
            case 'json':
                $rules['value'] = 'json';
                break;
            case 'number':
                $rules['value'] = 'numeric';
                break;
            case 'boolean':
                $rules['value'] = 'boolean';
                break;
            default:
                $rules['value'] = $schoolSetting->required ? 'required' : 'nullable';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $value = $request->input('value');

        // Handle file uploads
        if ($request->hasFile('value')) {
            // Delete old file if exists
            if ($schoolSetting->raw_value) {
                Storage::delete($schoolSetting->raw_value);
            }

            $path = $request->file('value')->store('settings', 'public');
            $value = $path;
        }

        $schoolSetting->update(['value' => $value]);

        return redirect()->route('school-settings.index')
            ->with('success', 'Setting value updated successfully.');
    }

    public function destroy(SchoolSetting $schoolSetting)
    {
        // Delete associated file if exists
        if (in_array($schoolSetting->type, ['image', 'pdf']) && $schoolSetting->raw_value) {
            Storage::delete($schoolSetting->raw_value);
        }

        $schoolSetting->delete();

        return redirect()->route('school-settings.index')
            ->with('success', 'Setting deleted successfully.');
    }
}
