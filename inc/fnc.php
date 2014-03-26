<?php

	
	function printContent(){
		global $meta, $lang;
		$article = getArticle("fullHidden", $meta['id_article'], $lang);
		if(isset($article[0]) && isset($article[0]["content_$lang"])){
			echo $article[0]["content_$lang"];
		}
	}

	function printPageTitle($id){
		global $lang;
		$article = getArticle("basic", $id, $lang);
		if(isset($article[0]) && isset($article[0]["title_$lang"])){
			echo $article[0]["title_$lang"];
		}
	}


	function printSkills(){
		global $conn;
		$skills = getPublishedSkills($conn);
		$html = "";
	
		for($i = 0; count($skills) > $i; $i++){
			$html .= '<div class="pj-'.$skills[$i]["code"].(isset($skills[$i]["css_class"]) ?  ' ' .$skills[$i]["css_class"] : '').
					 ' " data-skill="'.$skills[$i]["name"].'"></div>'."\n";		
		}	
		return $html;	
	}
	
?>