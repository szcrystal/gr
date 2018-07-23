<?php

namespace App\Http\Controllers\DashBoard;

use App\Admin;
use App\User;
use App\UserNoregist;
use App\Sale;
use App\SaleRelation;
use App\Item;
use App\PayMethod;
use App\Receiver;
use App\DeliveryGroup;
use App\Consignor;
use App\Category;

use App\Mail\OrderSend;
use App\Mail\PayDone;

use Mail;
use DB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SaleController extends Controller
{
    public function __construct(Admin $admin, Sale $sale, SaleRelation $saleRel, Item $item, User $user, PayMethod $payMethod, UserNoregist $userNoregist, Receiver $receiver, DeliveryGroup $dg, Consignor $consignor, Category $category)
    {
        
        $this -> middleware('adminauth');
        //$this -> middleware('log', ['only' => ['getIndex']]);
        
        $this -> admin = $admin;
        $this-> sale = $sale;
        $this->saleRel = $saleRel;
        $this-> item = $item;
        $this-> user = $user;
        $this->userNoregist = $userNoregist;
        $this->payMethod = $payMethod;
        $this->receiver = $receiver;
        $this->dg = $dg;
        $this->category = $category;
//        $this->category = $category;
//        $this -> tag = $tag;
//        $this->tagRelation = $tagRelation;
//        $this->consignor = $consignor;
        
        $this->perPage = 20;
        
        // URLの生成
        //$url = route('dashboard');
        
    }
    
    
    
    public function index(Request $request)
    {
    	//$saleObjs = $this->sale->whereYear('created_at', '=','2017', 'and')->whereYear('created_at', '2018')->orderBy('id', 'asc')->get();
//        $saleObjs = $this->sale->whereBetween(DB::raw('DATE(created_at)'), ['2017-01-01', '2018-05-31'])->orderBy('id', 'asc')->get();

		$saleForSum = null;
        
    	if($request->has('set')) {
        	$rules = [
                'first_y' => 'required',
                'first_m' => 'required_with:last_y',
                'last_m' => 'required_with:last_y',
            ];
            
             $messages = [
                 'first_y.required' => '「最初の年」を選択して下さい。',
                 'first_m.required_with' => '「最初の月」を選択して下さい。',
                 'last_m.required_with' => '「最後の月」を選択して下さい。',
            ];
            
            $this->validate($request, $rules, $messages);
            
            $data = $request->all();
            
            if(! isset($data['last_y'])) { //only first
            	if(isset($data['first_m'])) { //firstの月も指定されている時
            		$saleObjs = $this->saleRel->whereYear('created_at', $data['first_y'])->whereMonth('created_at', $data['first_m'])->orderBy('id', 'desc')->get();
                }
                else { //firstの年のみ指定されている時
                	$saleObjs = $this->saleRel->whereYear('created_at', $data['first_y'])->orderBy('id', 'desc')->get();
                }
            }
            else {
                $firstDate = $data['first_y'].'-'.$data['first_m']. '-01';
                $lastDate = $data['last_y'].'-'.$data['last_m']. '-31';
                
                $saleObjs = $this->saleRel->whereBetween(DB::raw('DATE(created_at)'), [$firstDate, $lastDate])->orderBy('id', 'desc')->get();
            }
            
            $relIds = $saleObjs->map(function($obj){
                return $obj->id;
            })->all();
            
            $saleForSum = $this->sale->whereIn('salerel_id', $relIds)->get();
            

            
            /*
            if(! isset($data['last_y'])) { //only first
            	if(isset($data['first_m'])) {
            		$saleObjs = $this->sale->whereYear('created_at', $data['first_y'])->whereMonth('created_at', $data['first_m'])->orderBy('id', 'desc')->get();
                }
                else {
                	$saleObjs = $this->sale->whereYear('created_at', $data['first_y'])->orderBy('id', 'desc')->get();
                }
            }
            else {
                $firstDate = $data['first_y'].'-'.$data['first_m']. '-01';
                $lastDate = $data['last_y'].'-'.$data['last_m']. '-31';
                
                $saleObjs = $this->sale->whereBetween(DB::raw('DATE(created_at)'), [$firstDate, $lastDate])->orderBy('id', 'desc')->get();
            }
            
            $numIds = $saleObjs->map(function($obj){
                return $obj->order_number;
            })->all();
            

            $numIds = array_unique($numIds); //重複データを削除する
            $numIds = array_values($numIds); //keyを振り直す
            
            $saleRelForSum = $this->saleRel->whereIn('order_number', $numIds)->get();
            */

        }
        else {
        	//$saleObjs = Sale::orderBy('id', 'desc')->paginate($this->perPage);
        	//$saleObjs = Sale::orderBy('id', 'desc')->get();
            $saleObjs = $this->saleRel->orderBy('id', 'desc')->get();
        }
        
        $saleSingle = $this->sale;
        
        $items= $this->item;
        $pms = $this->payMethod;
        $itemDg = $this->dg;
        $users = $this->user;
        $userNs = $this->userNoregist;
        
        $cates = $this->category;
        
        //$status = $this->articlePost->where(['base_id'=>15])->first()->open_date;
        
        return view('dashboard.sale.index', ['saleObjs'=>$saleObjs, 'saleSingle'=>$saleSingle, 'items'=>$items, 'pms'=>$pms, 'itemDg'=>$itemDg, 'users'=>$users, 'userNs'=>$userNs, 'cates'=>$cates, 'saleForSum'=>$saleForSum,]);
    }
    
    public function saleCompare()
    {
    	$date = date('Y-m', time());
        
        
        $saleObjs = $this->sale->whereYear('created_at', '2017')->whereMonth('created_at', '06')->orderBy('id', 'asc')->get();
        
        $totalSum = $saleObjs->sum('total_price');
        
        
        
        
        $saleRels = $this->saleRel;
        
        $items= $this->item;
        $pms = $this->payMethod;
        $itemDg = $this->dg;
        $users = $this->user;
        $userNs = $this->userNoregist;
        
        $cates = $this->category;
        
        //$status = $this->articlePost->where(['base_id'=>15])->first()->open_date;
        
        return view('dashboard.sale.indexCompare', ['saleObjs'=>$saleObjs, 'saleRels'=>$saleRels, 'items'=>$items, 'pms'=>$pms, 'itemDg'=>$itemDg,'users'=>$users, 'userNs'=>$userNs, 'cates'=>$cates]);
    }
    
    public function rankCompare()
    {
    	$date = date('Y-m', time());
        
        
        $saleItemIds = $this->sale->whereYear('created_at', '2017')->whereMonth('created_at', '06')->get()->map(function($obj){
        	return $obj->item_id;
        });
        
        $saleItemIds = array_unique($saleItemIds); //重複データを削除する
        $saleItemIds = array_values($saleItemIds); //keyを振り直す
        
        $saleObjs = $this->item->find($saleItemIds);
        
        
        
        $saleRels = $this->saleRel;
        
        $items= $this->item;
        $pms = $this->payMethod;
        $users = $this->user;
        $userNs = $this->userNoregist;
        
        $cates = $this->category;
        
        //$status = $this->articlePost->where(['base_id'=>15])->first()->open_date;
        
        return view('dashboard.sale.indexCompare', ['saleObjs'=>$saleObjs, 'saleRels'=>$saleRels, 'items'=>$items, 'pms'=>$pms, 'users'=>$users, 'userNs'=>$userNs, 'cates'=>$cates]);
    }

    public function show($id)
    {
        $sale = $this->sale->find($id);
        $saleRel = $this->saleRel->find($sale->salerel_id);
        
        $sameSales= $this->sale->whereNotIn('id', [$id])->where(['salerel_id'=>$saleRel->id])->get();
        
        $item= $this->item->find($sale->item_id);
        $items = $this->item;
        
        $pms = $this->payMethod;
        
        $users = $this->user;
        $userNs = $this->userNoregist;

		$receiver = $this->receiver->find($saleRel->receiver_id);
  
  		$itemDg = $this->dg->find($item->dg_id); 
    
    	$cates = $this->category;           
//        
//        $tagNames = $this->tagRelation->where(['item_id'=>$id])->get()->map(function($item) {
//            return $this->tag->find($item->tag_id)->name;
//        })->all();
//        
//        $allTags = $this->tag->get()->map(function($item){
//            return $item->name;
//        })->all();
        
        return view('dashboard.sale.form', ['sale'=>$sale, 'saleRel'=>$saleRel, 'sameSales'=>$sameSales, 'item'=>$item, 'items'=>$items, 'pms'=>$pms, 'users'=>$users, 'userNs'=>$userNs, 'receiver'=>$receiver, 'cates'=>$cates, 'itemDg'=>$itemDg, 'id'=>$id, 'edit'=>1]);
    }
    
    //銀行振込入金用Get
    public function saleOrder($orderNum) 
    {
    	$saleRel = $this->saleRel->where('order_number', $orderNum)->first();
        
        $sales= $this->sale->where('order_number', $orderNum)->get();
        
        //$item= $this->item->find($sale->item_id);
        $items = $this->item;
        $pms = $this->payMethod;
        
        $users = $this->user;
        $userNs = $this->userNoregist;

		$receiver = $this->receiver->find($saleRel->receiver_id);
  
  		//$itemDg = $this->dg->find($item->dg_id); 
    
    	$cates = $this->category;           
//        
//        $tagNames = $this->tagRelation->where(['item_id'=>$id])->get()->map(function($item) {
//            return $this->tag->find($item->tag_id)->name;
//        })->all();
//        
//        $allTags = $this->tag->get()->map(function($item){
//            return $item->name;
//        })->all();
        
        return view('dashboard.sale.orderForm', ['saleRel'=>$saleRel, 'sales'=>$sales, 'items'=>$items, 'pms'=>$pms, 'users'=>$users, 'userNs'=>$userNs, 'receiver'=>$receiver, 'cates'=>$cates, 'id'=>$orderNum, 'edit'=>1]);
    }
    
    
    //銀行振込入金用Post
    public function postSaleOrder(Request $request) 
    {
    	$rules = [
            'pay_done' => 'required',
            //'cate_id' => 'required',
        ];
        
         $messages = [
             'pay_done.required' => '「入金」のチェックがされていません。',
           // 'cate_id.required' => '「カテゴリー」を選択して下さい。',
        ];
        
        $this->validate($request, $rules, $messages);

    	$data = $request->all();
        
        $saleRel = $this->saleRel->find($data['order_id']);
        $saleRel->pay_done = isset($data['pay_done']) ? $data['pay_done'] : 0;
        $saleRel->save();
        
        
        $mail = Mail::to($data['user_email'], $data['user_name'])->send(new PayDone($saleRel->id));
            
        if(! $mail) {
            $status = '入金済みメールが送信されました。';
    
            return redirect('dashboard/sales/order/'. $saleRel->order_number)->with('status', $status);
        } 
        else {
            $errors = array('入金済みメールの送信に失敗しました。');
            return redirect('dashboard/sales/order/'. $saleRel->order_number)->withErrors($errors)->withInput();
        }
        
    }
    
   
    public function create()
    {
        $cates = $this->category->all();
        $consignors = $this->consignor->all();
        
        $allTags = $this->tag->get()->map(function($item){
            return $item->name;
        })->all();
//        $users = $this->user->where('active',1)->get();
        return view('dashboard.item.orderForm', ['cates'=>$cates, 'consignors'=>$consignors, 'allTags'=>$allTags]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        $editId = $request->has('edit_id') ? $request->input('edit_id') : 0;
//        
//        $rules = [
//            'title' => 'required|max:255',
//            'cate_id' => 'required',
//            //'main_img' => 'filenaming',
//        ];
//        
//         $messages = [
//             'title.required' => '「商品名」を入力して下さい。',
//            'cate_id.required' => '「カテゴリー」を選択して下さい。',
//        ];
//        
//        $this->validate($request, $rules, $messages);
        
        $data = $request->all();

//        if(isset($data['only_craim'])) {
//            $saleModel = $this->sale->find($data['saleId']);
//            $saleModel->craim = $data['craim'];
//            $saleModel->save();
//         	
//          	$status = "クレームが更新されました。";   
//            return redirect('dashboard/sales/'. $data['saleId'])->with('status', $status);
//        }

        
        $saleModel = $this->sale->find($data['saleId']); //saleIdとsale_idsの両方あるので注意
        $saleModel->fill($data);
        $saleModel->cost_price = $data['cost_price'] * $data['this_count'];
        $saleModel->save();
        
        $item = $this->item->find($saleModel->item_id);
        $item->cost_price = $data['cost_price'];
        $item->save();
        
        $sales = $this->sale->find($data['sale_ids']); //saleIdとsale_idsの両方あるので注意
        foreach($sales as $obj) {
        	if($obj->id != $saleModel->id) {
            	$obj->plan_date = $data['plan_date'];
                $obj->information = $data['information'];
                $obj->save();
            }
        }
        
        //if(isset())
            
        if(isset($data['only_up'])) {  
        	$status = "更新されました。";   
            return redirect('dashboard/sales/'. $data['saleId'])->with('status', $status);
		}
        elseif(isset($data['with_mail'])) { 
            $mail = Mail::to($data['user_email'], $data['user_name'])->send(new OrderSend($saleModel->id, $data['sale_ids']));
            
            if(! $mail) {
                $status = 'メールが送信されました。';
                
                $sales = $this->sale->find($data['sale_ids']);
                foreach($sales as $sale) {
                    $sale->deli_done = 1;
                    $sale->deli_start_date = date('Y-m-d H:i:s', time());
                    $sale ->save();      
                }   
        
                return redirect('dashboard/sales/'. $data['sale_ids'][0])->with('status', $status);
            } 
            else {
                $errors = array('メールの送信に失敗しました。');
                return redirect('dashboard/sales/'. $data['sale_ids'][0])->withErrors($errors)->withInput();
            }
        }
        
        //status
//        if(isset($data['open_status'])) { //非公開On
//            $data['open_status'] = 0;
//        }
//        else {
//            $data['open_status'] = 1;
//        }
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
//        
//        
//        return redirect('dashboard/items/'. $itemId)->with('status', $status);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function show($id)
//    {
//        //
//    }

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
