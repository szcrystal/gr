@extends('layouts.app')

@section('content')


	{{-- @include('main.shared.carousel') --}}

<div id="main" class="top">

        <div class="panel panel-default">

            <div class="panel-body">
                {{-- @include('main.shared.main') --}}

				<div class="main-list clearfix">


<div class="clearfix">
{{-- @include('cart.guide') --}}

<div class="float-left col-md-8">
	<h5 class="card-header mb-3 py-2">購入履歴詳細</h5>
	
	<div class="table-responsive table-custom table-cart">
    <table class="table table-bordered bg-white">
    	<col class="w-25"></col>
        <col></col>
        <tbody>
    	<tr>
     		<th>ご注文番号</th>
       		<td>{{ $sale->order_number }}</td>      
        </tr>
        <tr>
             <th>ご注文日</th>
            <td>{{ Ctm::changeDate($sale->created_at, 0) }}</td>      
        </tr>
        <tr>
             <th>発送日</th>
            <td>
            @if($sale->deli_done)
            {{ Ctm::changeDate($sale->deli_date, 0) }}
            @else
            <span class="text-info">発送準備中です。</span>
            @endif
            </td>      
        </tr>
        <tr>
             <th>枯れ保証期間</th>
            <td>
            	<?php $days = Ctm::getKareHosyou($sale->created_at); ?>
            	{{ $days['limit'] }}まで<br>
             	<b class="text-big">残{{ $days['diffDay'] }}日</b>   
            </td>      
        </tr>
        </tbody>
    </table>
    </div>
	
	<div class="table-responsive table-custom table-cart mt-4">
    <table class="table table-bordered bg-white">
        <thead>
              <tr>
                <th></th>
                <th>商品名</th>
                <th>数量</th>
                <th>金額（税込）</th>
            </tr>
          </thead>  
    
        <tbody>
             
             <tr>
                <td class="text-center"><img src="{{ Storage::url($item->main_img) }}" alt="{{ $item->title }}" class="img-fluid" width=80 height=80></td>
                
                <td>{{ $item->title }}<br>[ {{ $item->number }} ]</td>
                
                <td>{{ $sale->item_count }}</td>

                <td>
                <?php
                     $price = $sale->total_price;
                 ?>
                ¥{{ number_format($price) }}
                </td>

            </tr> 
                          
         </tbody> 
         
    </table>
</div>


<h5 class="card-header mb-3 py-2 mt-5">配送情報</h5>
<div class="table-responsive table-custom mt-3">
    <table class="table table-borderd border bg-white">
    	<thead>
     	   <tr><th>お届け先</th></tr>
        </thead>
        
        <tbody>
        	<tr>
            <td>        
    〒{{ Ctm::getPostNum($receiver->post_num) }}<br>
    {{ $receiver->prefecture }}&nbsp;
    {{ $receiver->address_1 }}&nbsp;
    {{ $receiver->address_2 }}<br>
    {{ $receiver->address_3 }}
    <span class="d-block mt-2">{{ $receiver->name }} 様</span>
    TEL : {{ $receiver->tel_num }}
    
	</td>
	</tr>
         </tbody> 
    </table>
</div>


</div> 


<div class="float-right col-md-4">
<h5 class="mb-4">&nbsp;</h5>
<div class="table-responsive table-custom">
    <table class="table border table-borderd bg-white">
        <col class="w-50"></col>
        <col class="text-right"></col>
        
        <tbody>
        <tr>
            <th><label class="control-label">商品金額合計（税込）</label></th>
             <td>
             
             ¥{{ number_format($price) }}
             </td>
        </tr>
        <tr>
            <th><label class="control-label">送料</label></th>
            <td>¥{{ number_format($sale->deli_fee) }}</td>
        </tr>
        
        @if($sale->pay_method == 5)
            <tr>
                <th><label class="control-label">代引き手数料</label></th>
                <td>¥{{ number_format($sale->cod_fee) }}</td>
            </tr>
        @endif
        
        @if(Auth::check())
        <tr>
            <th><label class="control-label">利用ポイント</label></th>
             <td>-{{ $sale->use_point }}</td>
        </tr>
        @endif
        
        <tr>
            <th><label class="control-label">注文金額合計（税込）</label></th>
             <td class="text-danger text-big">
                  ¥{{ number_format($sale->total_price + $sale->deli_fee + $sale->cod_fee - $sale->use_point) }}  
            </td>
        </tr>
        </tbody>
    </table>
</div>

<div class="table-responsive table-custom mt-3">
    <table class="table border table-borderd bg-white">
        <col style="width:50%;"></col>
        <col class="text-right"></col>
        {{--
        <tr>
            <th><label class="control-label">ポイント残高</label></th>
             <td>{{ $userArr['point'] - $usePoint }}</td>
        </tr>
        --}}
        <tr>
            <th><label class="control-label">ポイント発生</label></th>
            <td>{{ ceil($price * ($item->point_back/100)) }}</td>
        </tr>
    </table>
</div>


<div class="table-responsive table-custom mt-3">
    <table class="table border table-borderd bg-white">
        <col style="width:50%;"></col>
        <col class="text-right"></col>
        
        <tr>
            <th><label class="control-label">お支払い方法</label></th>
            <td>
            	{{ $pm->find($sale->pay_method)->name }}
            </td>
        </tr>

    </table>
</div>

</div> {{-- float-right --}}

</div>

<div class="mt-5">
<form class="form-horizontal" role="form" method="POST" action="{{ url('shop/cart') }}">
    {{ csrf_field() }}
                                                           
    <input type="hidden" name="item_count" value="1">
    <input type="hidden" name="from_item" value="1">
    <input type="hidden" name="item_id" value="{{ $item->id }}">
    <input type="hidden" name="uri" value="{{ Request::path() }}"> 
    
                         
   <button class="btn btn-block btn-custom col-md-4 mb-4 mx-auto py-2" type="submit" name="regist_off" value="1"><i class="fas fa-shopping-basket"></i> もう一度購入する</button>                 
</form>

<a href="{{ url('mypage/history') }}" class="btn border border-secondary bg-white"><i class="fas fa-angle-double-left"></i> 購入履歴一覧に戻る</a>
</div>




</div>
</div>
</div>
</div>

@endsection


{{--
@section('leftbar')
    @include('main.shared.leftbar')
@endsection


@section('rightbar')
	@include('main.shared.rightbar')
@endsection
--}}


