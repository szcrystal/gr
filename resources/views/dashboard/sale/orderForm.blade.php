@extends('layouts.appDashBoard')

@section('content')
<?php
use App\Item;
use App\Setting;
?>
	
	<div class="text-left">
        <h1 class="Title">
        @if(isset($edit))
        ご注文情報
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
        <form class="form-horizontal" role="form" method="POST" action="/dashboard/sales/order">

            {{ csrf_field() }}

             <div class="form-group mb-3">
                <div class="clearfix">
                	<p class="w-50 float-left">
                 		
                    @if($saleRel->pay_method == 6)
                        @if($saleRel->pay_done)
                   		<span class="text-success text-big">この注文は、銀行振込：入金済みです。</span>
                     	@else
                      	<span class="text-danger text-big">この注文は、銀行振込：未入金です。</span>
                       	@endif  
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
                            <col style="background: #dfdcdb; width: 20%;" class="cth">
                            <col style="background: #fefefe;" class="ctd">
                        </colgroup>
                        
                        <tbody>
                        	<tr>
                                <th>注文番号</th>
                                <td>
                                	{{ $saleRel->order_number }}
                                	<input type="hidden" name="order_id" value="{{ $saleRel->id }}">
                                </td>
                            </tr>
                        	<tr>
                                <th>購入日</th>
                                <td><span class="text-big"><b>{{ Ctm::changeDate($saleRel->created_at, 0) }}</b></span></td>
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
                                <th>決済方法</th>
                                <td><span class="text-big"><b>{{ $pms->find($saleRel->pay_method)->name }}</b></span></td>
                            </tr>

                  
                  			<?php 
                                $all = 0;
                     			$num = 1; 
                        	?>                 
                  
                  			@foreach($sales as $sale)
                            <tr>
                                <th>購入商品.{{ $num }}</th>
                                <td class="clearfix">
                                	
                                    
                                	<a href="{{ url('dashboard/sales/'.$sale->id) }}" class="float-right btn border border-secondary text-dark bg-white"><i class="fa fa-arrow-right"></i> 売上情報</a>
                                    
                                    商品番号: {{ $items->find($sale->item_id)->number }}<br>
                                	<a href="{{ url('dashboard/items/'. $sale->item_id) }}">
                                 	   
                                		（{{ $sale->item_id }}）
                                    	{{ Ctm::getItemTitle($items->find($sale->item_id)) }}<br>
                                    </a>
                                    
                                    ご希望配送時間：
                                    @if(isset($sale->plan_date))
                                        {{ $sale->plan_date }}
                                    @endif
                                    
                                    @if(isset($sale->plan_time))
                                        {{ $sale->plan_time }}
                                    @endif
                                    <br>
                                    配送状況：
                                    @if($sale->deli_done)
                                       <span class="text-success">発送済み（{{ date('Y/m/d H:i', time($sale->deli_start_date)) }}）</span>
                                     @else
                                      <span class="text-danger">未発送</span>
                                    @endif
                                    <br>
                                    
                                    配送業者：{{ $sale->deli_company }} - {{ $sale->deli_slip_num }}
                                    <br>

                                    個数：{{ $sale->item_count }}<br>
                                    <b>商品合計：¥{{ number_format($sale->total_price) }}</b>
                                    <br>
                                    
                                    メモ：{{ $sale->memo }}
                                    
                                    
                                    
                                    <?php 
                                    	$all += $sale->total_price;
                                     	$num++;   
                                    ?>
                                    
                                </td>
                            </tr>
                            @endforeach
                            
                            <tr>
                                <th>商品総合計（A）</th>
                                <td><span style="font-size: 1.2em;">¥{{ number_format($saleRel->all_price) }}</span></td>
                            </tr>
                            
                            <tr>
                                <th>送料（B）</th>
                                <td>
                                ¥{{ number_format($saleRel->deli_fee) }}
                                </td>
                            </tr>
                            
                            <tr>
                                <th>代引手数料（C）</th>
                                <td>¥{{ number_format($saleRel->cod_fee) }}</td>
                            </tr>
                            
                            <tr>
                                <th>ポイント利用（D）</th>
                                <td>
	                                {{ $saleRel->use_point }}
                                </td>
                            </tr>
                            
                            <tr>
                                <th>購入総合計<br>（A+B+C-D）</th>
                                <?php 
                                	//$total = $sale->total_price + $sale->deli_fee + $sale->cod_fee;
                                	$total = $saleRel->all_price + $saleRel->deli_fee + $saleRel->cod_fee - $saleRel->use_point;
                                ?>
                                <td>
                                	<span style="font-size: 1.3em;" class="text-success"><b>¥{{ number_format($total) }}</b></span><br>
                                
                                    @if($saleRel->pay_method == 6)
                                        @if($saleRel->pay_done)
                                            <span class="text-success">入金済み</span>
                                        @else
                                            <span class="text-danger">未入金</span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            
                            <tr>
                                <th>粗利額</th>
                                <td>
                                <?php
                                    $taxPer = Setting::get()->first()->tax_per;
                                    $taxPer = $taxPer/100 + 1; //$taxPer ->1.08

                                    $tax = $saleRel->all_price - ($saleRel->all_price / $taxPer); //$taxPer ->1.08

                                    $arari = $total - $tax - $sales->sum('cost_price') - $sales->sum('charge_loss');
                                ?>
                                                                
                                ¥{{ number_format($arari) }}
                                </td>
                            </tr>
                            <tr>
                                <th>粗利率</th>
                                <td>{{ round($arari / $total * 100, 1) }}%</td>
                            </tr>
                            
                            
                            @if($saleRel->pay_method == 6)
                            <tr>
                                <th>入金</th>
                           
                                <td class="clearfix">
                                	<fieldset class="form-group checkbox">
                                            <label>
                                                <?php
                                                    $checked = '';
                                                    if(Ctm::isOld()) {
                                                        if(old('pay_done'))
                                                            $checked = ' checked';
                                                    }
                                                    else {
                                                        if(isset($saleRel) && $saleRel->pay_done) {
                                                            $checked = ' checked';
                                                        }
                                                    }
                                                ?>
                                                <input type="checkbox" name="pay_done" value="1"{{ $checked }}> 入金済みにする
                                            </label>
                                    </fieldset>
                                    
                                	
                                    
                                </td>
                            </tr>
                            @endif
                            
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
                
                
                
                @if($saleRel->pay_method == 6)
                    <div class="form-group clearfix my-4">
                    	{{-- <button type="submit" class="btn btn-primary col-md-3 text-white float-left" name="only_up" value="1">更新のみする</button> --}}
                        
                    	<?php
                        	$state = ( $saleRel->pay_done && !Ctm::isLocal() ) ? ' disabled' : '';
                        ?>
                        <button type="submit" class="btn btn-danger col-md-4 text-white float-right" name="with_mail" value="1" {{ $state }}><i class="fa fa-envelope"></i> 入金済にしてメールを送る</button>
                    </div>
                @endif
        </form>
        
    </div>

@endsection
