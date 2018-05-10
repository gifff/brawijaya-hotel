<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Room;
use App\RoomType;
use App\Reservation;

class ReservationController extends Controller
{
  /**
   * Create a Reservation controller instance.
   * 
   * @return void
   */
  public function __construct() {

  }

  public function create(Request $request)
  {

    if (!$request->isJson())
    {
      return abort(400, 'Invalid content-type');
    }

    $min_reservation_date = date('Y-m-d', time() + 86400);
    $max_reservation_date = date('Y-m-d', time() + (30 * 86400));

    $validator = Validator::make($request->json()->all(), [
      'customer_name' => 'required|string|max:64',
      'customer_nin' => 'required|numeric|min:0|max:99999999999999999999',
      'phone' => 'required|numeric|min:0|max:999999999999',
      'check_in' => 'required|date_format:Y-m-d|after_or_equal:'.$min_reservation_date.'|before_or_equal:'.$max_reservation_date,
      'check_out' => 'required|date_format:Y-m-d|after:check_in',
      'adult_capacity' => 'required|numeric|min:0',
      'children_capacity' => 'required|numeric|min:0',
      'rooms' => 'min:1|required|array',
      'rooms.*.id' => 'required|numeric|min:0',
      'rooms.*.extra_bed' => 'required|boolean'
    ]);

    if ($validator->fails())
    {
      throw new ValidationException($validator);
    }

    $body = (object) $request->json()->all();
    
    // check for capacity
    $adult_count = $body->adult_capacity;
    $children_count = $body->children_capacity;
    foreach($body->rooms as $room)
    {
      $adult_count -= 2;
      $children_count -= 2;
      
      if ($room['extra_bed'])
      {
        if ($adult_count > $children_count)
        {
          $adult_count -= 1;
        } else {
          $children_count -= 2;
        }
      }
    }

    if ($adult_count > 0 || $children_count > 0)
    {
      abort(403, 'Capacity is not enough');
    }

    // check for invalid room id

    $rooms = Room::select('id')->get();
    $room_ids = [];
    foreach($rooms as $room) {
      $room_ids[] = $room->id;
    }

    foreach($body->rooms as $room) {
      if ( !in_array($room['id'], $room_ids, true))
      {
        abort(403, "Invalid room_id '".$room['id']."'. Room does not exist.");
      }
    }

    $occupied_rooms = Room::join('reservation_rooms', 'reservation_rooms.room_id', '=', 'rooms.id')
      ->join('reservations', 'reservations.id', '=', 'reservation_rooms.reservation_id')
      ->select('rooms.id')
      ->distinct()
      ->where([
        ['reservations.check_in', '>=', $body->check_in],
        ['reservations.check_in', '<=', $body->check_out]
      ])
      ->orWhere([
        ['reservations.check_out', '>=', $body->check_in],
        ['reservations.check_out', '<=', $body->check_out]
      ])
      ->get();
    $occupied_room_ids = [];
    foreach($occupied_rooms as $room)
    {
      $occupied_room_ids[] = $room->id;
    }
    $taken_ids = [];
    foreach($body->rooms as $room)
    {
      if ( in_array($room['id'], $occupied_room_ids))
      {
        $taken_ids[] = $room['id'];
      }
    }

    if (count($taken_ids) > 0) {
      abort(403, json_encode(
        [
          'message' => 'Some rooms are occupied',
          'value' => $taken_ids
        ]
      ));
    }

    $reservation = new Reservation;
    $reservation->customer_name = $body->customer_name;
    $reservation->customer_nin = $body->customer_nin;
    $reservation->phone = $body->phone;
    $reservation->check_in = $body->check_in;
    $reservation->check_out = $body->check_out;
    $reservation->adult_capacity = $body->adult_capacity;
    $reservation->children_capacity = $body->children_capacity;
    $reservation->save();

    foreach($body->rooms as $room)
    {
      $reservation->rooms()->attach($room['id'], [
        'extra_bed' => $room['extra_bed']
      ]);
    }
    // $reservation = Reservation::where('id',$reservation->id)->get();
    $_rooms = [];
    // TODO: calculate total price
    foreach($reservation->rooms as $room)
    {
      $_rooms[] = [
        'id' => $room->id,
        'name' => $room->name,
        'type' => $room->type->name,
        'price' => $room->type->price,
        'extra_bed' => $room->pivot->extra_bed
      ];
    }

    $response = [
      'id' => $reservation->id,
      'customer_name' => $reservation->customer_name,
      'customer_nin' => $reservation->customer_nin,
      'phone' => $reservation->phone,
      'check_in' => $reservation->check_in,
      'check_out' => $reservation->check_out,
      'adult_capacity' => $reservation->adult_capacity,
      'children_capacity' => $reservation->children_capacity,
      'rooms' => $_rooms
    ];

    return response()->json([
      'data' => $response
    ]);
  }

  public function fetchAll()
  {
    $reservations = Reservation::all();
    // $reservations = Reservation::where([
    //   ['check_in', '>', date('Y-m-d')],
    //   ['check_in', '<=', date('Y-m-d', time() + 2592000)]
    // ])
    // ->orWhere([
    //   ['check_out', '>', date('Y-m-d')],
    //   ['check_out', '<=', date('Y-m-d', time() + 2592000)]
    // ])
    // ->get();
    $resp = [];
    foreach($reservations as $reservation) {
      $rooms = [];
      foreach($reservation->rooms as $room) {
        $rooms[] = [
          'name' => $room->name,
          'type' => $room->type->name,
          'price' => $room->type->price,
          'extra_bed' => $room->pivot->extra_bed
        ];
      }
      $resp[] = [
        'customer_name' => $reservation->customer_name,
        'customer_nin' => $reservation->customer_nin,
        'phone' => $reservation->phone,
        'check_in' => $reservation->check_in,
        'check_out' => $reservation->check_out,
        'adult_capacity' => $reservation->adult_capacity,
        'children_capacity' => $reservation->children_capacity,
        'rooms' => $rooms
      ];
    }
    return response()->json($resp);
  }

  public function fetchOne(Request $request, $id)
  {
    return response()->json([
      "message" => "Hello",
      "id" => $id
    ]);
  }
}