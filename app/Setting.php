<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [ //varchar:文字数
                    
        'admin_name',
        'admin_email' ,
        'mail_footer',
        'tax_per',
        'bank_info',

    ];
}
