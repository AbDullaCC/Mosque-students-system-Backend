<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Mosque;

class MosqueController extends Controller
{
    public function create(Request $request){
        
        //is this account an owner?
        $this->authorize('owner',Mosque::class);

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
            'message' => 'The new mosque was created successfully.',
            'mosque' => $mosque
        ],200);

    }
}
