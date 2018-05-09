<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExampleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    //

    public function showVersion(Request $request)
    {
        return response()->json([
            "message" => "Hello",
            "path" => $request->query('name')
            ]);
    }
}
