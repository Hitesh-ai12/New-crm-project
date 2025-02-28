<?php

namespace App\Http\Controllers;
use App\Models\Signature;
use Illuminate\Http\Request;

class SignatureController extends Controller
{
       public function index()
       {
           return Signature::all();
       }
   
       public function store(Request $request)
       {
           $request->validate([
               'name' => 'required|string|max:255',
               'content' => 'required|string',
               'is_default' => 'boolean',
           ]);
   
           $signature = Signature::create([
               'name' => $request->name,
               'content' => $request->content,
               'is_default' => $request->is_default,
           ]);
   
           return response()->json($signature, 201);
       }

       public function update($id, Request $request) {
            $signature = Signature::find($id);
            if (!$signature) {
                return response()->json(['message' => 'Signature not found'], 404);
            }
            
            $signature->update($request->all());
            
            return response()->json($signature, 200);
        }   

  
       public function setDefault(Request $request)
       {
           $signature = Signature::find($request->id);
           if ($signature) {
               Signature::where('is_default', true)->update(['is_default' => false]);
               $signature->update(['is_default' => true]);
           }
   
           return response()->json($signature);
       }
   
       public function destroy($id)
       {
           $signature = Signature::find($id);
           if ($signature) {
               $signature->delete();
           }
   
           return response()->json(['message' => 'Signature deleted successfully.']);
       }
}
