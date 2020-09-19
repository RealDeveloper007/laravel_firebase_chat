<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Mpociot\Firebase\SyncsWithFirebase;

class Chat extends Model
{
	use SyncsWithFirebase;
    //
    
    protected $fillable = [
        'from_id', 'to_id','body','date','time','is_new'
    ];

    public function getfromdetailsAttribute()
    {
        $from = User::find($this->from_id);
        $FromDetails = new \stdClass();
        $FromDetails->name = $from->name;
        $FromDetails->profile_img = $from->profile_img;
        return $FromDetails;
    }

    public function gettodetailsAttribute()
    {
        $to_id = User::find($this->to_id);

        $ToDetails = new \stdClass();
        $ToDetails->name = $to_id->name;
        $ToDetails->profile_img = $to_id->profile_img;
        return $ToDetails;
    }
}
