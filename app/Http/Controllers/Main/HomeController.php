<?php

namespace App\Http\Controllers\Main;

use App\Item;
use App\Category;
use App\CategorySecond;
use App\Tag;
use App\TagRelation;
use App\Fix;
use App\Setting;
use App\ItemImage;
use App\Favorite;
use App\ItemStockChange;
use App\TopSetting;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class HomeController extends Controller
{
    public function __construct(Item $item, Category $category, CategorySecond $cateSec, Tag $tag, TagRelation $tagRel, Fix $fix, Setting $setting, ItemImage $itemImg, Favorite $favorite, ItemStockChange $itemSc, TopSetting $topSet)
    {
        //$this->middleware('search');
        
        $this->item = $item;
        $this->category = $category;
        $this->cateSec = $cateSec;
        $this->tag = $tag;
        $this->tagRel = $tagRel;
        $this->fix = $fix;
        $this->tag = $tag;
        $this->setting = $setting;
        $this->itemImg = $itemImg;
        $this->favorite = $favorite;
        $this->itemSc = $itemSc;
        $this->topSet = $topSet;
//        $this->tagRelation = $tagRelation;
//        $this->tagGroup = $tagGroup;
//        $this->category = $category;
//        $this->item = $item;
//        $this->fix = $fix;
//        $this->totalize = $totalize;
//        $this->totalizeAll = $totalizeAll;
        
        $this->perPage = env('PER_PAGE', 20);
        $this->itemPerPage = 15;
        
    }
    
    public function index(Request $request)
    {
//        $request->session()->forget('item.data');
//        $request->session()->forget('all');

        $cates = $this->category->all();
        
        $whereArr = ['open_status'=>1, 'is_potset'=>0];
        $whereArrSec = ['open_status'=>1/*,'feature'=>1*/];
        
        
//        $tagIds = TagRelation::where('item_id', 1)->get()->map(function($obj){
//            return $obj->tag_id;
//        })->all();
//        
//        $strs = implode(',', $tagIds);
        
//        $placeholder = '';
//        foreach ($tagIds as $key => $value) {
//           $placeholder .= ($key == 0) ? $value : ','.$value;
//        }
//        //exit;
//        
//    //    $strs = "FIELD(id, $strs)";
//    //    echo $strs;
//        //exit;
//        
//        //->orderByRaw("FIELD(id, $sortIDs)"
//        $tags = Tag::whereIn('id', $tagIds)->orderByRaw("FIELD(id, $placeholder)")->take(2)->get();
//        print_r($tags);
//        exit;
        
//        $stateObj = null;
//        //$stateName = '';
//        
//        if(isset($state)) {
//            $stateObj = $this->state->where('slug', $state)->get()->first();
//            $whereArr['state_id'] = $stateObj->id;
//            $whereArrSec['state_id'] = $stateObj->id;
//            //$stateName = $stateObj->name;
//        }

		//Carousel
        $caros = $this->itemImg->where(['item_id'=>9999, 'type'=>6])->get();

		//FirstItem =======================
        $getNum = 4;
		
        //New
        $newItems = null;
        
        $scIds = $this->itemSc->orderBy('updated_at','desc')->get()->map(function($isc){
        	return $isc->item_id;
        })->all();
        
        if(count($scIds) > 0) {
            $scIdStr = implode(',', $scIds);
            $newItems = $this->item->whereIn('id', $scIds)->where($whereArr)->orderByRaw("FIELD(id, $scIdStr)")->take($getNum)->get()->all();
        }
        
        //Ranking
        $rankItems = $this->item->where($whereArr)->orderBy('sale_count', 'desc')->take($getNum)->get()->all();
        
        //Recent 最近見た
        $cacheIds = array();
        $cacheItems = null;
        //$getNum = Ctm::isAgent('sp') ? 6 : 7;
        
        if(cache()->has('cacheIds')) {
        	
        	$cacheIds = cache('cacheIds');
            
//            print_r($cacheIds);
//            exit;
            
            $caches = implode(',', $cacheIds); //orderByRowに渡すものはString
//            echo $caches;
//            exit;
            
          	$cacheItems = $this->item->whereIn('id', $cacheIds)->where($whereArr)->orderByRaw("FIELD(id, $caches)")->take($getNum)->get()->all();  
        }
        
        //array
        $firstItems = [
        	'新着情報'=> $newItems,
            '人気ランキング'=> $rankItems,
            '最近チェックしたアイテム'=> $cacheItems,
        ];
        //FirstItem END ================================
        
        
        //おすすめ情報 RecommendInfo (cate & cateSecond & tag)
        $tagRecoms = $this->tag->where(['is_top'=>1])->orderBy('updated_at', 'desc')->get()->all();
        $cateRecoms = $this->category->where(['is_top'=>1])->orderBy('updated_at', 'desc')->get()->all();
        $subCateRecoms = $this->cateSec->where(['is_top'=>1])->orderBy('updated_at', 'desc')->get()->all();
        
        $res = array_merge($tagRecoms, $cateRecoms, $subCateRecoms);
        
//        $books = array(
//        	$tagRecoms,
//            $cateRecoms,
//            $subCateRecoms
//        );
        
        $collection = collect($res);
        $allRecoms = $collection->sortByDesc('updated_at');
        
//        print_r($allRecoms);
//        exit;

        //$allRecoms = $this->item->where($whereArr)->orderBy('created_at', 'desc')->take(10)->get(); 

		//category
        $itemCates = array();
        foreach($cates as $cate) { //カテゴリー名をkeyとしてatclのかたまりを配列に入れる
        
            $whereArr['cate_id'] = $cate->id;
            
            $as = $this->item->where($whereArr)->orderBy('created_at','DESC')->take(8)->get()->all();
            
            if(count($as) > 0) {
                $itemCates[$cate->id] = $as;
            }
        }
        
//        $items = $this->item->where(['open_status'=>1])->orderBy('created_at','DESC')->get();
//        $items = $items->groupBy('cate_id')->toArray();

		//head news
        $setting = $this->topSet->get()->first();
        
		$newsCont = $setting->contents;
		
        $metaTitle = $setting->meta_title;
        $metaDesc = $setting->meta_description;
        $metaKeyword = $setting->meta_keyword;
        
        //For this is top
        $isTop = 1;
        

        return view('main.home.index', ['firstItems'=>$firstItems, 'allRecoms'=>$allRecoms, 'itemCates'=>$itemCates, 'cates'=>$cates, 'newsCont'=>$newsCont, 'metaTitle'=>$metaTitle, 'caros'=>$caros, 'metaDesc'=>$metaDesc, 'metaKeyword'=>$metaKeyword, 'isTop'=>$isTop,]);
    }
    
    
    //NewItem Ranking RecentCheck
    public function uniqueArchive(Request $request)
    {
    	$path = $request->path();
        
        $whereArr = ['open_status'=>1, 'is_potset'=>0];
        
        $items = null;
        $title = '';
        
        if($path == 'new-items') {
        
            $scIds = $this->itemSc->orderBy('updated_at','desc')->get()->map(function($isc){
                return $isc->item_id;
            })->all();
            
            if(count($scIds) > 0) {
                $scIdStr = implode(',', $scIds);
                $items = $this->item->whereIn('id', $scIds)->where($whereArr)->orderByRaw("FIELD(id, $scIdStr)")->take(100)->paginate($this->perPage);
            }
            
            $title = '新着情報';
        }
        elseif($path == 'ranking') {
        	$items = $this->item->where($whereArr)->orderBy('sale_count', 'desc')->paginate($this->perPage);
            $title = '人気ランキング';
        }
        elseif($path == 'recent-items') {
        	$cacheIds = array();
            
            if(cache()->has('cacheIds')) {
                
                $cacheIds = cache('cacheIds');
                
                $caches = implode(',', $cacheIds); //orderByRowに渡すものはString
                
                $items = $this->item->whereIn('id', $cacheIds)->where($whereArr)->orderByRaw("FIELD(id, $caches)")->paginate($this->perPage);  
            }
            
            $title = '最近チェックしたアイテム';               
        }
        
        $metaTitle = $title;
        $metaDesc = '';
        $metaKeyword = '';
        
        return view('main.archive.index', ['items'=>$items, 'type'=>'unique', 'title'=>$title, 'metaTitle'=>$metaTitle, 'metaDesc'=>$metaDesc, 'metaKeyword'=>$metaKeyword,]);
 
    }
    
    
    //RecommendInfo : Cate/SubCate/Tag
    public function recomInfo(Request $request)
    {
    	$items = null;
        
        $path = $request->path();
        
    	if($path == 'recommend-info') {

        	$tagRecoms = $this->tag->where(['is_top'=>1])->orderBy('updated_at', 'desc')->get()->all();
            $cateRecoms = $this->category->where(['is_top'=>1])->orderBy('updated_at', 'desc')->get()->all();
            $subCateRecoms = $this->cateSec->where(['is_top'=>1])->orderBy('updated_at', 'desc')->get()->all();
            
//            $aaa = $tagRecoms->merge($cateRecoms);
//            $b = $aaa->paginate($this->perPage);
            
            
            $res = array_merge($tagRecoms, $cateRecoms, $subCateRecoms);
            
            $collection = collect($res);
            $sorts = $collection->sortByDesc('updated_at')->toArray();
            
            //Custom Pagination
            $perPage = $this->perPage;
            $total = count($sorts);
            $chunked = array();
            
            if($total) {
                $chunked = array_chunk($sorts, $perPage);
                $current_page = $request->page ? $request->page : 1;
                $chunked = $chunked[$current_page - 1]; //現在のページに該当する配列を$chunkedに入れる
            }
            
            $items = new LengthAwarePaginator($chunked, $total, $perPage); //pagination インスタンス作成
            $items -> setPath($path); //url pathセット
            //$allResults -> appends(['s' => $search]); //get url set
            //Custom pagination END
            
//            print_r($items);
//            exit;
            
            $title = 'おすすめ情報';
        }
        
        $metaTitle = $title;
        $metaDesc = '';
        $metaKeyword = '';
        
        return view('main.archive.recomInfo', ['items'=>$items, 'type'=>'unique', 'title'=>$title, 'metaTitle'=>$metaTitle, 'metaDesc'=>$metaDesc, 'metaKeyword'=>$metaKeyword,]);
    }
    
    
    
    
	//FIx Page =====================
    public function getFix(Request $request)
    {
        $path = $request->path();
        $fix = $this->fix->where('slug', $path)->first();
        
        if(!isset($fix)) {
            abort(404);
        }
        
        
        $title = $fix->title;
        $type = 'fix';
        
        $metaTitle = isset($fix->meta_title) ? $fix->meta_title : $title;
//        $metaDesc = $item->meta_description;
//        $metaKeyword = $item->meta_keyword;
        
        return view('main.home.fix', ['fix'=>$fix, 'metaTitle'=>$metaTitle, 'title'=>$title, 'type'=>$type]);
    }
    
    //Category ==============================
    public function category($slug)
    {
    	$cate = $this->category->where('slug', $slug)->first();
        
        if(!isset($cate)) {
            abort(404);
        }
        
        $items = $this->item->where(['cate_id'=>$cate->id, 'open_status'=>1, 'is_potset'=>0])->orderBy('id', 'desc')->paginate($this->perPage);
        
        $metaTitle = isset($cate->meta_title) ? $cate->meta_title : $cate->name;
        $metaDesc = $cate->meta_description;
        $metaKeyword = $cate->meta_keyword;
        
        $cate->timestamps = false;
        $cate->increment('view_count');
        
        return view('main.archive.index', ['items'=>$items, 'cate'=>$cate, 'type'=>'category', 'metaTitle'=>$metaTitle, 'metaDesc'=>$metaDesc, 'metaKeyword'=>$metaKeyword,]);
    }
    
    public function subCategory($slug, $subSlug)
    {
    	$cate = $this->category->where('slug', $slug)->first();
        
        if(!isset($cate)) {
            abort(404);
        }
        
        $subcate = $this->cateSec->where('slug',$subSlug)->first();
        
        if(!isset($subcate)) {
            abort(404);
        }
        
        $items = $this->item->where(['subcate_id'=>$subcate->id, 'open_status'=>1, 'is_potset'=>0])->orderBy('id', 'desc')->paginate($this->perPage);
        
        $metaTitle = isset($subcate->meta_title) ? $subcate->meta_title : $subcate->name;
        $metaDesc = $subcate->meta_description;
        $metaKeyword = $subcate->meta_keyword;
        
        $subcate->timestamps = false;
        $subcate->increment('view_count');
        
        return view('main.archive.index', ['items'=>$items, 'cate'=>$cate, 'subcate'=>$subcate, 'type'=>'subcategory', 'metaTitle'=>$metaTitle, 'metaDesc'=>$metaDesc, 'metaKeyword'=>$metaKeyword,]);
    }
    
    public function tag($slug)
    {
    	$tag = $this->tag->where('slug', $slug)->first();
        
        if(!isset($tag)) {
            abort(404);
        }
        
        $itemIds = $this->tagRel->where('tag_id',$tag->id)->get()->map(function($obj){
        	return $obj -> item_id;
        })->all();
        
        $items = $this->item->whereIn('id',$itemIds)->where(['open_status'=>1, 'is_potset'=>0])->orderBy('id', 'desc')->paginate($this->perPage);
        
        $metaTitle = isset($tag->meta_title) ? $tag->meta_title : $tag->name;
        $metaDesc = $tag->meta_description;
        $metaKeyword = $tag->meta_keyword;
        
        $tag->timestamps = false;
        $tag->increment('view_count');
        
        return view('main.archive.index', ['items'=>$items, 'tag'=>$tag, 'type'=>'tag', 'metaTitle'=>$metaTitle, 'metaDesc'=>$metaDesc, 'metaKeyword'=>$metaKeyword,]);
    }
    
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }
}
