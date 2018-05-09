<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Room;
use App\RoomType;

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

    public function allRooms()
    {
        $rooms = Room::all();
        $returned_rooms = [];
        foreach($rooms as $room) {
            $returned_rooms[] = [
                'name' => $room->name,
                'type' => $room->type->name,
                'price' => $room->type->price
            ];
        }
        return response()->json($returned_rooms);
    }

    public function showVersion(Request $request)
    {
        return response()->json([
            "message" => "Hello",
            "path" => $request->query('name')
            ]);
    }
}
