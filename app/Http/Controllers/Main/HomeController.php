<?php

namespace App\Http\Controllers\Main;

use App\Item;
use App\Category;
use App\CategorySecond;
use App\Tag;
use App\TagRelation;
use App\Fix;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function __construct(Item $item, Category $category, CategorySecond $cateSec, Tag $tag, TagRelation $tagRel, Fix $fix)
    {
        //$this->middleware('search');
        
        $this->item = $item;
        $this->category = $category;
        $this->cateSec = $cateSec;
        $this->tag = $tag;
        $this->tagRel = $tagRel;
        $this->fix = $fix;
        $this->tag = $tag;
//        $this->tagRelation = $tagRelation;
//        $this->tagGroup = $tagGroup;
//        $this->category = $category;
//        $this->item = $item;
//        $this->fix = $fix;
//        $this->totalize = $totalize;
//        $this->totalizeAll = $totalizeAll;
        
        $this->perPage = env('PER_PAGE', 21);
        $this->itemPerPage = 15;
        
    }
    
    public function index(Request $request)
    {
//        $request->session()->forget('item.data');
//        $request->session()->forget('all');


        $cates = $this->category->all();
        
        $whereArr = ['open_status'=>1];
        $whereArrSec = ['open_status'=>1/*,'feature'=>1*/];
        
        
//        $stateObj = null;
//        //$stateName = '';
//        
//        if(isset($state)) {
//            $stateObj = $this->state->where('slug', $state)->get()->first();
//            $whereArr['state_id'] = $stateObj->id;
//            $whereArrSec['state_id'] = $stateObj->id;
//            //$stateName = $stateObj->name;
//        }
        
        //新着3件 carousel
        //$items = $this->item->where($whereArr)->orderBy('created_at','DESC')->take(21)->get();
        $itemCates = array();
        foreach($cates as $cate) { //カテゴリー名をkeyとしてatclのかたまりを配列に入れる
        
            $whereArr['cate_id'] = $cate->id;
            
            $as = $this->item->where($whereArr)->orderBy('created_at','DESC')->take(8)->get()->all();
            
            if(count($as) > 0) {
                $itemCates[$cate->name] = $as;
            }
        }
//        print_r($itemCates);
//        exit;
        
//        $items = $this->item->where(['open_status'=>1¥])->orderBy('created_at','DESC')->get();
//        $items = $items->groupBy('cate_id')->toArray();
        
        
        return view('main.home.index', ['itemCates'=>$itemCates, 'cates'=>$cates,]);
    }

    public function getFix(Request $request)
    {
        $path = $request->path();
        $fix = $this->fix->where('slug', $path)->first();
        
        return view('main.home.fix', ['fix'=>$fix]);
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
