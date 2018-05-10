<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model {

    protected $fillable = [
        'customer_name',
        'customer_nin',
        'phone',
        'check_in',
        'check_out',
        'adult_capacity',
        'children_capacity'
    ];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    // Relationships

    public function rooms() {
        return $this->belongsToMany('App\Room', 'reservation_rooms', 'reservation_id', 'room_id')
            ->withPivot('extra_bed');
    }
}
