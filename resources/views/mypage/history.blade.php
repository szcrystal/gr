@extends('layouts.app')

@section('content')


	{{-- @include('main.shared.carousel') --}}

<div id="main" class="top">

        <div class="panel panel-default">

            <div class="panel-body">
                {{-- @include('main.shared.main') --}}


<h3 class="mb-3 card-header">購入履歴一覧</h3>
@if(! count($sales) > 0)
<div>
	<p style="min-height: 300px;">まだ購入した商品がありません。</p>
</div>
@else
<div class="table-responsive table-custom">
    <table class="table table-bordered bg-white">
        <thead>
        <tr>
        	<th>購入日/<br>ご注文番号</th>
         	<th colspan="2">商品名</th>
          	 
          	<th>個数</th>
           	<th>金額合計（税込）</th>
			<th>残 枯れ保証期間</th>
   			<th></th>         
        </tr>
        </thead>
        
        <tbody>
        @foreach($sales as $sale)
        <tr>
             <td>
             	{{ Ctm::changeDate($sale->created_at, 1) }}
            	<p class="mt-2"><small>ご注文番号</small><br><b>{{ $sale->order_number }}</b></p>
            </td>
            <td>
            	<?php $i = $item->find($sale->item_id); ?>
            	<img src="{{ Storage::url($i->main_img) }}" width="75" height="75" class="d-block img-fluid mx-auto">
            </td>
            <td class="clearfix">
             	{{ $i->title }}<br>
              	[{{ $i->number }}]
               <span class="d-block mt-1">¥{{ number_format(Ctm::getPriceWithTax($i->price)) }}</span> 
            </td>
             <td>{{ $sale->item_count }}</td>
             <td>
             	¥{{ number_format($sale->total_price) }}<br>
             	[{{ $pm->find($sale->pay_method)->name }}]
            </td>
             <td>
             	<?php 
              	   $days = Ctm::getKareHosyou($sale->created_at);   
                ?>
                {{ $days['limit'] }}まで<br>
               <b>残{{ $days['diffDay'] }}日</b>
             	<?php
//                      $limit = strtotime($sale->created_at." +91 day");
//                    $limitDay = new DateTime(date('Y-m-d', $limit));
//                     $current = new DateTime('now');
//                    $diff = $current->diff($limitDay);
          			//echo $diff->days;
                	
//                    $limit = $limit - strtotime("now");  
//                     $days = (strtotime('Y-m-d', $limit) - strtotime("1970-01-01")) / 86400;   
          
                	//echo $days;
                 	//exit;         
              	?> 
                  
             </td>
             <td>
             	<a href="{{ url('mypage/history/'.$sale->id) }}" class="btn btn-block border-secondary bg-white text-small mb-3">
                詳細を確認 <i class="fas fa-angle-double-right"></i>
                </a>
                
                <form class="form-horizontal" role="form" method="POST" action="{{ url('shop/cart') }}">
                    {{ csrf_field() }}
                                                                           
                    <input type="hidden" name="item_count" value="1">
                    <input type="hidden" name="from_item" value="1">
                    <input type="hidden" name="item_id" value="{{ $i->id }}">
                    <input type="hidden" name="uri" value="{{ Request::path() }}"> 
                                      
                   <button class="btn btn-custom text-small px-4" type="submit" name="regist_off" value="1"><i class="fas fa-shopping-basket"></i> もう一度購入</button>                 
				</form>
             </td>
        </tr>
        @endforeach
        
        </tbody>
        
	</table>
</div>

<div>
    {{ $sales->links() }}
</div>
@endif


<a href="{{ url('mypage') }}" class="btn border-secondary bg-white mt-5">
<i class="fas fa-angle-double-left"></i> マイページに戻る
</a>                  


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


