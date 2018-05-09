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

}
