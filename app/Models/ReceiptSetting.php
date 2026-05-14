<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceiptSetting extends Model
{
   protected $fillable = [
       'cafe_name',
       'address',
       'phone',
       'footer_message',
       'wifi_name',
       'wifi_password',
       'logo',
   ];
}
