(function($) {

var exe = (function() {

	return {
    
		opts: {
            crtClass: 'crnt',
            btnID: '.top_btn',
            all: 'html, body',
            animEnd: 'webkitAnimationEnd MSAnimationEnd oanimationend animationend', //mozAnimationEnd
            transitEnd: 'webkitTransitionEnd MSTransitionEnd otransitionend transitionend', //mozTransitionEnd 
        },
        
        scrollFunc: function() {
            var t = this,
                tb = $(t.opts.btnID);
            
            tb.css('display','none').on('click', function() {
                $(t.opts.all).animate({ scrollTop:0 }, 1200, 'easeOutExpo');
            });

            $(document).scroll(function(){

                if($(this).scrollTop() < 200)
                    tb.fadeOut(200);
                else 
                    tb.fadeIn(300);
            });
            
        },
        
        
        isAgent: function(user) {
            if( navigator.userAgent.indexOf(user) > 0 ) return true;
        },
        
        isLocal: function() {
        	if( location.port == 8006 ) return true;
        },
        
        isSpTab: function(arg) {

        	var spArr = ['iPhone','iPod','Mobile ','Mobile;','Windows Phone','IEMobile'];
            var tabArr = ['iPad','Kindle','Sony Tablet','Nexus 7','Android Tablet'];
            var arr = [];
            
            if(arg == 'sp')
            	arr = spArr;
            
            else if(arg == 'tab')
            	arr = tabArr;
            
            else
            	arr = spArr.concat(tabArr);
            
        	
            var th = this;
            var bool = false;
            
            arr.forEach(function(e, i, a) {
            	if(th.isAgent(e)) {
                	bool = true;
                    return; //Exit
                }
            });
            
            return bool;
        },
        
        
        
        
        put: function(tag, argText) {
            $(tag).text(argText);
            console.log("CheckText is『 %s 』" , argText);
        },
        
        
        
        addClass: function() {
        	//$('.add-item').find('.item-panel').eq(0).addClass('first-panel');
            $('.item-panel').eq(0).css({border:'2px solid green'});
        },
        
        nl2br: function(str) {
            str = str.replace(/\r\n/g, "<br>");
            str = str.replace(/(\n|\r)/g, "<br>");
            return str;
        },
        
        searchSlide: function() {
        	$input = $('.s-form input');
           
           	if(this.isSpTab('sp')) {
           
                $('.btn-s').on('click', function(){
                	var $nav = $('.main-navigation');
                    
                    if($nav.is(':visible')) {
                    	
                        var top = $nav.data('top');
                    	$('html,body').css({position:'static'}).scrollTop(top);
                    	
                        $nav.slideUp(200, 'linear', function(){
                            $('.menu-dropdown').hide();
                            $('.s-form > div').slideToggle(220);
                        });
                    }
                    else {
                    	$('.s-form > div').slideToggle(220);
                    }
                    
                    
                });
           	}
            else {
                $('.btn-s').on('click', function(){
                    if($input.is(':hidden')) {
                        $input.show().animate({width:'14em', opacity:1}, 300, 'linear', function(){
                    		//$(this).queue([]).stop();
                        });
                    }
                    else {
                        $input.animate({width:0, opacity:0}, 300, 'linear', function(){
                    		//$(this).queue([]).stop();
                        }).fadeOut(200);
                    
                    }
                });
                
            }
        },
        
        toggleSp: function() {
           
        	$('.head-navi .fa-search').on('click', function(){
            	$('.searchform').slideToggle(150);
            });
           
            var t;
            $('.nav-tgl').on('click', function(){
            	var $leftbar = $('.main-navigation');
                
                var h = $(window).height();
                h = h-60;
                
                $leftbar.find('.panel-body').css({height:h});

            	if($leftbar.is(':visible')) {
                	$('.fade-black').css({height:0}).fadeOut(50);
                    
                	$leftbar.stop().animate({left:'-200px'}, 80, 'linear', function(){
                    	$(this).hide(0);
                        $('html,body').css({position:'static'}).scrollTop(t);
                    });
                }
                else {
                	t = $(window).scrollTop();
                    
                    $('.fade-black').css({height:h}).fadeIn(100);
                    
            		$leftbar.show(50, function(){
                    	$(this).stop().animate({left:0}, 100);
                        $('html,body').css({position:'fixed', top:-t}); //overflow:'hidden',
                    });
                }
                //$('.navbar-brand').text(t);
            });

            //SP Only
//            $('.site-header .nav-tgl').on('click', function(){
//            	
//                var $nav = $('.main-navigation');
//                var $sForm = $('.s-form > div');
//                var t = $(window).scrollTop();
//                
//                if($nav.is(':visible')) {
//                	var top = $nav.data('top');
//                    $('html,body').css({position:'static'}).scrollTop(top);
//                }
//                else {
//                    $('html,body').css({position:'fixed', top:-t});
//                    $nav.data('top', t);
//                }
//
//				if($sForm.is(':visible')) {
//                	$sForm.slideUp(200, function(){
//                        $nav.slideToggle(300, 'linear', function(){
//                            $('.menu-dropdown').hide();
//                        });
//                        
//                        $(this).queue([]).stop();
//                    });
//                }
//                else {
//                    $nav.slideToggle(300, 'linear', function(){
//                        $('.menu-dropdown').hide();
//                    });
//                }
//                
//            });
        },
        
        
        postNumSet: function() {
        	$('#zipcode').jpostal({
                postcode : [
                    '#zipcode'
                ],
                address : {
                    '#pref':'%3',
                    '#address':'%4%5'
                }
            });
            
            $('#zipcode_2').jpostal({
                postcode : [
                    '#zipcode_2'
                ],
                address : {
                    '#pref_2':'%3',
                    '#address_2':'%4%5'
                }
            });
            
        },
        
        
        dropDown: function() {
        	var $mainNav = $('.main-navi > ul > li');
            
        	//var len = $('.state-nav li').length;
            var num = 0;
           
            var speed = 350;
           	var easing = 'linear';
           
           	var hideSpeed = this.isSpTab('sp') ? 150 : 100;
            //console.log(len);
           
           	//$('.menu-dropdown').eq(1).slideToggle(200);
            
            $mainNav.on('click', function(e){
            	//console.log('bbb');
				
                var $clickThis = $(this);
                var $dropMenu = $('.drops');
                
                var n = $clickThis.index();
                
                $(e.target).addClass('nav-active');
                
                if($dropMenu.eq(n).is(':visible')) {
                	
                    if(! $(e.target).hasClass('drops') ) {
                        $clickThis.removeClass('nav-active');
                        
                        $dropMenu.fadeOut(speed, easing, function() {
                            $(this).queue([]).stop();
                        });
                    }
                }
                else {
                	//console.log('ccc');

                    $dropMenu.fadeOut(hideSpeed, easing, function(){
                        $mainNav.removeClass('nav-active');
                        $clickThis.addClass('nav-active');
                        
                        $clickThis.children('.drops').slideDown(speed, easing, function() {
                            $(this).queue([]).stop();
                        });
                    
                    });
                    
                }
                
                //return false;

            });
            
            $('body').on('click', function(e){
            	var $dropMenu = $('.drops');
                
                if( ! $(e.target).hasClass('drops') ) {
                	
                    //console.log("aaa");
                    
                    if($dropMenu.is(':visible')) {
                        
                        $('.main-navi li').removeClass('nav-active');
                        
                        $dropMenu.fadeOut(hideSpeed, easing, function() {
                            $(this).queue([]).stop();
                        });
                    }
                }
            });

            
            /*
            $mainNav.on({
            	'mouseover': function(e){
				
                    var $clickThis = $(this);
                    var $dropMenu = $('.drops');
                    
                    var n = $(this).index();
                    
                    $(e.target).addClass('nav-active');
                    
                    if($dropMenu.eq(n).is(':visible')) {
                        
                        $clickThis.removeClass('nav-active');
                        
                        $dropMenu.fadeOut(speed, easing, function() {
                            $(this).queue([]).stop();
                        });
                    }
                    else {
                    
                        $dropMenu.fadeOut(hideSpeed, function(){
                            $mainNav.removeClass('nav-active');
                            $clickThis.addClass('nav-active');
                            
                            $clickThis.children('.drops').fadeIn(speed, easing, function() {
                                $(this).queue([]).stop();
                            });
                        
                        });
                    }
                },
                
                'mouseout': function(e){
				
                    var $clickThis = $(this);
                    var $dropMenu = $('.drops');
                    
                    var n = $(this).index();
                    
                    $(e.target).addClass('nav-active');
                    
                    if($dropMenu.eq(n).is(':visible')) {
                        
                        $clickThis.removeClass('nav-active');
                        
                        $dropMenu.fadeOut(speed, easing, function() {
                            $(this).queue([]).stop();
                        });
                    }
                    else {
                    
                        $dropMenu.fadeOut(hideSpeed, function(){
                            $mainNav.removeClass('nav-active');
                            $clickThis.addClass('nav-active');
                            
                            $clickThis.children('.drops').fadeIn(speed, easing, function() {
                                $(this).queue([]).stop();
                            });
                        
                        });
                    }
                }
                
            });
            */
            
           
           
        },
        
        
        eventItem: function() {
           
            //Thumbnail Upload
            $('.thumb-file').on('click', function(){
            	var $th = $(this);
                $th.on('change', function(e){
                	var file = e.target.files[0],
                    reader = new FileReader(),
                    $preview = $(this).parents('.thumb-wrap').find('.thumb-prev');
                    //t = this;

                    // 画像ファイル以外の場合は何もしない
                    if(file.type.indexOf("image") < 0){
                      return false;
                    }

                    // ファイル読み込みが完了した際のイベント登録
                    reader.onload = (function(file) {
                      return function(e) {
                        //既存のプレビューを削除
                        $preview.empty();
                        // .prevewの領域の中にロードした画像を表示するimageタグを追加
                        $preview.append($('<img>').attr({
                                  src: e.target.result,
                                  width: "100%",
                                  //class: "preview",
                                  title: file.name
                        }));
                        //console.log(file.name);

                    };
                })(file);

                reader.readAsDataURL(file);
                });
            	
            });
        
        },
        
        outReceive: function() {
        	var $destination = $('input[name="destination"]');
         	var $em = $('.receiver').find('em');
             
              if($destination.is(':checked')) {
                $em.hide();
              }
              else {
                $em.show();
              }

            $destination.on('click', function(){
                if($(this).is(':checked')) { 
                    $em.fadeOut(30);
                    $('.receiver-error:visible').fadeOut(30).siblings().removeClass('is-invalid');
                    //$('.receiver-error:visible');
                }
                else {
                    $em.fadeIn(30);
                }
            });
        },
        
        addFavorite: function() {
        	var $fav= $('.fav');
            var $favOn = $('.fav-on');
            var $favOff = $('.fav-off'); 
          
//              var url = $(this).parent('div').find('.link-url').val(); //input type=text
//            var $frame = $(this).parent('div').find('.link-frame');
//            //console.log(url);     
         
             $fav.on('click', function(e){
             	var $th = $(this);
                var _itemId = $(this).data('id');
                var _tokenVal = $('input[name=_token]').val();
                var _isOn = 0;
                
                if($th.hasClass('fav-on')) {
                	_isOn = 1;
//                     console.log(_isOn);
//                      exit;     
                }
                
				//$th.removeClass('d-inline').fadeOut(100, function(){
    				$('.loader').fadeIn(10);
                //});
                

                //controllerでajax処理する場合、_tokenも送る必要がある
                $.ajax({
                    url: '/item/script',
                    type: "POST",
                    cache: false,
                    data: {
                        _token: _tokenVal,
                        itemId: _itemId,
                        isOn: _isOn,
                    },
                    //dataType: "json",
                    success: function(resData){
                        
                        var str = resData.str;
                        //console.log(str);
                        
                        if(_isOn) {
                            $favOn.removeClass('d-inline').fadeOut(100, function(){
                            	$('.loader').fadeOut(10);
                                $favOff.removeClass('d-none').fadeIn(50); 
                            });
                        } 
                        else {
                            $favOff.removeClass('d-inline').fadeOut(100, function(){
                            	$('.loader').fadeOut(50);
                                $favOn.removeClass('d-none').fadeIn(50);
                            });
                        }
                        
                        $th.siblings('small').text(str);
                        
                        //exit();
                        
//                        $select2.empty().append(
//                            $('<option>'+ '選択して下さい' + '</option>').attr({
//                                  disabled: 1,
//                                  selected: 1,
//                            })
//                        );
//                        
//                        $.each(selectArr, function(index, val){
//                            console.log(Object.keys(val));
//                            
//                            $select2.append(
//                                $('<option>' + Object.values(val) + '</option>').attr({
//                                  value: Object.keys(val),
//                                  
//                                })
//                            );
//                        }); //each
                        
                        //$frame.html(resData).slideDown(100);
                    },
                    error: function(xhr, ts, err){
                        //resp(['']);
                    }
                });
            
            });
        },
        
        
        
    } //return

})();


$(function(e){ //ready
    
    //exe.autoComplete();
    
    exe.scrollFunc();
    
    if(exe.isSpTab('sp')) {
    	exe.toggleSp();
    }
    else {
    	exe.searchSlide();
    }
  
    exe.dropDown();
    exe.eventItem();
    
    
    exe.outReceive();
    exe.addFavorite();
  
  	exe.postNumSet();
});



})(jQuery);
