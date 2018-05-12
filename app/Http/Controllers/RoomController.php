<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
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

  public function fetchAll(Request $request)
  {
    $start_date = $request->query('start');
    $end_date = $request->query('end');

    $validator = Validator::make($request->query(), [
      'start' => 'required_with_all:end|date_format:Y-m-d',
      'end' => 'required_with_all:start|date_format:Y-m-d|after:start'
    ]);
  
    if ($validator->fails())
    {
      throw new ValidationException($validator);
    }
    $rooms = Room::all();
    $available_rooms = [];

    if(isset($start_date) && isset($end_date)) 
    {
      $occupied_rooms = Room::join('reservation_rooms', 'reservation_rooms.room_id', '=', 'rooms.id')
      ->join('reservations', 'reservations.id', '=', 'reservation_rooms.reservation_id')
      ->select('rooms.*')
      ->distinct()
      ->where([
        ['reservations.check_in', '>=', $start_date],
        ['reservations.check_in', '<=', $end_date]
      ])
      ->orWhere([
        ['reservations.check_out', '>=', $start_date],
        ['reservations.check_out', '<=', $end_date]
      ])
      ->get();

      if (count($occupied_rooms) > 0) {
        $occupied_room_ids = [];
        foreach($occupied_rooms as $o_room) 
        {
          $occupied_room_ids[] = $o_room->id;
        }
        foreach($rooms as $room)
        {
          if (!in_array($room->id, $occupied_room_ids))
          {
            $available_rooms[] = $room;
          }
        }
      } else {
        $available_rooms = $rooms;
      }
    } else {
      $available_rooms = $rooms;
    }
    
    $returned_rooms = [];
    foreach($available_rooms as $room) {
      $returned_rooms[] = [
        'id' => $room->id,
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
