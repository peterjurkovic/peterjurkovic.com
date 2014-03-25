$(function() {
	animateSkills();
	renderProjects();
	$('h2').append('<span class="circle"></span>');

	var $skills = $('#skill-wrapp div');
	
	setTimeout(function(){
		swapColors($skills);
	}, 500);

	$skills.hover(showSkillTip, hideSkillTip) ;


	//$wrapp.on("div", "mouseenter", showSkillTip);
	//$wrapp.on("div", "mouseleave", hideSkillTip);
});

function swapColors($skills){
	$skills.each(function(){
		var $this = $(this);
		$this.data('color-bg', $this.css("background-color"));
	});
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
			 .css('background-image', 'url(../data/' + $this.attr("data-image") + '.png)')
			 .transition({ opacity: 0 })
			 .transition({ opacity: 1 });
	});
}