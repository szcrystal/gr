@extends('layouts.appDashBoard')

@section('content')
<?php
use App\Setting;

?>
	
	<div class="text-left">
        <h1 class="Title">
        @if(isset($edit))
        売上情報
        @else
        売上情報
        @endif
        </h1>
        <p class="Description"></p>
    </div>

    <div class="row">
      <div class="col-sm-12 col-md-6 col-lg-6 col-xl-5 mb-5">
        <div class="bs-component clearfix">
        <div class="pull-left">
            <a href="{{ url('/dashboard/sales') }}" class="btn bg-white border border-1 border-round border-secondary text-primary"><i class="fa fa-angle-double-left" aria-hidden="true"></i>一覧へ戻る</a>
            <br>
            <a href="{{ url('/dashboard/sales/order/' . $sale->order_number) }}" class="btn bg-white border border-1 border-round border-secondary text-primary mt-2"><i class="fa fa-angle-double-left" aria-hidden="true"></i>ご注文情報へ</a>
        </div>
        </div>
    </div>
  </div>

    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>Error!!</strong><br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
        
	@if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
        
    <div class="col-lg-12 mb-5">
        <form class="form-horizontal" role="form" method="POST" action="/dashboard/sales">

            {{ csrf_field() }}

            
             <div class="form-group mb-3">
                <div class="clearfix">
                	<p class="w-50 float-left">
                 		@if($sale->deli_done)
                   		<span class="text-success text-big">この商品は{{ date('Y/m/d H:i', time($sale->deli_start_date)) }}に発送済みです。</span>
                     	@else
                      	<span class="text-danger text-big">この商品は未配送です。</span>
                       	@endif                  
                    </p>
                    {{--
                    <button type="submit" class="btn btn-info btn-block float-right mx-auto w-btn w-25 text-white"><i class="fa fa-envelope"></i> 配送済みメールを送る</button>
                    --}}
                </div>
            </div>


            	<div class="table-responsive">
                    <table class="table table-bordered">
                        <colgroup>
                            <col style="background: #f5dfd5; width: 20%;" class="cth">
                            <col style="background: #fefefe;" class="ctd">
                        </colgroup>
                        
                        <tbody>
                        	<tr>
                                <th>売上ID</th>
                                <td>{{ $sale->id }}</td>
                            </tr>
                        	<tr>
                                <th>購入日</th>
                                <td><span class="text-big">{{ Ctm::changeDate($sale->created_at, 0) }}</span></td>
                            </tr>
                            <tr>
                                <th>購入者</th>
                                <td>
                                    @if($saleRel->is_user)
                                        <span class="text-dark">会員</span>: 
                                        <a href="{{ url('dashboard/users/'. $saleRel->user_id) }}">
                                        <?php
                                        	$users = $users->find($saleRel->user_id);
                                        ?>
                                    @else
                                         <span class="text-danger">非会員</span>: 
                                         <a href="{{ url('dashboard/users/'. $saleRel->user_id.'?no_r=1') }}">
                                         <?php
                                            $users = $userNs->find($saleRel->user_id);
                                        ?>   
                                     @endif
                                     （{{ $users->id }}）{{ $users->name }}<br>
                                     <a href="mailto:{{ $users->email }}">{{ $users->email }}</a><br>
                                     
                                     〒{{ Ctm::getPostNum($users->post_num) }}<br>
                                     {{ $users->prefecture }}
                                     {{ $users->address_1 }}
                                     {{ $users->address_2 }}
                                     {{ $users->address_3 }}<br>
                                     TEL：{{ $users->tel_num }}
                                     
                                     <input type="hidden" name="user_email" value="{{ $users->email }}">
                                     <input type="hidden" name="user_name" value="{{ $users->name }}">
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>配送先</th>
                                <td>
                                〒{{ Ctm::getPostNum($receiver->post_num) }}<br>
                                {{ $receiver->prefecture }}{{ $receiver->address_1 }}{{ $receiver->address_2 }}&nbsp;
                                {{ $receiver->address_3 }}<br>
                                {{ $receiver->name }} 様<br>
                                TEL: {{ $receiver->tel_num }}
                                
                                </td>
                            </tr>
                            <tr>
                            	<th>配送状況</th>
                             	<td>   
                            	@if($sale->deli_done)
                                   <span class="text-success">発送済み（{{ date('Y/m/d H:i', time($sale->deli_start_date)) }}）</span>
                                 @else
                                  <span class="text-danger">未発送</span>
                                @endif
                                </td>  
                            </tr>
                            
                            <tr>
                                <th>注文番号</th>
                                <td><a href="{{ url('dashboard/sales/order/'. $sale->order_number) }}">{{ $sale->order_number }}</a></td>
                            </tr>
                            <tr>
                                <th>(ID)商品名</th>
                                <td class="clearfix">
                                	
                                	<a href="{{ url('dashboard/items/'. $item->id) }}">
                                	<img src="{{ Storage::url($item->main_img) }}" width="80" height="60" class="img-fluid float-left mr-3">
                                 	</a>   
                                	<div>
                                 		商品番号: {{ $item->number }}<br>   
                                 		<a href="{{ url('dashboard/items/'. $item->id) }}">   
                                 	   	（{{ $item->id }}）{{ $item->title }}
                                     	</a>
                                      	<br>
                                       	      
                                      	¥<b>{{ number_format(Ctm::getPriceWithTax($item->price)) }}</b> （税込）  
                                    </div>
	
									<input type="hidden" name="sale_ids[]" value="{{ $sale->id }}">
                                </td>
                  
                            </tr>
                            <tr>
                                <th>個数</th>
                                <td>{{ $sale->item_count }}</td>
                            </tr>
                            
                            
                            
                            <tr>
                                <th>商品合計金額</th>
                                <td>
                                	<?php 
                                    	$per = Setting::find(1)->tax_per;
                                    	$per = ($per/100) + 1;
                                    ?>
                                	<b>¥{{ number_format($sale->total_price / $per) }}</b>（税抜）<br>
                                	<b>¥{{ number_format($sale->total_price) }}</b>（税込）
                                </td>
                            </tr>
                            
                            <tr>
                                <th>決済方法</th>
                                <td>{{ $pms->find($sale->pay_method)->name }}</td>
                            </tr>
                            
                            <tr>
                                <th>ご希望配送日時</th>
                                <td>
                                	@if(isset($sale->plan_date))
                                        <p class="mb-2">{{ $sale->plan_date }}</p>
                                    @endif
                                    
                                    @if(isset($sale->deli_time))
                                        {{ $sale->deli_time }}
                                    @endif
                                </td>
                            </tr>
                             
                            <tr>
                                <th>送料区分/<br>送料</th>
                                <td>
                                @if($item->deli_fee)
                                	<span class="text-warning">送料無料商品</span>
                                @else
                                	{{ $itemDg->name }}<br>
                                @endif
                                {{-- ¥{{ number_format($sale->deli_fee) }} --}}
                                
                                <fieldset class="mt-2 mb-4 form-group">
                                    <input class="form-control col-md-5 d-inline{{ $errors->has('deli_fee') ? ' is-invalid' : '' }}" name="deli_fee" value="{{ Ctm::isOld() ? old('deli_fee') : (isset($sale->deli_fee) ? $sale->deli_fee : '') }}">
                                    
                                    @if ($errors->has('deli_fee'))
                                        <div class="text-danger">
                                            <span class="fa fa-exclamation form-control-feedback"></span>
                                            <span>{{ $errors->first('deli_fee') }}</span>
                                        </div>
                                    @endif
                                </fieldset>
                                
                                </td>
                            </tr>
                            
                            <tr>
                            	<th>配送業者<br><small class="text-dark">*同時発送する商品に対しても適用されます</small></th>
                                <td>
                                	<fieldset class="mb-4 form-group">
                                    <input class="form-control col-md-5 d-inline{{ $errors->has('deli_company') ? ' is-invalid' : '' }}" name="deli_company" value="{{ Ctm::isOld() ? old('deli_company') : (isset($sale->deli_company) ? $sale->deli_company : '') }}">
                                    
                                    @if ($errors->has('deli_company'))
                                        <div class="text-danger">
                                            <span class="fa fa-exclamation form-control-feedback"></span>
                                            <span>{{ $errors->first('deli_company') }}</span>
                                        </div>
                                    @endif
                                </fieldset>
                                </td>
                            </tr>
                            
                            <tr>
                            	<th>伝票番号<br><small class="text-dark">*同時発送する商品に対しても適用されます</small></th>
                                <td>
                                <fieldset class="mb-4 form-group">
                                	
                                    <input class="form-control col-md-5 d-inline{{ $errors->has('deli_slip_num') ? ' is-invalid' : '' }}" name="deli_slip_num" value="{{ Ctm::isOld() ? old('deli_slip_num') : (isset($sale->deli_slip_num) ? $sale->deli_slip_num : '') }}">
                                    
                                    @if ($errors->has('deli_slip_num'))
                                        <div class="text-danger">
                                            <span class="fa fa-exclamation form-control-feedback"></span>
                                            <span>{{ $errors->first('deli_slip_num') }}</span>
                                        </div>
                                    @endif
                                </fieldset>
                                
                                </td>
                            </tr>
                            
                            @if($sale->pay_method == 5)
                            <tr>
                                <th>代引手数料</th>
                                <td>¥{{ number_format($sale->cod_fee) }}</td>
                            </tr>
                            @endif
                            <tr>
                                <th>総合計（A）</th>
                                <?php $total = $sale->total_price + $sale->deli_fee + $sale->cod_fee; ?>
                                <td><span style="font-size: 1.3em;" class="text-success"><b>¥{{ number_format($total) }}</b></span></td>
                            </tr>
                            
                            <tr>
                                <th>仕入れ値</th>
                                <td>
                                <?php 
                                	$costPrice = $items->find($sale->item_id)->cost_price;
                                    $sumCostPrice = $costPrice * $sale->item_count;
                                ?>
                                <fieldset class="mb-4 form-group">
                                    <input class="form-control col-md-5 d-inline{{ $errors->has('cost_price') ? ' is-invalid' : '' }}" name="cost_price" value="{{ Ctm::isOld() ? old('cost_price') : (isset($costPrice) ? $costPrice : '') }}">
                                    
                                    <span class="">x {{ $sale->item_count }} = {{ number_format($sumCostPrice) }}
                                    
									<input type="hidden" name="this_count" value="{{ $sale->item_count }}">
                                    
                                    @if ($errors->has('cost_price'))
                                        <div class="text-danger">
                                            <span class="fa fa-exclamation form-control-feedback"></span>
                                            <span>{{ $errors->first('cost_price') }}</span>
                                        </div>
                                    @endif
                                </fieldset>
                                </td>
                            </tr>
                            
                            <tr>
                                <th>送料差損</th>
                                <td>
                                <fieldset class="mb-4 form-group">
                                    <input class="form-control col-md-5{{ $errors->has('charge_loss') ? ' is-invalid' : '' }}" name="charge_loss" value="{{ Ctm::isOld() ? old('charge_loss') : (isset($sale) ? $sale->charge_loss : '') }}">
                                    

                                    @if ($errors->has('charge_loss'))
                                        <div class="text-danger">
                                            <span class="fa fa-exclamation form-control-feedback"></span>
                                            <span>{{ $errors->first('charge_loss') }}</span>
                                        </div>
                                    @endif
                                </fieldset>
                                </td>
                            </tr>
                            
                            
                  
                  			<?php 
                                $all = 0;
                     			$num = 2; 
                        	?>                 
                  
                  			@foreach($sameSales as $sameSale)
                            <tr>
                                <th>同時購入商品.{{ $num }}</th>
                                <td class="clearfix">
                                	@if(! $sale->deli_done)
                                	<fieldset class="form-group checkbox">
                                            <label>
                                                <?php
                                                    $checked = '';
                                                    if(Ctm::isOld()) {
                                                        if(old('open_status'))
                                                            $checked = ' checked';
                                                    }
                                                    else {
                                                        if(isset($item) && ! $item->open_status) {
                                                            $checked = ' checked';
                                                        }
                                                    }
                                                ?>
                                                <input type="checkbox" name="sale_ids[]" value="{{ $sameSale->id }}"{{ $checked }}> 同時に発送済みメールをする
                                            </label>
                                    </fieldset>
                                    @endif
                                    
                                	<a href="{{ url('dashboard/sales/'.$sameSale->id) }}" class="float-right btn border border-secondary text-dark bg-white"><i class="fa fa-arrow-right"></i> 売上情報</a>
                                    
                                    商品番号: {{ $items->find($sameSale->item_id)->number }}<br>
                                	<a href="{{ url('dashboard/items/'. $sameSale->item_id) }}">
                                 	   
                                    （{{ $sameSale->item_id }}）
                                    {{ $items->find($sameSale->item_id)->title }}<br>
                                    </a>
                                    
                                    ご希望配送時間：
                                    @if(isset($sameSale->plan_date))
                                        {{ $sameSale->plan_date }}
                                    @endif
                                    &nbsp;&nbsp;
                                    @if(isset($sameSale->deli_time))
                                        {{ $sameSale->deli_time }}
                                    @endif
                                    <br>
                                    配送状況：
                                    @if($sameSale->deli_done)
                                       <span class="text-success">発送済み（{{ date('Y/m/d H:i', time($sameSale->deli_start_date)) }}）</span>
                                     @else
                                      <span class="text-danger">未配送</span>
                                    @endif
                                    <br>
                                    個数：{{ $sameSale->item_count }}<br>
                                    {{--
                                    商品合計：¥{{ number_format($sameSale->total_price) }}<br>
                                    送料：¥{{ number_format($sameSale->deli_fee) }}<br>
                                    @if($sameSale->pay_method == 5)
                                    	代引手数料：¥{{ number_format($sameSale->cod_fee) }}<br>
                                    @endif
                                    --}}
                                    <?php $allTotal = $sameSale->total_price + $sameSale->deli_fee +  $sameSale->cod_fee; ?>
                                    <b>商品合計（B）：<span class="text-success">¥{{ number_format($sameSale->total_price) }}</span></b>
                                    
                                    <?php 
                                    	$all += $allTotal;
                                     	$num++;   
                                    ?>
                                    
                                </td>
                            </tr>
                            @endforeach
                            
                            <tr>
                                <th>購入総合計（A+B）</th>
                                <td><span style="font-size: 1.2em;">¥{{ number_format($total + $all) }}</span></td>
                            </tr>
                            
                            
                            
                 
                            
                            {{--
                            <tr>
                                <th>対応状況</th>
                                <td>
                                    <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                                        <div class="col-md-10">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="status" value="1"{{isset($contact) && $contact->status ? ' checked' : '' }}> 対応済みにする
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            --}}

                            

                        </tbody>
                    </table>
                </div>
                
                <div class="mt-5">
                	<fieldset class="mb-4 form-group">
                    	<label for="detail" class="control-label text-info">お届け予定日（ユーザー反映）<small class="text-dark">*同時発送する商品に対しても適用されます</small></label>
                        <select class="form-control col-md-6{{ $errors->has('deli_schedule_date') ? ' is-invalid' : '' }}" name="deli_schedule_date">
                            <option selected disabled>選択して下さい</option>
                            	<?php 
                                	$days = array();
                                    $week = ['日', '月', '火', '水', '木', '金', '土'];
                                
                                    for($plusDay = 0; $plusDay < 64; $plusDay++) {
                                        $now = date('Y-m-d', time());
                                        $first = strtotime($now." +". $plusDay . " day");
                                        $days[] = date('Y/m/d', $first) . '（' . $week[date('w', $first)] . '）';
                                    }
                                ?>
                            

                                @foreach($days as $day)
                                    <?php
                                        $selected = '';
                                        if(Ctm::isOld()) {
                                            if(old('deli_schedule_date') == $day)
                                                $selected = ' selected';
                                        }
                                        else {
                                            if(isset($sale) && $sale->deli_schedule_date == $day) {
                                                $selected = ' selected';
                                            }
                                        }
                                    ?>
                                    <option value="{{ $day }}"{{ $selected }}>{{ $day }}</option>
                                @endforeach
                        </select>
                        
                        @if ($errors->has('deli_schedule_date'))
                            <span class="help-block">
                                <strong>{{ $errors->first('deli_schedule_date') }}</strong>
                            </span>
                        @endif
                    </fieldset>
                        
                	<fieldset class="mb-2 form-group{{ $errors->has('information') ? ' is-invalid' : '' }}">
                        <label for="detail" class="control-label text-info">ご連絡事項（ユーザー反映）<small class="text-dark">*同時発送する商品に対しても適用されます</small></label>

                            <textarea id="information" class="form-control" name="information" rows="8">{{ Ctm::isOld() ? old('information') : (isset($sale) ? $sale->information : '') }}</textarea>

                            @if ($errors->has('information'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('information') }}</strong>
                                </span>
                            @endif
                    </fieldset>
                    
                    <fieldset class="mt-5 mb-2 form-group{{ $errors->has('memo') ? ' is-invalid' : '' }}">
                        <label for="memo" class="control-label">メモ<span class="text-small">（内部のみ）</span></label>

                            <textarea id="memo" class="form-control" name="memo" rows="8">{{ Ctm::isOld() ? old('memo') : (isset($sale) ? $sale->memo : '') }}</textarea>

                            @if ($errors->has('memo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('memo') }}</strong>
                                </span>
                            @endif
                    </fieldset>
                    
                    <fieldset class="mb-2 form-group{{ $errors->has('craim') ? ' is-invalid' : '' }}">
                        <label for="detail" class="control-label">クレーム<span class="text-small">（内部のみ）</span></label>

                            <textarea id="detail" class="form-control" name="craim" rows="8">{{ Ctm::isOld() ? old('craim') : (isset($sale) ? $sale->craim : '') }}</textarea>

                            @if ($errors->has('craim'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('craim') }}</strong>
                                </span>
                            @endif
                    </fieldset>
                
                </div>

				<input type="hidden" name="saleId" value="{{ $sale->id }}">
				
                <div class="clearfix my-5">
                	<div class="form-group float-left w-25">
                        <button type="submit" class="btn btn-primary btn-block w-btn w-100 text-white" name="only_up" value="1"> 更新のみする</button>
                    </div>
                
                    <div class="form-group float-right col-md-4">
                        <button type="submit" class="btn btn-danger btn-block mx-auto w-btn w-100 text-white" name="with_mail" value="1"><i class="fa fa-envelope"></i> 更新して発送済みメールを送る</button>
                    </div>
                </div>
        </form>
        
    </div>

@endsection
