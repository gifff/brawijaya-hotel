<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class RoomType extends Model {

    protected $fillable = [
        'name',
        'price'
    ];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    // Relationships
    public function rooms() {
        return $this->hasMany('App\Room', 'type_id');
    }

}
