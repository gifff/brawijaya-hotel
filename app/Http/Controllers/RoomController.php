<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Room;
use App\RoomType;
use App\Reservation;

class RoomController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  { }

  public function fetchAll()
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
    return response()->json([
      'data' => $returned_rooms
      ]);
  }

  public function fetchOne(Request $request, $room_id)
  {
    $room = Room::where('id', $room_id)->first();

    if ($room == NULL)
    {
      return abort(404, "Room not found");
    }

    return response()->json([
      "data" => [
        'name' => $room->name,
        'type' => $room->type->name,
        'price' => $room->type->price,
        'holiday_price' => $room->type->price * 1.25
      ]
    ]);

  }
}
