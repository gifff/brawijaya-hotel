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
    $this->holidays = [
      [ "date" => 1514764800, "title" => "Tahun Baru Masehi" ],
      [ "date" => 1518739200, "title" => "Tahun Baru Imlek" ],
      [ "date" => 1521244800, "title" => "Hari Raya Nyepi" ],
      [ "date" => 1522368000, "title" => "Jumat Agung" ],
      [ "date" => 1523664000, "title" => "Isra Mi'raj" ],
      [ "date" => 1525132800, "title" => "Hari Buruh" ],
      [ "date" => 1525910400, "title" => "Kenaikan Isa Almasih" ],
      [ "date" => 1527552000, "title" => "Hari Waisak" ],
      [ "date" => 1527811200, "title" => "Hari Kesaktian Pancasila" ],
      [ "date" => 1528675200, "title" => "Cuti Bersama Lebaran" ],
      [ "date" => 1528761600, "title" => "Cuti Bersama Lebaran" ],
      [ "date" => 1528848000, "title" => "Cuti Bersama Lebaran" ],
      [ "date" => 1528934400, "title" => "Cuti Bersama Lebaran" ],
      [ "date" => 1529020800, "title" => "Hari Raya Idul Fitri" ],
      [ "date" => 1529280000, "title" => "Cuti Bersama Lebaran" ],
      [ "date" => 1529366400, "title" => "Cuti Bersama Lebaran" ],
      [ "date" => 1529452800, "title" => "Cuti Bersama Lebaran" ],
      [ "date" => 1534464000, "title" => "Hari Kemerdekaan" ],
      [ "date" => 1534896000, "title" => "Idul Adha" ],
      [ "date" => 1536624000, "title" => "Tahun Baru Islam" ],
      [ "date" => 1542672000, "title" => "Maulid Nabi Muhammad SAW" ],
      [ "date" => 1545609600, "title" => "Cuti Bersama Hari Natal" ],
      [ "date" => 1545696000, "title" => "Hari Natal" ]
    ];
  }

  private function count_weekend($date_a, $date_b) {
    $start = new \DateTime($date_a);
    $end = new \DateTime($date_b);
    $interval = new \DateInterval('P1D');
    $end->add($interval);
    $daterange = new \DatePeriod($start, $interval ,$end);
    $weekends = 0;
    foreach($daterange as $date){
        $day = $date->format('N');
        if ($day >= 6) {
            $weekends++;
        }
    }
    return $weekends;
  }

  public function publicHoliday(Request $request)
  {
    $resp = [];
    foreach($this->holidays as $holiday) 
    {
      $resp[] = [
        "date" => \strftime("%Y-%m-%d", $holiday['date']),
        "title" => $holiday['title']
      ];
    }
    return response()->json([
      'data' => $resp
    ]);
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

    // Calculate Price

    $price = 0;
    $check_in = strtotime($reservation->check_in);
    $check_out = strtotime($reservation->check_out);

    $duration = ($check_out - $check_in)/86400; // in day(s)
    $holiday_count = $this->count_weekend($reservation->check_in, $reservation->check_out);
    foreach($this->holidays as $holiday)
    {
      if ($reservation->check_in <= $holiday["date"] && $holiday["date"] <= $reservation->check_out )
      {
        $holiday_count++;
      }
    }
    // Extra Bed = 500.000
    foreach($reservation->rooms as $room)
    {
      $_rooms[] = [
        'id' => $room->id,
        'name' => $room->name,
        'type' => $room->type->name,
        'price' => $room->type->price,
        'extra_bed' => $room->pivot->extra_bed
      ];
      $price += $room->type->price * $duration;
      if ($room->pivot->extra_bed)
      {
        $price += 50000 * $duration;
      }

      $price += 0.25 * $room->type->price * $holiday_count;
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
      'rooms' => $_rooms,
      'total_price' => $price
    ];

    return response()->json([
      'data' => $response
    ]);
  }

  public function fetchAll()
  {
    $reservations = Reservation::all();

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
        'id' => $reservation->id,
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
    return response()->json([
      'data' => $resp
    ]);
  }

  public function fetchOne(Request $request, $reservation_id)
  {
    $reservation = Reservation::where("id", $reservation_id)->first();
    if ($reservation == NULL)
    {
      return abort(404, "Reservation not found");
    }
    $rooms = [];
    foreach($reservation->rooms as $room) {
      $rooms[] = [
        'name' => $room->name,
        'type' => $room->type->name,
        'price' => $room->type->price,
        'extra_bed' => $room->pivot->extra_bed
      ];
    }
    return response()->json([
      'data' => [
        'customer_name' => $reservation->customer_name,
        'customer_nin' => $reservation->customer_nin,
        'phone' => $reservation->phone,
        'check_in' => $reservation->check_in,
        'check_out' => $reservation->check_out,
        'adult_capacity' => $reservation->adult_capacity,
        'children_capacity' => $reservation->children_capacity,
        'rooms' => $rooms
      ]
    ]);
  }
}