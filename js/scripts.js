$(init);


function init(){
	animateSkills();
	renderProjects(true);
	$('h2').append('<span class="circle"></span>');
	var $skills = $('#skill-wrapp div');
	setTimeout(function(){
		swapColors($skills);
	}, 500);
	
	if(!$.browser.mobile){
		$skills.hover(showSkillTip, hideSkillTip) ;
		$(document).on("mouseleave", ".pj-project", hideProjectDescr );
		$(document).on("mouseenter", ".pj-project", showProjectDescr );
		$(document).on("mouseleave", "#gallery a", imageLeave );
		$(document).on("mouseenter", "#gallery a", imageEnter );
	}


	$skills.on("click", onSkillClicked );
	$(document).on("click", ".pj-email", appendEmailAddress );
	$(document).on("click", ".pj-asyncload", loadOther );
	$(document).on("click", "form a", sendEmail );
	$(document).on("click", ".pj-project", showProjectDetail );
	$(document).on("refreshfilter", refreshSkillFilter);
	$(document).on('click', '#pj-selected-skills div', onRemoveSkillClicked);

	$('<div class="remodal" data-remodal-id="modal"><article></article></div>').appendTo('body');
	$('.remodal').remodal();
	initProjectDetail();
}

function onSkillClicked(){
	var $this = $(this);
	$this.toggleClass('pj-selected');
	$(document).trigger("refreshfilter");
	return false;
}

function onRemoveSkillClicked(){
	var $this = $(this),
	    id = getId($this);
		$('#skill-wrapp').find('[data-id='+id+']').removeClass('pj-selected');
	$(document).trigger("refreshfilter");
	return false;
}
function refreshSkillFilter(){
	showLoader();
	var data = {
			items : getSelectedSkilss(),
			lang : $('body').attr("data-lang"),
			act : 4
	};
	executeRequest( data , function(json) {
		json = $.parseJSON(json);
		if(json.err == 0){
			$('#pj-project-wrapp').html(json.html);
			renderProjects(false);
			var $loadOtherBtn = $('.pj-asyncload');
			if(data.items.length === 0){
				$loadOtherBtn.removeClass('hidden');
			}else{
				$loadOtherBtn.addClass('hidden');
			}
		}
	});
	return false;
}

function getSelectedSkilss(){
	var selected = [];
	$('#skill-wrapp div.pj-selected').each(function(){
		selected.push( getId($(this)) );
	});
	return selected;
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
	if(isWindowWithSmall()){
		return false;
	}
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
	if(isWindowWithSmall()){
		return false;
	}
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
		$items.removeClass("hidden").fadeOut().fadeIn(1000);
	}
}


jQuery.fn.center = function () {
    this.css("position","absolute");
    this.css("top", (($(window).height() - this.outerHeight()- ($.browser.mobile ? 100 : 0 )) / 2) + $(window).scrollTop() + "px");
    this.css("left", (($(window).width() - this.outerWidth() - ($.browser.mobile ? 100 : 0 ))  / 2) + $(window).scrollLeft() + "px");
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
			$this.addClass('hidden');
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
	if(arguments.length === 1 && !isNaN(arguments[0])){
		id = parseInt( arguments[0], 10);
	}else{
		id = getId( $(this) );
	}
	showLoader();
	setTimeout(function(){
		executeRequest({
			id : id,
			lang : $('body').attr("data-lang"),
			act : 3
		}, function(json) {
			json = $.parseJSON(json);
			if(json.err == 0){
			 	$('.remodal').find('article').html(json.html);
			 	if(!isHashSet()){
			 		location.hash = 'project-' + id;
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

function isWindowWithSmall(){
	return $(document).outerWidth() < 480;
}

function isMobileDevice(){
	
}

function getId(e){
	return e.attr('data-id');
}




(function( jQuery, window, undefined ) {
"use strict";

var matched, browser;

jQuery.uaMatch = function( ua ) {
  ua = ua.toLowerCase();

	var match = /(opr)[\/]([\w.]+)/.exec( ua ) ||
		/(chrome)[ \/]([\w.]+)/.exec( ua ) ||
		/(version)[ \/]([\w.]+).*(safari)[ \/]([\w.]+)/.exec(ua) ||
		/(webkit)[ \/]([\w.]+)/.exec( ua ) ||
		/(opera)(?:.*version|)[ \/]([\w.]+)/.exec( ua ) ||
		/(msie) ([\w.]+)/.exec( ua ) ||
		ua.indexOf("trident") >= 0 && /(rv)(?::| )([\w.]+)/.exec( ua ) ||
		ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec( ua ) ||
		[];

	var platform_match = /(ipad)/.exec( ua ) ||
		/(iphone)/.exec( ua ) ||
		/(android)/.exec( ua ) ||
		/(windows phone)/.exec(ua) ||
		/(win)/.exec( ua ) ||
		/(mac)/.exec( ua ) ||
		/(linux)/.exec( ua ) ||
		[];

	return {
		browser: match[ 3 ] || match[ 1 ] || "",
		version: match[ 2 ] || "0",
		platform: platform_match[0] || ""
	};
};

matched = jQuery.uaMatch( window.navigator.userAgent );
browser = {};

if ( matched.browser ) {
	browser[ matched.browser ] = true;
	browser.version = matched.version;
	browser.versionNumber = parseInt(matched.version);
}

if ( matched.platform ) {
	browser[ matched.platform ] = true;
}

// These are all considered mobile platforms, meaning they run a mobile browser
if ( browser.android || browser.ipad || browser.iphone || browser[ "windows phone" ] ) {
	browser.mobile = true;
}

// These are all considered desktop platforms, meaning they run a desktop browser
if ( browser.mac || browser.linux || browser.win ) {
	browser.desktop = true;
}

// Chrome, Opera 15+ and Safari are webkit based browsers
if ( browser.chrome || browser.opr || browser.safari ) {
	browser.webkit = true;
}

// IE11 has a new token so we will assign it msie to avoid breaking changes
if ( browser.rv )
{
	var ie = 'msie';

	matched.browser = ie;
	browser[ie] = true;
}

// Opera 15+ are identified as opr
if ( browser.opr )
{
	var opera = 'opera';

	matched.browser = opera;
	browser[opera] = true;
}

// Stock Android browsers are marked as safari on Android.
if ( browser.safari && browser.android )
{
	var android = 'android';

	matched.browser = android;
	browser[android] = true;
}

// Assign the name and platform variable
browser.name = matched.browser;
browser.platform = matched.platform;


jQuery.browser = browser;

})( jQuery, window );