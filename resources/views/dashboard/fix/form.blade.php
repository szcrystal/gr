@extends('layouts.appDashBoard')

@section('content')
	
	<h1 class="text-left">
	@if(isset($edit))
    固定ページ編集
	@else
	固定ページ新規追加
    @endif
    </h1>

    <div class="row">
      <div class="col-sm-12 col-md-6 col-lg-6 col-xl-5 mb-5">
        <div class="bs-component clearfix">
        <div class="pull-left">
            <a href="{{ url('/dashboard/fixes') }}" class="btn bg-white border border-1 border-round border-secondary text-primary"><i class="fa fa-angle-double-left" aria-hidden="true"></i>一覧へ戻る</a>
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


    <div class="col-lg-11">
        <form class="form-horizontal" role="form" method="POST" action="/dashboard/fixes">
			

            {{ csrf_field() }}
            
            @if(isset($edit))
                <input type="hidden" name="edit_id" value="{{$id}}">
            @endif

            <fieldset class="form-group mb-4 text-right">
                    <div class="checkbox">
                    	<?php
                            if(count(old()))
                            	$checked = old('not_open') ? ' checked' : '';
                            else
                            	$checked = isset($fix) && $fix->not_open ? ' checked' : '';
                        ?>
                        <label>
                            <input type="checkbox" name="not_open" value="1"{{ $checked }}> 非公開にする
                        </label>
                    </div>
            </fieldset>

            <fieldset class="form-group mb-4">
                <label class="control-label">タイトル</label>

                <input id="title" type="text" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" name="title" value="{{ isset($fix) && !count(old()) ? $fix->title : old('title') }}" required>

                    @if ($errors->has('title'))
                        <span class="help-block">
                            <strong>{{ $errors->first('title') }}</strong>
                        </span>
                    @endif
            </fieldset>

            <fieldset class="form-group mb-4">
                <label class="control-label">サブタイトル（リンク名）</label>

                    <input id="sub_title" type="text" class="form-control{{ $errors->has('sub_title') ? ' is-invalid' : '' }}" name="sub_title" value="{{ isset($fix) && !count(old()) ? $fix->sub_title : old('sub_title') }}" required>

                    @if ($errors->has('sub_title'))
                        <span class="help-block">
                            <strong>{{ $errors->first('sub_title') }}</strong>
                        </span>
                    @endif
            </fieldset>

            <fieldset class="form-group mb-4">
                <label class="control-label">スラッグ</label>

                    <input id="slug" type="text" class="form-control{{ $errors->has('slug') ? ' is-invalid' : '' }}" name="slug" value="{{ isset($fix) && !count(old()) ? $fix->slug : old('slug') }}" required>

                    @if ($errors->has('slug'))
                        <span class="help-block">
                            <strong>{{ $errors->first('slug') }}</strong>
                        </span>
                    @endif
            </fieldset>

            <fieldset class="form-group mb-4">
                <label class="control-label">コンテンツ</label>

                    <textarea id="contents" class="form-control{{ $errors->has('contents') ? ' is-invalid' : '' }}" name="contents" rows="30">{{ isset($fix) && !count(old()) ? $fix->contents : old('contents') }}</textarea>

                    @if ($errors->has('contents'))
                        <span class="help-block">
                            <strong>{{ $errors->first('contents') }}</strong>
                        </span>
                    @endif
            </fieldset>


              <div class="form-group mt-5">
                <div class="">
                    <button type="submit" class="btn btn-primary btn-block mx-auto w-25"><span class="octicon octicon-sync"></span>更　新</button>
                </div>
            </div>

        </form>

    </div>

    

@endsection
