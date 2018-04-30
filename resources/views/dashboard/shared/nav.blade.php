<!-- Navigation-->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
    <a class="navbar-brand" href="{{ url('dashboard') }}">グリーンロケット</a>
    
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarResponsive">
      
      <ul class="navbar-nav navbar-sidenav" id="exampleAccordion">

		<li class="nav-item" data-toggle="tooltip" data-placement="right" title="Dashboard">
			<a class="nav-link" href="{{ url('/dashboard') }}">
            	<i class="fa fa-fw fa-dashboard"></i>
            	<span class="nav-link-text">Dashboard</span>
          	</a>
        </li>
        
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Dashboard">
            <a class="nav-link" href="{{ url('dashboard/register') }}">
                <i class="fa fa-fw fa-dashboard"></i>
                <span class="nav-link-text">管理者設定</span>
              </a>
        </li>
        
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Dashboard">
            <a class="nav-link" href="{{ url('dashboard/setting') }}">
                <i class="fa fa-fw fa-dashboard"></i>
                <span class="nav-link-text">サイト設定</span>
              </a>
        </li>
        
        <div class="border border-secondary border-top-0 w-75 mx-auto"></div>
        
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Example Pages">
          <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#collapseItem" data-parent="#exampleAccordion">
            <i class="fa fa-fw fa-file"></i>
            <span class="nav-link-text">商品管理</span>
          </a>
          <ul class="sidenav-second-level collapse" id="collapseItem">
            <li>
              <a href="{{ url('dashboard/items') }}">商品一覧</a>
            </li>
            <li>
              <a href="{{ url('dashboard/items/create') }}">商品新規登録</a>
            </li>
            
          </ul>
        </li>
        
    
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Example Pages">
          <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#collapseCate" data-parent="#exampleAccordion">
            <i class="fa fa-fw fa-file"></i>
            <span class="nav-link-text">カテゴリー</span>
          </a>
          <ul class="sidenav-second-level collapse" id="collapseCate">
            <li>
              <a href="{{ url('dashboard/categories') }}">カテゴリー一覧</a>
            </li>
            <li>
              <a href="{{ url('dashboard/categories/create') }}">カテゴリー新規登録</a>
            </li>

          </ul>
        </li>
        
    
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Example Pages">
          <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#collapseTag" data-parent="#exampleAccordion">
            <i class="fa fa-fw fa-file"></i>
            <span class="nav-link-text">タグ管理</span>
          </a>
          <ul class="sidenav-second-level collapse" id="collapseTag">
            <li>
              <a href="{{ url('dashboard/tags') }}">タグ一覧</a>
            </li>
            <li>
              <a href="{{ url('dashboard/tags/create') }}">タグ新規登録</a>
            </li>

          </ul>
        </li>
        
		
  		<div class="border border-secondary border-top-0 w-75 mx-auto"></div>
    
    	<li class="nav-item" data-toggle="tooltip" data-placement="right" title="Example Pages">
          <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#collapseUser" data-parent="#exampleAccordion">
            <i class="fa fa-fw fa-file"></i>
            <span class="nav-link-text">会員管理</span>
          </a>
          <ul class="sidenav-second-level collapse" id="collapseUser">
            <li>
              <a href="{{ url('dashboard/items') }}">会員一覧</a>
            </li>
            <li>
              <a href="{{ url('dashboard/items/create') }}">会員登録</a>
            </li>

          </ul>
        </li> 
        
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Example Pages">
          <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#collapseSale" data-parent="#exampleAccordion">
            <i class="fa fa-fw fa-file"></i>
            <span class="nav-link-text">売上管理</span>
          </a>
          <ul class="sidenav-second-level collapse" id="collapseSale">
            <li>
              <a href="{{ url('dashboard/items') }}">売上一覧</a>
            </li>
            <li>
              {{-- <a href="{{ url('dashboard/items/create') }}">会員登録</a> --}}
            </li>

          </ul>
        </li>        

        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Link">
          <a class="nav-link" href="#">
            <i class="fa fa-fw fa-link"></i>
            <span class="nav-link-text">Link</span>
          </a>
        </li>
      
      </ul>
      
      
      <ul class="navbar-nav sidenav-toggler">
        <li class="nav-item">
          <a class="nav-link text-center" id="sidenavToggler">
            <i class="fa fa-fw fa-angle-left"></i>
          </a>
        </li>
      </ul>
      
      
      
      <ul class="navbar-nav ml-auto">

        <li class="nav-item">
          <a class="nav-link" data-toggle="modal" data-target="#exampleModal">
            <i class="fa fa-fw fa-sign-out"></i>Logout</a>
        </li>

        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle mr-lg-2" id="adminDropdown" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-fw fa-user"></i> {{ Auth::guard('admin')->user()->name }}さん
            </a>

            <div style="left:initial; right:0;" class="dropdown-menu" aria-labelledby="adminDropdown">

                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#">
                    <span class="text-primary">
                        <strong><i class="fa fa-arrow-right fa-fw"></i>データ編集</strong>
                    </span>
                </a>

                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#">
                    <span class="text-success">
                        <strong><i class="fa fa-fw fa-sign-out"></i>Logout</strong>
                    </span>
                </a>

            </div>
        </li>
      </ul>


    </div>
</nav>
