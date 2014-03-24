$(function() {
	var $items = $('#skill-wrapp div'),
	positionList = elements($items);
	
	//$items.css({ top: '-2500', left: '0' })
});


function elements($items){
	var arr = [],
		wh = $(document).outerWidth();

	$items.each(function(i){
		var $this = $(this),
			offset = $this.offset();
		console.log(offset);
		$this.transition({ x: (i % 2 === 0 ? ((wh * -1) - 450) : wh + 450) })
		  .show()
		  .transition({ y: 200 })
		  .transition({ x: 0 })
		  .transition({ y: 0 });	
		console.log($this.offset());
		arr.push($this.position());
		console.log('---');
	});
	return arr;
}