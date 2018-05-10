<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Room extends Model {

    protected $fillable = [
        'name'
    ];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    // Relationships
    public function type() {
        return $this->belongsTo('App\RoomType', 'type_id');
    }

    public function reservations() {
        return $this->belongsToMany('App\Reservation')
            ->withPivot('extra_bed')
            ->withTimestamps();
    }

}
