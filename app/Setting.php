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
        'cot_per',
        'snap_primary',
        'snap_secondary',
        'snap_category',
        
        'meta_title',
        'meta_description',
        'meta_keyword',

    ];
}
