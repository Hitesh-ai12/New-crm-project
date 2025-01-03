<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{

    // Featch type , source , tags here 
    public function index()
    {
        $tags = Item::where('type', 'tag')->get();
        $stages = Item::where('type', 'stage')->get();
        $sources = Item::where('type', 'source')->get();
    
        return response()->json([
            'tags' => $tags,
            'stages' => $stages,
            'sources' => $sources,
        ]);
    }

    // Store type , source , tags here 
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:tag,stage,source',
        ]);
    
        $item = Item::create($request->only(['name', 'type']));
    
        return response()->json(['success' => true, 'item' => $item]);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);
    
        $item = Item::findOrFail($id);
        // Update the item's name with the validated data
        $item->update($validatedData);
        // Return a success response with the updated item
        return response()->json([
            'success' => true,
            'item' => $item
        ]);
    }
    
    public function destroy($id)
    {
        // Find the item by its ID, if not found return a 404 error
        $item = Item::findOrFail($id);
    
        // Delete the item
        $item->delete();
    
        // Return a success response with a deletion message
        return response()->json([
            'success' => true,
            'message' => 'Item deleted successfully'
        ]);
    }
    
}
