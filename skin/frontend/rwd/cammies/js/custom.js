// JavaScript Document
var $cm = jQuery.noConflict();
$cm(document).ready(function() { 
     $cm("#owl-demo").owlCarousel({     
         items:1,
		 loop:true,		 
		 autoplay:false,
		 autoplayTimeout:1000,
		 autoplayHoverPause:false, 
		 dots:true,
		 nav:false    
      }); 
	 $cm("#owl-demo2").owlCarousel({     
       	items:3,
		loop:true,
		margin:20,
		dots:false,
		nav:true, 
		responsive:{
				0:{
					items:1
				},
				300:{
					items:1
				},
								
				500:{
					items:2
				},				
				600:{
					items:2
				},
				800:{
					items:3
				},				
				1000:{
					items:3
				}
			}  
      });
	  $cm("#block-related").owlCarousel({
		items:5,
		loop:true,
		margin:10,
		dots:false,
		nav:true, 
		responsive:{
				0:{
					items:1
				},
				300:{
					items:1
				},
				400:{
					items:2
				},
								
				500:{
					items:2
				},				
				600:{
					items:2
				},
				700:{
					items:3
				},
				800:{
					items:3
				},				
				1000:{
					items:5
				}
			}  
      }); 
	  $cm("#upsell-product-table").owlCarousel({
		items:5,
		loop:true,
		margin:10,
		dots:false,
		nav:true, 
		responsive:{
				0:{
					items:1
				},
				300:{
					items:1
				},
				400:{
					items:2
				},
								
				500:{
					items:2
				},				
				600:{
					items:2
				},
				700:{
					items:3
				},
				800:{
					items:3
				},				
				1000:{
					items:5
				}
			} 
      }); 
	  
});
$cm(document).ready(function(){
	
	(function(){
		$cm.fn.extend({
		accordion: function() {
		return this.each(function() {
		
		var cul = $cm(this);
		
		if(cul.data('accordiated'))
		return false;
		
		$cm.each(cul.find('ul, li>div'), function(){
			$cm(this).data('accordiated', true);
			$cm(this).hide();
		});
		
		$cm.each(cul.find('span.head'), function(){
			$cm(this).click(function(e){
			activate(this);
			return void(0);
			});
		});
		
		var active = (location.hash)?$cm(this).find('a[href=' + location.hash + ']')[0]:"";
		
		if(active){
			activate(active, 'toggle');
			$cm(active).parents().show();
		}
		
		function activate(el,effect){
			$cm(el).parent('li').toggleClass('active').siblings().removeClass('active').children('ul, div').slideUp('fast');
			$cm(el).siblings('ul, div')[(effect || 'slideToggle')]((!effect)?'fast':null);
		}
		
		});
		}
		});
	})($cm);
	
	$cm("ul.accordion li.parent").each(function(){
		$cm(this).append('<span class="head"><a href="javascript:void(0)"></a></span>');
	});
	
	$cm('ul.accordion').accordion();
	
	$cm("ul.accordion li.active").each(function(){
		$cm(this).children().next("ul").css('display', 'block');
	});
	
});

$cm(document).ready(function() {
  $cm('#bookmark-this').click(function(e) {
    var bookmarkURL = window.location.href;
    var bookmarkTitle = document.title;

    if ('addToHomescreen' in window && window.addToHomescreen.isCompatible) {
      // Mobile browsers
      addToHomescreen({ autostart: false, startDelay: 0 }).show(true);
    } else if (window.sidebar && window.sidebar.addPanel) {
      // Firefox version < 23
      window.sidebar.addPanel(bookmarkTitle, bookmarkURL, '');
    } else if ((window.sidebar && /Firefox/i.test(navigator.userAgent)) || (window.opera && window.print)) {
      // Firefox version >= 23 and Opera Hotlist
      $cm(this).attr({
        href: bookmarkURL,
        title: bookmarkTitle,
        rel: 'sidebar'
      }).off(e);
      return true;
    } else if (window.external && ('AddFavorite' in window.external)) {
      // IE Favorite
      window.external.AddFavorite(bookmarkURL, bookmarkTitle);
    } else {
      // Other browsers (mainly WebKit - Chrome/Safari)
      alert('Press ' + (/Mac/i.test(navigator.userAgent) ? 'Cmd' : 'Ctrl') + '+D to bookmark this page.');
    }

    return false;
  });
});