$(function() {
	animateSkills();
	renderProjects();
	$('h2').append('<span class="circle"></span>');

	var $skills = $('#skill-wrapp div');
	
	setTimeout(function(){
		swapColors($skills);
	}, 500);

	$skills.hover(showSkillTip, hideSkillTip) ;


	$(document).on("click", ".pj-email", appendEmailAddress );
	$(document).on("mouseleave", ".pj-project", hideProjectDescr );
	$(document).on("mouseenter", ".pj-project", showProjectDescr );
	
});

function isOnLeftSide($this){
	var pos = $this.position(),
		windowWidth = $(document).outerWidth();
	if(windowWidth / 2 > pos.left){
		return true;
	}
	return false;
}

function showProjectDescr(e){
	e.preventDefault();
	var $this = $(this);
	$this.addClass( !isOnLeftSide($this) ? 'pj-leftside' : 'pj-rightside');
	$this.find('.pj-project-tech div').each(function(i){
		$(this)
		.transition({ x: 10 + i,  delay: 0 })
		.transition({ x: 0,  delay: 10  * i});
	})
	
	$this.find('.pj-project-hover,.pj-project-tech').show();
	$('.pj-project').not($this).addClass('pj-transparent');
	return false;
}

function hideProjectDescr(){
	var $this = $(this);
	$('.pj-project').not($this).removeClass('pj-transparent');
	$this.find('.pj-project-hover, .pj-project-tech').hide();
}

function swapColors($skills){
	$skills.each(function(){
		var $this = $(this);
		$this.data('color-bg', $this.css("background-color"));
	});
}

function appendEmailAddress(){
	$(this).attr("href", "mailto:email@peterjurkovic.sk");
}

function showSkillTip(){
	var $this = $(this),
		 pos = $this.position(),
		 w = $this.width(),
		 h = $this.height(),
		 text = $this.attr('data-skill');
	$('<div id="pj-tooltip"></div>').text(text)
	        .appendTo('#skill-wrapp')
	        .css('min-width', w + 'px')
	        .css('width', getWidth() )
	        .css('top', getTop() + 'px')
	        .css('left' , getLeft() )
	        .css("color", getColor() )
	        .css("background-color", $this.data("color-bg"))
	        ;
	return false;

	function positionBottom(){
		return $this.hasClass('pj-bottom');
	}

	function getColor(){
		return $this.hasClass('pj-white') ? '#fff' : $this.css("background-color");
	}

	function getTop(){
		if(positionBottom()){
			return pos.top + h;
		}
		if($this.hasClass('pj-jquery-mobile')){
			return (pos.top - 51);	
		}
		return (pos.top - 30);
	}

	function getLeft(){
		if(w > 43 || text.length < 6){
			return pos.left + 'px';
		}
		return ( pos.left - (w / 2)) + 'px';
	}

	function getWidth(){
		if(w > 43 || text.length < 6){
			return  w + 'px';
		}
		return w * 2;
	}
}
function hideSkillTip(){
	$('#pj-tooltip').remove();
	return false;
}

function animateSkills(){
	var windowWidth = $(document).outerWidth(),
		$items = $('#skill-wrapp div');
		if(windowWidth >= 660){
			$items.each(function(i){
				var $this = $(this);
				$this.transition({ x: (i % 2 === 0 ? (windowWidth * -1 - 450) : windowWidth + 450), delay: i * 15 })
				  .transition({ y: 200 })
				  .transition({ x: 0 })
				  .transition({ y: 0 });	
			});
		}
}


function renderProjects(){
	$items = $('.pj-project');
	$items.each(function(){
		var $this = $(this);
		$this.removeClass("hidden")
			 .css('background-image', 'url(../data/' + $this.attr("data-image"))
			 .transition({ opacity: 0 })
			 .transition({ opacity: 1 });
	});
}


jQuery.fn.center = function () {
    this.css("position","absolute");
    this.css("top", (($(window).height() - this.outerHeight()) / 2) + $(window).scrollTop() + "px");
    this.css("left", (($(window).width() - this.outerWidth()) / 2) + $(window).scrollLeft() + "px");
    return this;
}

function showLoader(){
	$('<div id="pj-loader" class="circle"><p>Loading...</p></div>').center().appendTo('body');
	return false;
}
function hideLoader(){
	$('#pj-loader').remove();
	return false;
}