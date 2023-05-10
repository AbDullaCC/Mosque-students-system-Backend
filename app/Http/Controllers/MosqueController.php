<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Mosque;

class MosqueController extends Controller
{
    public function create(Request $request){
        
        //if this account isn't an owner
        if(auth()->user()->role != 'owner'){
            return response()->json([
                'message' => 'error',
                'error' => 'This account isn\'t an owner, you can\'t create a new mosque.'
            ],403);
        }

        try{
            $info = $request->validate([
                'name' => 'required|min:1|max:20|unique:mosques'
            ]);
        }
        catch(ValidationException $e){
            return response()->json([
                'message' => 'error',
                'error' => $e->getMessage()
            ],400);
        }

        $mosque = new Mosque($info);
        $mosque['owner_id'] = auth()->user()->id;
        $mosque->save();

        return response()->json([
            "The new mosque '$mosque->name' was created successfully."
        ],200);

    }
}
