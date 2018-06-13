<?php 
/* Here is mail view */
?>

<?php //$info = DB::table('siteinfos')['first(); ?>


{{ $user['name'] }} 様
@if($isUser)
<br /><br />
{!! nl2br( $header ) !!}

@else
よりご注文がありました。<br>
ご注文内容は下記となります。
@endif


<br /><br />
<hr>
【ご注文番号】：{{ $saleRel->order_number }}<br>
【ご注文日】：{{ Ctm::changeDate($saleRel->created_at, 1) }}<br>
【ご注文者】：{{ $user->name }} 様<br>
【お届け先】： 
<div style="margin: 0 0 1.5em 1.0em;">
{{ $receiver->name }} 様<br>
〒{{ Ctm::getPostNum($receiver->post_num) }}<br>
{{ $receiver->prefecture }}{{ $receiver->address_1 }}{{ $receiver->address_2 }}<br>
{{ $receiver->address_3 }}
</div>
【発送日】： {{ date('Y/m/d', time()) }}<br>
【発送商品】： <br>
<?php $num = 1; 
?>
@foreach($sales as $sale)
<div style="margin: 0 0 1.5em 1.0em;">
<div>{{ $num }}.</div>
商品番号: {{ $itemModel->find($sale->item_id)->number }}<br>
商品名: {{ $itemModel->find($sale->item_id)->title }}<br>
個数: {{ $sale->item_count}}<br>
金額：¥{{ number_format($sale->total_price) }}（税込）
</div>

<?php $num++; ?>
@endforeach
<br>
<hr>
<br><br>

@if($isUser)
{!! nl2br( $footer ) !!}
@endif

<br><br><br><br>
{!! nl2br($setting->mail_footer) !!}


