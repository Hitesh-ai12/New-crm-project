<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ColumnSetting;
use Illuminate\Support\Facades\Auth;

class ColumnSettingController extends Controller
{
    public function getColumnSettings()
    {
        $userId = Auth::id();

        $settings = ColumnSetting::where('user_id', $userId)->first();

        return response()->json([
            'selectedColumns' => $settings ? $settings->selected_columns : ["first_name", "email", "phone"]
        ]);
    }


    public function saveColumnSettings(Request $request)
    {
        $userId = Auth::id();

        $request->validate([
            'selectedColumns' => 'required|array',
        ]);

        ColumnSetting::updateOrCreate(
            ['user_id' => $userId],
            ['selected_columns' => $request->selectedColumns]
        );

        return response()->json(['message' => 'Column settings saved successfully.']);
    }

    // Save column order settings (when drag ends)
    public function saveColumnOrderSettings(Request $request)
    {
        $userId = Auth::id();

        $request->validate([
            'selectedColumns' => 'required|array',
        ]);

        // Update the column order for the user
        ColumnSetting::updateOrCreate(
            ['user_id' => $userId],
            ['selected_columns' => $request->selectedColumns]
        );

        return response()->json(['message' => 'Column order saved successfully.']);
    }
}
