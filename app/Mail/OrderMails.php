<?php

namespace App\Mail;

use App\Setting;
use App\Sale;
use App\SaleRelation;
use App\User;
use App\UserNoregist;
use App\Receiver;
use App\MailTemplate;
use App\Item;
use App\PayMethod;
use App\PayMethodChild;
use App\DeliveryCompany;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderMails extends Mailable
{
    use Queueable, SerializesModels;

    //public $saleId;
	public $saleIds;
    public $mailId;
    public $isForward;
    
//    public $user;
//    public $receiver;
    
    public $setting;
    public $pmModel;
    public $pmChildModel;
    public $itemModel;
    public $dcModel;
    
    public $mailTemplate;
    
    
    public function __construct($saleIds, $mailId, $isForward=0)
    {
        $this->setting = Setting::get()->first();
        
        //$this->saleId = $saleId;
        $this->saleIds = $saleIds; 
        $this->mailId = $mailId;
        
        $this->isForward = $isForward;
        
//        echo $this->isForward;
//        exit;

        
//        $this->saleRel = SaleRelation::find($saleRelId);
//        $this->sales = Sale::where(['salerel_id'=>$this->saleRel->id])->get();       

//        $this->pmModel = new PayMethod;
//        $this->itemModel = new Item;
        
        //$this->isUser = $isUser;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
    	$this->pmModel = new PayMethod;
        $this->pmChildModel = new PayMethodChild;
        $this->itemModel = new Item;
        $this->dcModel = new DeliveryCompany;
        
        //$templ = MailTemplate::where(['type_code'=>'itemDelivery', ])->get()->first();
        $templ = MailTemplate::find($this->mailId);

		//転送メールのタイトルに「転送」の文字列を付ける
        
        $forwardTitle = $this->isForward ? '転送-' : '';
        
        //$thisSale = Sale::find($this->saleId);
        
        $sales = Sale::find($this->saleIds);
        $saleRelId = $sales->first()->salerel_id;
        
        $saleRel = SaleRelation::find($saleRelId);
        
        if($saleRel->is_user) 
            $user = User::find($saleRel->user_id);
        else
            $user = UserNoregist::find($saleRel->user_id);
        

        $receiver = Receiver::find($saleRel->receiver_id);

        //return $this->from($this->setting->admin_email, $this->setting->admin_name)
        return $this->from('no-reply@green-rocket.jp', $this->setting->admin_name)
                    ->view('emails.orderMails')
                    ->with([
                    	'templ' => $templ, //ここをコメントアウトすればFailedJobの確認ができる。errorメールの送信はProviders/AppServiceProvider内にて
                        //'header' => $templ->header,
                        //'footer' => $templ->footer, 
                        //'thisSale' => $thisSale,
                        'sales' => $sales,
                        'saleRel' => $saleRel,
                        'user' => $user,
                        'receiver' => $receiver,
                        'isUser' => 1,        
                    ])
                    ->subject($forwardTitle . $templ->title);
    }
}
