$(init);


function init(){
	animateSkills();
	renderProjects(true);
	$('h2').append('<span class="circle"></span>');
	var $skills = $('#skill-wrapp div');
	setTimeout(function(){
		swapColors($skills);
	}, 500);
	$skills.hover(showSkillTip, hideSkillTip) ;
	$(document).on("click", ".pj-email", appendEmailAddress );
	$(document).on("click", ".pj-asyncload", loadOther );
	$(document).on("click", "form a", sendEmail );
	$(document).on("click", ".pj-project .pj-project-tech, .pj-project-hover", showProjectDetail );
	$(document).on("mouseleave", ".pj-project", hideProjectDescr );
	$(document).on("mouseenter", ".pj-project", showProjectDescr );
	$(document).on("mouseleave", "#gallery a", imageLeave );
	$(document).on("mouseenter", "#gallery a", imageEnter );
	$('<div class="remodal" data-remodal-id="modal"><article></article></div>').appendTo('body');
	$('.remodal').remodal();
	initProjectDetail();
}


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
	var $this = $(this),
		isLeftSide = isOnLeftSide($this);
	$this.addClass( !isLeftSide ? 'pj-leftside' : 'pj-rightside');
	$this.removeClass( isLeftSide ? 'pj-leftside' : 'pj-rightside');	
	$this.find('.pj-project-tech div').each(function(i){
		var $skill = $(this);
		$skill.transition({ x: ((i + 1) * (isLeftSide ? 1 : -1) ), delay : i * 10 })
			  .transition({ x: 0 , delay : i * 10 });
	});
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


function renderProjects(timeout){
	var $wrapp = $('#pj-project-wrapp'),
		$items = $wrapp.find('.pj-project.hidden');
	if(timeout){
		$wrapp.fadeOut();
		setTimeout(function() {$items.removeClass("hidden");$wrapp.fadeIn(1000)}, 1000);
	}else{
		$items.removeClass("hidden").fadeOut().fadeIn(1500);
	}
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


function loadOther(){
	showLoader();
	var data = {
		lang : $(this).attr("data-lang"),
		act : 1
	},
	$this = $(this);
	executeRequest(data, function(json) {
		json = $.parseJSON(json);
		if(json.err == 0){
			$('.pj-projects').append(json.html);
			renderProjects(false);
			$this.remove();
		}
	});
	return false;
}


function sendEmail(){
	var $form = $('form'),
		data = renameArr( $form.serializeArray() );
	if(!isValid($form)){
		return false;
	}
	data.act = 2;
	data.lang = $('body').attr("data-lang");
	executeRequest(data, function(json) {
		json = $.parseJSON(json);
		if(json.err == 0){
			$('<p id="pj-success"></p>')
			.text(json.msg)
			.appendTo('form');
			$form.find('input, textarea').val('');
			setTimeout(function(){
				$('#pj-success').remove();
			}, 4000);

		}
	});
	return false;
}

function showProjectDetail(){
	var id = null;
	if(arguments.length === 1){
		id = parseInt( arguments[0], 10);
	}
	showLoader();
	var $this = $(this),
		data = {
			id : (isNaN(id) ?  $this.parent().attr('data-id') : id ),
			lang : $('body').attr("data-lang"),
			act : 3,
	};
	setTimeout(function(){
		executeRequest(data, function(json) {
			json = $.parseJSON(json);
			if(json.err == 0){
			 	$('.remodal').find('article').html(json.html);
			 	if(!isHashSet()){
			 		location.hash = 'project-' + data.id;
			 	}
			 	getModalBoxInstance().open();
			}
		});
	}, 500);
	return false;
}


function executeRequest(data, callBack){
	$.ajax({
			type: 'GET',
			url : '/inc/ajax.php',
			data : data,
			contentType: "application/json"
	})
	.done( callBack )
	.fail(function(xhr, statusText, e) {
    	console.warn('Some error occured.');
    	console.warn(statusText);
    	console.warn(e);
	})
	.complete(function(e) {
		setTimeout(hideLoader, 400);
	});
}


function getModalBoxInstance(){
	return $.remodal.lookup[$('[data-remodal-id=modal]').data('remodal')];
}

function isValid(f){
	var inputs = f.find('input, textarea'),
	valid = true,
	errorClass = 'pj-form-error',
	vldt = {
		required : function(v,i) {return {r : !!v ,  msg : 'All fields are required'};},
		email	 : function(v,i) {return {r : v.match( /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/ ), msg : 'Invalid email address'};},
		fiveplus : function(v,i) {return {r : v.length >= 5, msg : 'At least 5 characters'} ;}
	};
	f.find('#pj-errors').remove();
	inputs.removeClass(errorClass);
	inputs.each(function(){
		var input = $(this),
			val = input.val(),
			cls = input.attr("class").split(' ');

		for(i in cls){
			if(vldt.hasOwnProperty(cls[i])){
				var res = vldt[cls[i]](val,input);
				if(!res.r){
					if(f.find('#pj-errors').length === 0){
						$('<p id="pj-errors"></p>')
						.text(res.msg)
						.appendTo(f);
					}
					input.addClass(errorClass);
					// showStatus({err : 1, msg : res.msg});
					valid = false;
				}
			}
		}
	});
	return valid;	
}

function renameArr(a){
	var d = {};	
	for (i in a) {
		d[a[i].name] = a[i].value;
	}
	return d;
}

function initProjectDetail(){
	if(isHashSet()){
		 var data = location.hash.replace("#", "").split("-");
         showProjectDetail(data[1]);
	}
}

function isHashSet(){
    return /^#[a-z]{1,10}\-\d{1,3}$/.test( location.hash );
}

function imageEnter() {
	$('#gallery').find('a').not($(this)).addClass('pj-transparent');
	return false;
}

function imageLeave() {
	$('#gallery a').removeClass('pj-transparent');
	return false;
}