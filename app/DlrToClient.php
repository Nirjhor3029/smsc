<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DlrToClient extends Model
{
    protected $fillable = [
        'is_ldr_sent',
        't_msg_id',
        'dlr_status',
        'sms_id'
    ];
}
