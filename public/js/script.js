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
                        $input.show().animate({width:'22em', opacity:1}, 300, 'linear', function(){
                    
                        });
                    }
                    else {
                        $input.animate({width:0, opacity:0}, 300, 'linear', function(){
                    
                        }).fadeOut(600);
                    
                    }
                });
            }
        },
        
        toggleSp: function() {
           
            //SP Only
            $('.site-header .nav-tgl').on('click', function(){
            	
                var $nav = $('.main-navigation');
                var $sForm = $('.s-form > div');
                var t = $(window).scrollTop();
                
                if($nav.is(':visible')) {
                	var top = $nav.data('top');
                    $('html,body').css({position:'static'}).scrollTop(top);
                }
                else {
                    $('html,body').css({position:'fixed', top:-t});
                    $nav.data('top', t);
                }

				if($sForm.is(':visible')) {
                	$sForm.slideUp(200, function(){
                        $nav.slideToggle(300, 'linear', function(){
                            $('.menu-dropdown').hide();
                        });
                        
                        $(this).queue([]).stop();
                    });
                }
                else {
                    $nav.slideToggle(300, 'linear', function(){
                        $('.menu-dropdown').hide();
                    });
                }
                
            });
        },
        
        
        dropDown: function() {
        	var $stateNav = $('.state-nav li');
        	//var len = $('.state-nav li').length;
            var num = 0;
           
            var speed = 200;
           	var easing = 'linear';
           
           	var hideSpeed = this.isSpTab('sp') ? 150 : 0;
            //console.log(len);
           
           	//$('.menu-dropdown').eq(1).slideToggle(200);
           
            $stateNav.on('click', function(e){
				
                var $clickThis = $(this);
                var n = $(this).index();
                
                $(e.target).addClass('nav-active');
                
                if($('.menu-dropdown').eq(n).is(':visible')) {
                	
                    $stateNav.removeClass('nav-active');
                    
                	$('.menu-dropdown').eq(n).slideUp(speed, easing, function() {
                        $(this).queue([]).stop();
                    });
                }
                else {
                
                    $('.menu-dropdown').slideUp(hideSpeed, function(){
                        $stateNav.removeClass('nav-active');
                        $clickThis.addClass('nav-active');
                        
                        $('.menu-dropdown').eq(n).slideDown(speed, easing, function() {
                            $(this).queue([]).stop();
                        });
                    
                    });
                }
                
                
            });
           
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
        
        
        
    } //return

})();


$(function(e){ //ready
    
    //exe.autoComplete();
    
    exe.scrollFunc();
    exe.toggleSp();
  
    exe.dropDown();
    exe.eventItem();
    exe.searchSlide();
  
  	//exe.addClass();
});



})(jQuery);
