@extends('layouts.appDashBoard')

@section('content')
	
	<div class="text-left">
        <h1 class="Title">
        @if(isset($edit))
        商品編集
        @else
        商品新規追加
        @endif
        </h1>
        <p class="Description"></p>
    </div>

    <div class="row">
      <div class="col-sm-12 col-md-6 col-lg-6 col-xl-5 mb-5">
        <div class="bs-component clearfix">
        <div class="pull-left">
            <a href="{{ url('/dashboard/items') }}" class="btn bg-white border border-1 border-round border-secondary text-primary"><i class="fa fa-angle-double-left" aria-hidden="true"></i>一覧へ戻る</a>
        </div>
    	</div>
    </div>
  </div>



    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>Error!!</strong> 追加できません<br><br>
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
        <form class="form-horizontal" role="form" method="POST" action="/dashboard/items" enctype="multipart/form-data">
        
        	<div class="form-group mb-5">
                <div class="clearfix">
                    <button type="submit" class="btn btn-primary btn-block mx-auto w-btn w-25">更　新</button>
                </div>
            </div>
        
			@if(isset($edit))
                <input type="hidden" name="edit_id" value="{{$id}}">
            @endif

            {{ csrf_field() }}
            
            @if(isset($edit))
                <input type="hidden" name="edit_id" value="{{$id}}">
            @endif

		<div class="form-group">
                <div class="col-md-12 text-right">
                    <div class="checkbox">
                        <label>
                            <?php
                                $checked = '';
                                if(Ctm::isOld()) {
                                    if(old('open_status'))
                                        $checked = ' checked';
                                }
                                else {
                                    if(isset($atcl) && ! $atcl->open_status) {
                                        $checked = ' checked';
                                    }
                                }
                            ?>
                            <input type="checkbox" name="open_status" value="1"{{ $checked }}> この商品を非公開にする
                        </label>
                    </div>
                </div>
            </div>
	
		<div class="form-group clearfix mb-4 thumb-wrap">
            <div class="float-left col-md-5 thumb-prev">
                @if(count(old()) > 0)
                    @if(old('main_img') != '' && old('main_img'))
                    <img src="{{ Storage::url(old('main_img')) }}" class="img-fluid">
                    @elseif(isset($item) && $item->main_img)
                    <img src="{{ Storage::url($item->main_img) }}" class="img-fluid">
                    @else
                    <span class="no-img">No Image</span>
                    @endif
                @elseif(isset($item) && $item->main_img)
                <img src="{{ Storage::url($item->main_img) }}" class="img-fluid">
                @else
                <span class="no-img">No Image</span>
                @endif
            </div>

            <div class="float-left col-md-7">
                <fieldset class="form-group{{ $errors->has('main_img') ? ' is-invalid' : '' }}">
                    <label for="main_img">メイン画像</label>
                    <input class="form-control-file thumb-file" id="main_img" type="file" name="main_img">
                </fieldset>
            </div>
            
            @if ($errors->has('main_img'))
                <span class="help-block text-danger">
                    <strong>{{ $errors->first('main_img') }}</strong>
                </span>
            @endif
        </div>
        
        <div class="form-group clearfix">
            @for($i=0; $i< 10; $i++)
                @include('dashboard.item.img')
            @endfor
    	</div>            
  
        
			<fieldset class="mb-4 form-group{{ $errors->has('number') ? ' has-error' : '' }}">
                <label>商品番号</label>
                <input class="form-control{{ $errors->has('number') ? ' is-invalid' : '' }}" name="number" value="{{ Ctm::isOld() ? old('number') : (isset($item) ? $item->number : '') }}">

                @if ($errors->has('number'))
                    <div class="text-danger">
                        <span class="fa fa-exclamation form-control-feedback"></span>
                        <span>{{ $errors->first('number') }}</span>
                    </div>
                @endif
            </fieldset>
            
            <fieldset class="mb-4 form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                <label>商品名</label>
                <input class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" name="title" value="{{ Ctm::isOld() ? old('title') : (isset($item) ? $item->title : '') }}">

                @if ($errors->has('title'))
                    <div class="text-danger">
                        <span class="fa fa-exclamation form-control-feedback"></span>
                        <span>{{ $errors->first('title') }}</span>
                    </div>
                @endif
            </fieldset>
            
            <fieldset class="mb-4 form-group{{ $errors->has('catchcopy') ? ' has-error' : '' }}">
                <label>キャッチコピー</label>
                <input class="form-control{{ $errors->has('catchcopy') ? ' is-invalid' : '' }}" name="catchcopy" value="{{ Ctm::isOld() ? old('catchcopy') : (isset($item) ? $item->catchcopy : '') }}">

                @if ($errors->has('catchcopy'))
                    <div class="text-danger">
                        <span class="fa fa-exclamation form-control-feedback"></span>
                        <span>{{ $errors->first('catchcopy') }}</span>
                    </div>
                @endif
            </fieldset>
            
            
            
            <div class="mb-4 form-group{{ $errors->has('cate_id') ? ' has-error' : '' }}">
                <label>カテゴリー</label>
                <select class="form-control select-first col-md-6" name="cate_id">
                    <option disabled selected>選択して下さい</option>
                    @foreach($cates as $cate)
                        <?php
                            $selected = '';
                            if(Ctm::isOld()) {
                                if(old('cate_id') == $cate->id)
                                    $selected = ' selected';
                            }
                            else {
                                if(isset($item) && $item->cate_id == $cate->id) {
                                    $selected = ' selected';
                                }
                            }
                        ?>
                        <option value="{{ $cate->id }}"{{ $selected }}>{{ $cate->name }}</option>
                    @endforeach
                </select>
                
                @if ($errors->has('cate_id'))
                    <span class="help-block text-warning">
                        <strong>{{ $errors->first('cate_id') }}</strong>
                    </span>
                @endif
                
            </div>
            
            
            <fieldset class="mb-4 form-group{{ $errors->has('price') ? ' has-error' : '' }}">
                <label for="price" class="control-label">価格</label>
                <input class="form-control col-md-6" name="price" value="{{ Ctm::isOld() ? old('price') : (isset($item) ? $item->price : '') }}">
                

                @if ($errors->has('price'))
                    <div class="text-danger">
                        <span class="fa fa-exclamation form-control-feedback"></span>
                        <span>{{ $errors->first('price') }}</span>
                    </div>
                @endif
            </fieldset>
            
            <fieldset class="mb-4 form-group{{ $errors->has('cost_price') ? ' has-error' : '' }}">
                <label for="cost_price" class="control-label">仕入れ値</label>
                <input class="form-control col-md-6" name="cost_price" value="{{ Ctm::isOld() ? old('cost_price') : (isset($item) ? $item->cost_price : '') }}">
                

                @if ($errors->has('cost_price'))
                    <div class="text-danger">
                        <span class="fa fa-exclamation form-control-feedback"></span>
                        <span>{{ $errors->first('cost_price') }}</span>
                    </div>
                @endif
            </fieldset>
            
            <div class="mb-4 form-group{{ $errors->has('consignor_id') ? ' has-error' : '' }}">
                <label>出荷元</label>
                <select class="form-control select-first col-md-6" name="consignor_id">
                    <option selected>選択して下さい</option>
                    @foreach($consignors as $consignor)
                        <?php
                            $selected = '';
                            if(Ctm::isOld()) {
                                if(old('consignor_id') == $consignor->id)
                                    $selected = ' selected';
                            }
                            else {
                                if(isset($item) && $item->consignor_id == $consignor->id) {
                                    $selected = ' selected';
                                }
                            }
                        ?>
                        <option value="{{ $consignor->id }}"{{ $selected }}>{{ $consignor->name }}</option>
                    @endforeach
                </select>
                
                @if ($errors->has('consignor_id'))
                    <span class="help-block text-warning">
                        <strong>{{ $errors->first('consignor_id') }}</strong>
                    </span>
                @endif
                
            </div>
            
            

            <div class="mb-4 form-group{{ $errors->has('cod') ? ' has-error' : '' }}">
                <label>代金引換設定</label>
                <select class="form-control select-first col-md-6" name="cod">
                    <option disabled selected>選択して下さい</option>
                        <?php
                        	$cods = array( 'なし', 'あり');
                        ?>
                    @foreach($cods as $key => $cod)
                        <?php
                            $selected = '';
                            if(Ctm::isOld()) {
                                if(old('cod') == $key)
                                    $selected = ' selected';
                            }
                            else {
                                if(isset($item) && $item->cod == $key) {
                                    $selected = ' selected';
                                }
                            }
                        ?>
                        <option value="{{ $key }}"{{ $selected }}>{{ $cod }}</option>
                    @endforeach
                </select>
                
                @if ($errors->has('cod'))
                    <span class="help-block text-warning">
                        <strong>{{ $errors->first('cod') }}</strong>
                    </span>
                @endif
                
            </div>
            
            <fieldset class="mb-4 form-group{{ $errors->has('stock') ? ' has-error' : '' }}">
                <label for="stock" class="control-label">在庫数</label>
                <input class="form-control col-md-6" name="stock" value="{{ Ctm::isOld() ? old('stock') : (isset($item) ? $item->stock : '') }}">
                

                @if ($errors->has('stock'))
                    <div class="text-danger">
                        <span class="fa fa-exclamation form-control-feedback"></span>
                        <span>{{ $errors->first('stock') }}</span>
                    </div>
                @endif
            </fieldset>

            
            <fieldset class="mb-4 form-group{{ $errors->has('about_ship') ? ' has-error' : '' }}">
                    <label for="detail" class="control-label">配送について</label>

                        <textarea id="detail" type="text" class="form-control" name="detail" rows="15">{{ Ctm::isOld() ? old('detail') : (isset($item) ? $item->detail : '') }}</textarea>

                        @if ($errors->has('detail'))
                            <span class="help-block">
                                <strong>{{ $errors->first('detail') }}</strong>
                            </span>
                        @endif
            </fieldset>
            
            <fieldset class="mb-4 form-group{{ $errors->has('explain') ? ' has-error' : '' }}">
                    <label for="explain" class="control-label">説明</label>

                    <textarea id="explain" type="text" class="form-control" name="explain" rows="15">{{ Ctm::isOld() ? old('explain') : (isset($item) ? $item->explain : '') }}</textarea>

                    @if ($errors->has('explain'))
                        <span class="help-block">
                            <strong>{{ $errors->first('explain') }}</strong>
                        </span>
                    @endif
            </fieldset>
            
            <fieldset class="mb-4 form-group{{ $errors->has('detail') ? ' has-error' : '' }}">
                    <label for="detail" class="control-label">商品情報</label>

                    <textarea id="detail" type="text" class="form-control" name="detail" rows="15">{{ Ctm::isOld() ? old('detail') : (isset($item) ? $item->detail : '') }}</textarea>

                    @if ($errors->has('detail'))
                        <span class="help-block">
                            <strong>{{ $errors->first('detail') }}</strong>
                        </span>
                    @endif
            </fieldset>
            
            
            <div class="clearfix tag-wrap">

                <div class="tag-group form-group{{ $errors->has('tag-group') ? ' has-error' : '' }}">
                    <label for="tag-group" class="control-label">タグ</label>
                    <div class="clearfix">
                        <input id="tag-group" type="text" class="form-control col-md-5 tag-control" name="input-tag-group" value="" autocomplete="off" placeholder="Enter tag">

                        <div class="add-btn" tabindex="0">追加</div>

                        <span style="display:none;">{{ implode(',', $allTags) }}</span>

                        <div class="tag-area">
                            @if(count(old()) > 0)
                                <?php
                                    //$tagNames = old($tag->slug);
                                    $tagNames = old('tags');
                                ?>
                            @endif

                            @if(isset($tagNames))
                                @foreach($tagNames as $name)
                                <span><em>{{ $name }}</em><i class="fa fa-times del-tag" aria-hidden="true"></i></span>
                                <input type="hidden" name="tags[]" value="{{ $name }}">
                                @endforeach
                            @endif

                        </div>

                    </div>

                </div>

            </div><?php //tagwrap ?>
            
            
            <fieldset class="mb-4 form-group{{ $errors->has('what_is') ? ' has-error' : '' }}">
                    <label for="story_text" class="control-label">What is</label>

                        <textarea id="what_is" type="text" class="form-control" name="what_is" rows="10">{{ Ctm::isOld() ? old('what_is') : (isset($item) ? $item->what_is : '') }}</textarea>

                        @if ($errors->has('what_is'))
                            <span class="help-block">
                                <strong>{{ $errors->first('what_is') }}</strong>
                            </span>
                        @endif
            </fieldset>
            
            
            <fieldset class="mb-4 form-group{{ $errors->has('warning') ? ' has-error' : '' }}">
                    <label for="warning" class="control-label">Warning</label>

                        <textarea id="warning" type="text" class="form-control" name="warning" rows="10">{{ Ctm::isOld() ? old('warning') : (isset($item) ? $item->warning : '') }}</textarea>

                        @if ($errors->has('warning'))
                            <span class="help-block">
                                <strong>{{ $errors->first('warning') }}</strong>
                            </span>
                        @endif
            </fieldset>
            
            
            <div class="form-group">
                <div class="">
                    <button type="submit" class="btn btn-primary btn-block w-btn w-25 mx-auto">更　新</button>
                </div>
            </div>


            

        </form>

    </div>

    

@endsection
