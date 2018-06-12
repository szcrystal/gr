<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
    	'salerel_id',
		'order_number',
        
        'item_id', 
        'item_count', 
        
        /*
        'regist',
        'user_id',
        'is_user',
        'receiver_id',
        */

        'pay_method',
        'deli_fee',
        'cod_fee',
        'use_point',
        'total_price',
        
        'deli_done',
        'deli_date',
        'pay_done',
        
        /*
        'destination',
        
        'mail_done',
        
        'pay_trans_code',
        'pay_user_id',
        'pay_order_number',
        'pay_payment_code', //ネットバンク、GMO後払いのみ  
    	'pay_result', //クレカのみ
        'pay_state',
        */

    ];
}
