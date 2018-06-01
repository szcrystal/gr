<?php

namespace App\Http\Controllers\Cart;

use App\Item;
use App\Setting;
use App\User;
use App\UserNoregist;
use App\Sale;
use App\SaleRelation;
use App\Receiver;
use App\PayMethod;
use App\Prefecture;

use App\Mail\OrderEnd;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Ctm;
use Mail;
use Auth;

class CartController extends Controller
{
    public function __construct(Item $item, Setting $setting, User $user, UserNoregist $userNor, Sale $sale, SaleRelation $saleRel, Receiver $receiver, PayMethod $payMethod, Prefecture $prefecture)
    {
        
        //$this -> middleware('adminauth');
        //$this -> middleware('log', ['only' => ['getIndex']]);
        
        $this ->item = $item;
        $this ->setting = $setting;
        $this ->set = $this->setting->get()->first();
        
        $this->user = $user;
        $this->userNor = $userNor;
        $this->sale = $sale;
        $this->saleRel = $saleRel;
        $this->receiver = $receiver;
        $this->payMethod = $payMethod;
        $this-> prefecture = $prefecture;
//        $this->category = $category;
//        $this->categorySecond = $categorySecond;
//        $this -> tag = $tag;
//        $this->tagRelation = $tagRelation;
//        $this->consignor = $consignor;
//        
//        $this->perPage = 20;
        
        // URLの生成
        //$url = route('dashboard');
        
        /* ************************************** */
        //env()ヘルパー：環境変数（$_SERVER）の値を取得 .env内の値も$_SERVERに入る
    }
    
    
    
    public function index()
    {
        
        $itemObjs = Item::orderBy('id', 'desc')->paginate($this->perPage);
        
        $cates= $this->category;
        
        
        //$status = $this->articlePost->where(['base_id'=>15])->first()->open_date;
        
        return view('dashboard.item.index', ['itemObjs'=>$itemObjs, 'cates'=>$cates,  ]);
    }

    public function show($id)
    {
        $item = $this->item->find($id);
        $cates = $this->category->all();
        $subcates = $this->categorySecond->where(['parent_id'=>$item->cate_id])->get();
        $consignors = $this->consignor->all();
        //$users = $this->user->where('active',1)->get();
        
        $tagNames = $this->tagRelation->where(['item_id'=>$id])->get()->map(function($item) {
            return $this->tag->find($item->tag_id)->name;
        })->all();
        
        $allTags = $this->tag->get()->map(function($item){
            return $item->name;
        })->all();
        
        return view('dashboard.item.form', ['item'=>$item, 'cates'=>$cates, 'subcates'=>$subcates, 'consignors'=>$consignors, 'tagNames'=>$tagNames, 'allTags'=>$allTags, 'id'=>$id, 'edit'=>1]);
    }
   
    public function create()
    {
        $cates = $this->category->all();
        $consignors = $this->consignor->all();
        
        $allTags = $this->tag->get()->map(function($item){
            return $item->name;
        })->all();
//        $users = $this->user->where('active',1)->get();
        return view('dashboard.item.form', ['cates'=>$cates, 'consignors'=>$consignors, 'allTags'=>$allTags]);
    }
    
    public function getClear()
    {
    	$request->session()->forget('item');
        $request->session()->forget('all');
        
        return redirect('/');
    }
    
    public function getThankyou(Request $request)
    {
    	$data = $request->all();
     
//         print_r(session('all'));
//         print_r(session('item.data'));
//         exit;

		
        $itemData = session('item.data');
     	$all = session('all'); //session(all): regist, allPrice
      	$allData = $all['data']; //session(all.data): destination, pay_method, user, receiver  
     	
      	$regist = $all['regist']; 
       	$allPrice = $all['all_price']; 
        $deliFee = $all['deli_fee'];
        $codFee = $all['cod_fee'];
        $usePoint = $all['use_point'];      
      	$addPoint = $all['add_point'];
          
        $destination = isset($allData['destination']) ? 1 : 0;
        $pm = $allData['pay_method'];
        
        $userData = Auth::check() ? $this->user->find(Auth::id()) : $allData['user']; //session(all.data.user)
      	$receiverData = $allData['receiver']; //session('all.data.receiver');
      	
       
       	//print_r($userData);
		//exit;
    
      
      	//User登録処理
      	$userId = 0;
        $isUser = 0;
        
       	if(Auth::check()) { 
        	$uObj = $this->user->find(Auth::id());
        	$userId = $uObj->id;
         	
          	$uObj->point += $addPoint;
           	$uObj->save();
                     
         	$isUser = 1;   
        }
        else {   
            $userData['magazine'] = isset($userData['magazine']) ? $userData['magazine'] : 0;
            session('all.data.user.magazine', $userData['magazine']);
            
            if($regist) {   
                $userData['password'] = bcrypt($userData['password']);
      			$userData['point'] = $addPoint;
    //            $userData['email'] = 'co@frank.fam.cx';
                
                $user = $this->user;
                $user->fill($userData);
                $user->save();
                
                $userId = $user->id;
                $isUser = 1;
                //ポイントの処理が必要
            }
            else {
                $userNor = $this->userNor;
                $userNor->fill($userData);
                $userNor->save();
                
                $userId = $userNor->id;
            }
        } //AuthCheck
        
        
        //配送先登録 Receiver 別先であってもなくても登録
        $isEmpty = 1;
        foreach($receiverData as $receive) {
        	if(empty($receive)) { //空の時はTrueになる
         		$isEmpty = 0;
           		break;      
         	}    
        }
       
       //if($isEmpty && ! $destination) { //receiveDataが入力されている時
       $receiverData['user_id'] = $userId;
       $receiverData['regist'] = $regist;
       $receiverData['order_number'] = $all['order_number'];
       //$receiverData['is_user'] = $isUser;
       
       if($destination) {
       		$receiverData['name'] = $userData['name'];
         	$receiverData['hurigana'] = $userData['hurigana'];      
       		$receiverData['tel_num'] = $userData['tel_num'];
         	$receiverData['post_num'] = $userData['post_num'];
          	$receiverData['prefecture'] = $userData['prefecture'];
          	$receiverData['address_1'] = $userData['address_1'];
           	$receiverData['address_2'] = $userData['address_2']; 
            $receiverData['address_3'] = $userData['address_3'];               	  
        }
        
        $receiver = $this->receiver;
        $receiver->fill($receiverData);
        $receiver->save();
        
        $receiverId = $receiver->id;
        //配送先END -----------------------------------------------
   
       
       //paymentCode ネットバンクとGMOのみ
       $payPaymentCode = null;
       if($pm == 3) {
       		if($data['payment_code'] == 4)
         		$payPaymentCode = 'ジャパンネットバンク';
         	elseif($data['payment_code'] == 5) 
          		$payPaymentCode = '楽天銀行';
            elseif($data['payment_code'] == 17)  
            	$payPaymentCode = 'SBIネット銀行';     
       }
       elseif($pm == 4) {
       		$payPaymentCode = 'GMO後払い';
       }
       
       
       //SaleRelationのcreate
        $saleRel = $this->saleRel->create([
            'order_number' => $all['order_number'], //コンビニなし
            'regist' =>$all['regist'],
            'user_id' =>$userId,
            'is_user' => $isUser,
            'receiver_id' => $receiverId, 
            'pay_method' => $pm,
            
            'deli_fee' => $deliFee,
            'cod_fee' => $codFee,
            'use_point' => $usePoint,
            'all_price' => $allPrice,
            
            'destination' => $destination,
            'deli_done' => 0,
            'pay_done' => 0,
            
            'pay_trans_code' =>$data['trans_code'], //コンビニはこれのみ
            'pay_user_id' =>isset($data['user_id']) ? $data['user_id'] : null, //コンビニなし
            
            'pay_payment_code' => $payPaymentCode, //ネットバンク、GMO後払いのみ  
            'pay_result' => isset($data['result']) ? $data['result'] : null, //クレカのみ
            'pay_state' => isset($data['state']) ? $data['state'] : null,  //ネットバンク、GMO後払いのみ  
        
        ]);
        
        $saleRelId = $saleRel->id;
        
        $receiver->salerel_id = $saleRelId;
        $receiver->save();
    
    	$saleIds = array();
        //売上登録処理 Sale create
        foreach($itemData as $key => $val) {
            $sale = $this->sale->create(
                [
                	'salerel_id' => $saleRelId,
                	'order_number' => $all['order_number'], //コンビニなし
                    
                    'item_id' =>$val['item_id'],
                    'item_count' =>$val['item_count'], 
                    
                    'regist' =>$all['regist'],
                    'user_id' =>$userId,
                    'is_user' => $isUser,
                    'receiver_id' => $receiverId,
					
     				               
                    'pay_method' => $pm,
                    'deli_fee' => $deliFee,
                    'cod_fee' => 0,
                    'use_point' => 0,
                    'total_price' => $val['item_total_price'],
                    
                    'deli_done' => 0,
                    'pay_done' => 0,
                    
                    /*
                    'destination' => $destination,
                    
                    'pay_trans_code' =>$data['trans_code'], //コンビニはこれのみ
            		'pay_user_id' =>isset($data['user_id']) ? $data['user_id'] : null, //コンビニなし
            		
              		'pay_payment_code' => $paymentCode, //ネットバンク、GMO後払いのみ  
              		'pay_result' => isset($data['result']) ? $data['result'] : null, //クレカのみ
                	'pay_state' => isset($data['state']) ? $data['state'] : null,  //ネットバンク、GMO後払いのみ
                 	*/   
                              
                ]
            );
            
            $saleIds[] = $sale->id; 
            
            //在庫引く処理
            $item = $this->item->find($val['item_id']);
            $item->stock -= $val['item_count'];
            $item->save();
                        
        } //foreach
        
        //各商品の合計金額
        //$allTotal = $this->sale->find($saleIds)->sum('total_price');
        
        
        
        //Mail送信 ----------------------------------------------
        
        //Ctm::sendMail($data, 'itemEnd');
        Mail::to($userData['email'], $userData['name'])->send(new OrderEnd($saleRelId, 1));
        Mail::to($this->set->admin_email, $this->set->admin_name)->send(new OrderEnd($saleRelId, 0));
        
        
        if(! Ctm::isLocal()) {
            $request->session()->forget('item');
            $request->session()->forget('all'); 
		}   
     
     	$pmModel = $this->payMethod;
     	return view('cart.end', ['data'=>$data, 'pm'=>$pm, 'pmModel'=>$pmModel, 'paymentCode'=>$payPaymentCode, 'active'=>4]);
      
      
      //クレカURL
      //https://192.168.10.16/shop/thankyou?trans_code=718296&user_id=9999&result=1&order_number=679294540
      //後払い戻りURL
      //https://192.168.10.16/shop/thankyou?trans_code=718177&order_number=1449574270&state=5&payment_code=18&user_id=9999    
    }

    public function postConfirm(Request $request)
    {
    	$pt=0;
    	if(Auth::check()) {
     		$pt = $this->user->find(Auth::id())->point;
     	}

        $rules = [
            'user.name' => 'filled|max:255',
            'user.hurigana' => 'filled|max:255',
            'user.email' => 'filled|email|max:255',
            'user.tel_num' => 'filled|numeric',
//            'cate_id' => 'required',
			'user.post_num' => 'filled|nullable|numeric|digits:7', //numeric|max:7
   			//'user.prefecture' => 'required',         
   			'user.address_1' => 'filled|max:255',
      		'user.address_2' => 'filled|max:255',  
        	'user.password' => 'filled|min:8|confirmed',                      
			'use_point' => 'numeric|max:'.$pt,
   			        
			'destination' => 'required_without:receiver.name,receiver.hurigana,receiver.tel_num,receiver.post_num,receiver.prefecture,receiver.address_1,receiver.address_2,receiver.address_3',
            'receiver.name' => 'required_without:destination|max:255',
            'receiver.hurigana' => 'required_without:destination|max:255',
            'receiver.tel_num' => 'required_without:destination|nullable|numeric',
            'receiver.post_num' => 'required_without:destination|nullable|numeric|digits:7',
            'receiver.prefecture' => 'required_without:destination',
            'receiver.address_1' => 'required_without:destination|max:255',
            'receiver.address_2' => 'required_without:destination|max:255',
            'receiver.address_3' => 'max:255',
            
            'pay_method' => 'required', 
            //'main_img' => 'filenaming',
        ];
        
        //
        if(! Auth::check()) {
        	$rules['user.prefecture'] = 'required';
         	
          	if($request->input('regist') && ! Ctm::isLocal()) {
          		$rules['user.email'] = 'filled|email|unique:users,email|max:255';
          	}   
               
        }
        
         $messages = [
            'title.required' => '「商品名」を入力して下さい。',
            'cate_id.required' => '「カテゴリー」を選択して下さい。',
            'destination.required_without' => '「配送先」を入力して下さい。', //登録先住所に配送の場合は「登録先住所に配送する」にチェックをして下さい。
            'pay_method.required' => '「お支払い方法」を選択して下さい。',
            'use_point.max' => '「ポイント」が保持ポイントを超えています。',
            //'post_thumb.filenaming' => '「サムネイル-ファイル名」は半角英数字、及びハイフンとアンダースコアのみにして下さい。',
            //'post_movie.filenaming' => '「動画-ファイル名」は半角英数字、及びハイフンとアンダースコアのみにして下さい。',
            //'slug.unique' => '「スラッグ」が既に存在します。',
        ];
        
        $this->validate($request, $rules, $messages);
        $data = $request->all();
        

        //全データをsessionに入れる
        $request->session()->put('all.data', $data); //user receiver destination paymentMethod
        //$request->session()->put('all.user', $data['user']);
        //$request->session()->put('all.receiver', $data['receiver']);
        //$request->session()->put('user.data', $data['user']);
        //$request->session()->put('receiver.data', $data['receiver']);
        
        $itemSes = session('item.data');
        $regist = session('all.regist');
        $allPrice = session('all.all_price');
//        print_r(session('all'));
//        exit;
        
        
        //商品テーブル用のオブジェクト取得 -------------------------------
        $itemData = array();
        $addPoint = 0;
//        print_r($itemSes);
//        exit;
        
        foreach( $itemSes as $key => $val) {
        	$obj = $this->item->find($val['item_id']);
         	//カウント   
         	$obj['count'] = $val['item_count'];
          	//トータルプライス   
            $obj['item_total_price'] = $val['item_total_price'];
            //ポイント計算
            $obj['point'] = ceil($val['item_total_price'] * ($obj->point_back/100)); //切り上げ？ 切り捨て:floor()
			$addPoint += $obj['point'];
            
			$itemData[] = $obj;
        }
        
        //手数料、送料、ポイントをここで合計する -------------------------
        $totalFee = 0;
        
        //ポイント -----------
        $usePoint = $data['use_point'];
        $totalFee = $allPrice - $usePoint;
        
        
        //送料 -----------
        $deliFee = 0;
        
        
        $totalFee = $totalFee + $deliFee;
        
        //代引き手数料 -----------
        $codFee = 0;
        if($data['pay_method'] == 5) { 
        	
         	if($totalFee <= 10000) {
          		$codFee = 324;
          	}
           	elseif ($totalFee >= 10001 && $totalFee <= 30000) {
            	$codFee = 432;
            }
            elseif ($totalFee >= 30001 && $totalFee <= 100000) {
            	$codFee = 648;
            }
            elseif ($totalFee >= 100001 && $totalFee <= 300000) {
            	$codFee = 1080;
            }
            elseif ($totalFee >= 300001 && $totalFee <= 500000) {
                $codFee = 2160;
            }
            elseif ($totalFee >= 500001 && $totalFee <= 1000000) {
                $codFee = 3240;
            }
            elseif ($totalFee >= 1000001 && $totalFee <= 999999999) {
                $codFee = 4320;
            }           
        }
        
        $totalFee = $totalFee + $codFee;
        
        //送料、手数料、ポイントのsession入れ *********************
        session(['all.deli_fee'=>$deliFee, 'all.cod_fee'=>$codFee, 'all.use_point'=>$usePoint, 'all.add_point'=>$addPoint]);

        
        // Settle 決済 ====================================================
        $title = $itemData[0]->title;
        $number = $itemData[0]->number;
        
        //Order_Number
        //$rand = mt_rand();
        $orderNum = Ctm::getOrderNum(10);
        
        //UserInfo
        if(isset($data['user'])) { 
        	$user_name = $data['user']['name'];
         	$user_email = $data['user']['email'];   
        }
        else {
        	$user_name = $this->user->find(Auth::id())->name;
            $user_email = $this->user->find(Auth::id())->email;
        }
        
        $settles = array();
        
        if($data['pay_method'] == 5 || $data['pay_method'] == 6) {
        	$settles['url'] = url('shop/thankyou');
        }
        else {
            $settles['url'] = "https://beta.epsilon.jp/cgi-bin/order/receive_order3.cgi"; //テスト環境
            //$settleUrl = ""; //本番
        }
        
        $payCode = 0;
        if($data['pay_method'] == 1) { //クレカ
        	$payCode = '10000-0000-00000-00000-00000-00000-00000';
        }
        elseif($data['pay_method'] == 2) { //コンビニ
        	$payCode = '00100-0000-00000-00000-00000-00000-00000';
        }
        elseif($data['pay_method'] == 3) { // ネットバンク
            $payCode = '00010-0000-00000-00000-00000-00000-00000';
        }
        elseif($data['pay_method'] == 4) { //後払い
            $payCode = '00000-0000-00000-00010-00000-00000-00000';
        }
//        elseif($data['pay_method'] == 4) { //代引き
//            $payCode = '10000-0000-00000-00000-00000-00000-00000'; // ???
//        }
        //User識別
        $settles['contract_code'] = '66254480';
        $settles['user_id'] = '9999';
        $settles['user_name'] = $user_name;
        $settles['user_mail_add'] = $user_email;
        $settles['item_code'] = $number;
        $settles['item_name'] = $title;
        $settles['order_number'] = $orderNum;
        $settles['st_code'] = $payCode;
        $settles['mission_code'] = 1;
        $settles['item_price'] = $totalFee;
        $settles['process_code'] = 1;
        $settles['memo1'] = 'あいうえお';
        $settles['xml'] = 0;
        $settles['lang_id'] = 'ja';
        //$settles['page_type'] = 12;
//        $settles['version'] = 2;
//        $settles['character_code'] = 'UTF8';
        
        //注文番号のsession入れ
        session(['all.order_number'=>$settles['order_number']]);
        
        $payMethod = $this->payMethod;
        
        $userArr = '';
        if(Auth::check()) {
        	$userArr = $this->user->find(Auth::id());
        }
        else {
        	$userArr = $data['user'];
        }
        
//        print_r($userArr);
//        exit;
        
        return view('cart.confirm', ['data'=>$data, 'userArr'=>$userArr, 'itemData'=>$itemData, 'regist'=>$regist, 'allPrice'=>$allPrice, 'settles'=>$settles, 'payMethod'=>$payMethod, 'deliFee'=>$deliFee, 'codFee'=>$codFee, 'usePoint'=>$usePoint, 'addPoint'=>$addPoint, 'active'=>3]);
    }
    
    
    public function postForm(Request $request)
    {
//        print_r(session('item.data'));
//         print_r(session('all'));   
//         exit; 
      
      	if($request->has('from_cart') ) { //cartからpostで来た時
       		$data = $request -> all(); 
            
       		
            $regist = $request->has('regist_on') ? 1 : 0;
         	$request->session()->put('all.regist', $regist);
          	
           	foreach($data['last_item_count'] as $key => $val) {   
            	$request->session()->put('item.data.'.$key.'.item_count', $val); //session入れ   
            	$request->session()->put('item.data.'.$key.'.item_total_price', $data['last_item_total_price'][$key]); //session入れ
            }
       	}
        else { //getの時
        	if($request->session()->has('all.regist')) {
         		$regist = session('all.regist');
         	}
          	else {
           		abort(404);
           	}      
        }          
     
     	//PayMethod
      	$payMethod = $this->payMethod->all();
       
       	//Prefecture
        $prefs = $this->prefecture->all();      
       	
        //User   
        $userObj = null;
        if(Auth::check()) {
        	$userObj = $this->user->find(Auth::id());
        }        
     
     	return view('cart.form', ['regist'=>$regist, 'payMethod'=>$payMethod, 'prefs'=>$prefs, 'userObj'=>$userObj, 'active'=>2]);   
    }
    
    
    public function postCart(Request $request)
    {        
        $itemData = array();
        $allPrice = 0;
        
//        echo date('Y/m/d', '2018-04-01 12:57:30');
//        exit;
//        $request->session()->forget('item.data');
//        $request->session()->forget('all');
        
        if($request->has('from_item')) { //postの時、request dataをsessionに入れる
            $data = $request->all();
            
            if($request->session()->has('item.data')) {
                
                if(! in_array($data, session('item.data'))) {
//                    echo "abc";
//                     print_r($data);   
//                     print_r(session('item.data'));   
//                    exit;
                    $request->session()->push('item.data', $data);
                 }   
            }
            else {
//                echo "bbb";
//                print_r(session('item.data'));
//                exit;
                $request->session()->push('item.data', $data);
            }
            
            $request->session()->put('org_url', $data['uri']);
        }

        
        $submit = 0;
        //再計算の時
        if($request->has('re_calc')) {
            $data = $request->all();
            $submit = 1;
//            print_r($secData);
//            exit;
        }
        
        //削除の時
        if($request->has('del_item_key')) {
        	$data = $request->all();
            $request->session()->forget('item.data.'.$data['del_item_key']);
            
            //Keyの連番を振り直してsessionに入れ直す
            $reData = array_merge(session('item.data'));
            $request->session()->put('item.data', $reData);
        }
        
        //itemのsessionがある時　なければスルーして$itemDataを空で渡す
        if( $request->session()->has('item.data') ) {
            $itemSes = session('item.data');
//            print_r($itemSes);
//             exit;
                
            //sessionからobjectを取得して配列に入れる
            foreach($itemSes as $key => $val) {
                $obj = $this->item->find($val['item_id']);
                
                if($submit) { //再計算の時
                	$obj['count'] = $data['last_item_count'][$key];
                }
                else {
                	$obj['count'] = $val['item_count'];	
                } 
                
                //値段 * 個数
                $total = Ctm::getPriceWithTax($obj->price);
                $obj['total_price'] = $total * $obj['count'];
                //$request->session()->put('item.data.'.$key.'.item_total_price', $obj['item_total_price']); //session入れ
				//合計金額を算出
				$allPrice += $obj['total_price'];		
                
                $itemData[] = $obj;       
            }
            
            $request->session()->put('all.all_price', $allPrice);
            //合計金額を算出
//            $priceArr = collect($itemData)->map(function($item) use($allPrice) {
//                return $item->total_price; 
//            })->all();
//            
//            $allPrice = array_sum($priceArr);
        }
        
        
        return view('cart.index', ['itemData'=>$itemData, 'allPrice'=>$allPrice, 'uri'=>session('org_url'), 'active'=>1 ]);
        
        //$tax_per = $this->set->tax_per;
//        print_r($itemSes);
//        exit;
//        
//        if ($request->session()->exists('item_data')) {
//            $itemSes = session('item_data');
//                
//        }
//           $request->session()->put('item_data', $data);
//        $ses = $request->session()->all();
//        
//        print_r($ses);
//        exit;

//         if($request->has('regist_on') || $request->has('regist_off')) {
//             $regist = $request->has('regist_on') ? 1 : 0;
//              $payMethod = $this->payMethod->all();   
//             return view('cart.form', ['itemData'=>$itemData, 'regist'=>$regist, 'allPrice'=>$allPrice, 'payMethod'=>$payMethod, ]);
//         }
//         else {
//            return view('cart.index', ['itemData'=>$itemData, 'allPrice'=>$allPrice, 'uri'=>session('org_url') ]);
//        }
        
//        //status
//        if(isset($data['open_status'])) { //非公開On
//            $data['open_status'] = 0;
//        }
//        else {
//            $data['open_status'] = 1;
//        }
//        
//        //stock_show
//        $data['stock_show'] = isset($data['stock_show']) ? 1 : 0;
//        
//        
//        if($editId) { //update（編集）の時
//            $status = '商品が更新されました！';
//            $item = $this->item->find($editId);
//        }
//        else { //新規追加の時
//            $status = '商品が追加されました！';
//            //$data['model_id'] = 1;
//            
//            $item = $this->item;
//        }
//        
//        $item->fill($data);
//        $item->save();
//        $itemId = $item->id;
//        
////        print_r($data['main_img']);
////        exit;
//        
//        //Main-img
//        if(isset($data['main_img'])) {
//                
//            //$filename = $request->file('main_img')->getClientOriginalName();
//            $filename = $data['main_img']->getClientOriginalName();
//            $filename = str_replace(' ', '_', $filename);
//            
//            //$aId = $editId ? $editId : $rand;
//            //$pre = time() . '-';
//            $filename = 'item/' . $itemId . '/thumbnail/'/* . $pre*/ . $filename;
//            //if (App::environment('local'))
//            $path = $data['main_img']->storeAs('public', $filename);
//            //else
//            //$path = Storage::disk('s3')->putFileAs($filename, $request->file('thumbnail'), 'public');
//            //$path = $request->file('thumbnail')->storeAs('', $filename, 's3');
//            
//            $item->main_img = $path;
//            $item->save();
//        }
//        
//        //Spare-img
//        if(isset($data['spare_img'])) {
//            $spares = $data['spare_img'];
//            
////            print_r($spares);
////            exit;
//            
//            foreach($spares as $key => $spare) {
//                if($spare != '') {
//            
//                    $filename = $spare->getClientOriginalName();
//                    $filename = str_replace(' ', '_', $filename);
//                    
//                    //$aId = $editId ? $editId : $rand;
//                    //$pre = time() . '-';
//                    $filename = 'item/' . $itemId . '/thumbnail/'/* . $pre*/ . $filename;
//                    //if (App::environment('local'))
//                    $path = $spare->storeAs('public', $filename);
//                    //else
//                    //$path = Storage::disk('s3')->putFileAs($filename, $request->file('thumbnail'), 'public');
//                    //$path = $request->file('thumbnail')->storeAs('', $filename, 's3');
//                    
//                    //$item->spare_img .'_'. $ii = $path;
//                    $item['spare_img_'. $key] = $path;
//                    $item->save();
//                }
//
//            }
//        }
//        
//        //spare画像の削除
//        if(isset($data['del_spareimg'])) {
//            $dels = $data['del_spareimg'];     
//             
//              foreach($dels as $key => $del) {
//                   if($del) {
//                     $imgName = $item['spare_img_'. $key];
//                       if($imgName != '') {
//                         Storage::delete($imgName);
//                     }
//                    
//                       $item['spare_img_'. $key] = '';
//                    $item->save();
//                 }   
//           }
//        }
//        
//
//        
//        //タグのsave動作
//        if(isset($data['tags'])) {
//            $tagArr = $data['tags'];
//        
//            foreach($tagArr as $tag) {
//                
//                //Tagセット
//                $setTag = Tag::firstOrCreate(['name'=>$tag]); //既存を取得 or なければ作成
//                
//                if(!$setTag->slug) { //新規作成時slugは一旦NULLでcreateされるので、その後idをセットする
//                    $setTag->slug = $setTag->id;
//                    $setTag->save();
//                }
//                
//                $tagId = $setTag->id;
//                $tagName = $tag;
//
//
//                //tagIdがRelationになければセット ->firstOrCreate() ->updateOrCreate()
//                $tagRel = $this->tagRelation->firstOrCreate(
//                    ['tag_id'=>$tagId, 'item_id'=>$itemId]
//                );
//                /*
//                $tagRel = $this->tagRelation->where(['tag_id'=>$tagId, 'item_id'=>$itemId])->get();
//                if($tagRel->isEmpty()) {
//                    $this->tagRelation->create([
//                        'tag_id' => $tagId,
//                        'item_id' => $itemId,
//                    ]);
//                }
//                */
//
//                //tagIdを配列に入れる　削除確認用
//                $tagIds[] = $tagId;
//            }
//        
//            //編集時のみ削除されたタグを消す
//            if(isset($editId)) {
//                //元々relationにあったtagがなくなった場合：今回取得したtagIdの中にrelationのtagIdがない場合をin_arrayにて確認
//                $tagRels = $this->tagRelation->where('item_id', $itemId)->get();
//                
//                foreach($tagRels as $tagRel) {
//                    if(! in_array($tagRel->tag_id, $tagIds)) {
//                        $tagRel->delete();
//                    }
//                }
//            }
//        }
        
        
        //return view('cart.index', ['data'=>$data ]);
    }

    public function postScript(Request $request)
    {
        $cate_id = $request->input('selectValue');
        
//        $allTags = $this->tag->get()->map(function($item){
//            return $item->name;
//        })->all();
        
        $subCates = $this->categorySecond->where(['parent_id'=>$cate_id, ])->get()->map(function($obj) {
            return [ $obj->id => $obj->name ];
        })->all();
        
         $array = [1, 11, 12, 13, 14, 15];
         
        return response()->json(array('subCates'=> $subCates)/*, 200*/); //200を指定も出来るが自動で200が返される  
          //return view('dashboard.script.index', ['val'=>$val]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect('dashboard/items/'.$id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $name = $this->category->find($id)->name;
        
        $atcls = $this->item->where('cate_id', $id)->get()->map(function($item){
            $item->cate_id = 0;
            $item->save();
        });
        
        $cateDel = $this->category->destroy($id);
        
        $status = $cateDel ? '商品「'.$name.'」が削除されました' : '商品「'.$name.'」が削除出来ませんでした';
        
        return redirect('dashboard/items')->with('status', $status);
    }
}
