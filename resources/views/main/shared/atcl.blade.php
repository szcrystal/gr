<?php
use App\Setting;
use App\Item;
use App\Category;
use App\CategorySecond;
use App\Favorite;
use App\Icon;
?>

<?php
	$isCate = (isset($type) && $type == 'category') ? 1 : 0;
	
    $categoryId = $isCate ? $item->parent_id : $item->cate_id;
    $category = Category::find($categoryId);
    
    $link = $isCate ? url('category/' . $category->slug . '/' . $item->slug) : url('/item/'. $item->id);

    $isSp = Ctm::isAgent('sp');
    $isSale = Setting::get()->first()->is_sale;
?>

@if($isSale || isset($item->sale_price))
<span class="sale-belt">SALE</span>
@endif


<div class="img-box">
    <a href="{{ $link }}">
    	<img src="{{ Storage::url($item->main_img) }}" alt="{{ $item->title }}">
    </a>
</div>

<div class="meta">
    <h3><a href="{{ $link }}">
        @if($isCate)
            {{ Ctm::shortStr($item->name, $strNum) }}
        @else
            {{ Ctm::shortStr($item->title, $strNum) }}
        @endif
    </a></h3>
    
    @if(! $isCate)
        <p>
            <a href="{{ $link }}">
                @if(isset($category->link_name))
                    {{ $category->link_name }}
                @else
                    {{ $category->name }}
                @endif
            </a>
        </p>
        
        @if(isset($item->icon_id) && $item->icon_id != '')
            <div class="icons">
                <?php $obj = $item; ?>
                @include('main.shared.icon')
            </div>
        @endif


        <div class="tags">
            <?php $num = 2; ?>
            @include('main.shared.tag')
        </div>
            
        
        <div class="price">
            <?php
                $pots = Item::where(['is_potset'=>1, 'pot_parent_id'=>$item->id])->orderBy('price', 'asc')->get();
                $isPotParent = $pots->isNotEmpty();
                
                if($isPotParent) {
                    $thisItem = $pots->first();
                }
                else {
                    $thisItem = $item;
                }
            ?>
            
            @if($isSale || isset($thisItem->sale_price))
                @if(! $isSp)
                    <strike>{{ number_format(Ctm::getPriceWithTax($thisItem->price)) }}</strike>
                    <i class="fal fa-arrow-right text-small"></i>
                @endif
            @endif
            
            @if(isset($thisItem->sale_price))
                <span class="show-price text-enji">{{ number_format(Ctm::getPriceWithTax($thisItem->sale_price)) }}
            @else
                @if($isSale)
                    <span class="show-price text-enji">{{ number_format(Ctm::getSalePriceWithTax($thisItem->price)) }}
                @else
                    <span class="show-price">{{ number_format(Ctm::getPriceWithTax($thisItem->price)) }}
                @endif
            @endif
            </span>
            <span class="show-yen">円(税込)
            @if($isPotParent)
            〜
            @endif
            </span>
            
        </div>
    

        @if(Auth::check())
            <div class="favorite">
                <?php
                    if(Favorite::where(['user_id'=>Auth::id(), 'item_id'=>$item->id])->first()) {
                        $on = ' d-none';
                        $off = ' d-inline'; 
                        $str = 'お気に入りの商品です';              
                    }
                    else {
                        $on = ' d-inline';
                        $off = ' d-none';
                        $str = 'お気に入りに登録';
                    }               
                ?>

                <span class="fav fav-on{{ $on }}" data-id="{{ $item->id }}"><i class="fal fa-heart"></i></span>
                <span class="fav fav-off{{ $off }}" data-id="{{ $item->id }}"><i class="fas fa-heart"></i></span>
                <span class="loader"><i class="fas fa-square"></i></span>
                <small class="fav-str">{{-- $str --}}</small>    
            </div>
        @else
            {{-- <span class="fav-temp"><a href="{{ url('login') }}"><i class="far fa-heart"></i></a></span> --}}
        @endif
    
    @endif
    
</div>


