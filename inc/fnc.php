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
			$html .= '<div class="'.getSkillCssClasses($skills[$i]).
					 ' " data-skill="'.$skills[$i]["name"].'"></div>'."\n";		
		}	
		return $html;	
	}

	function printProjects(){
		global $conn, $lang;
		$projects = getArticle("categ", 34, $lang);
		$html = '';
		for($i = 0; 8 > $i; $i++){
			$html .= getProject($projects[$i]);
		}
		echo $html;
	}

	function getProjects($offset){
		global $conn, $lang;
		$projects = getArticle("categ", 34, $lang);
		$html = '';
		for($i = $offset; count($projects) > $i; $i++){
			$html .= getProject($projects[$i]);
		}
		return $html;
	}


	function getProject($article){
		return '<div class="pj-project hidden" data-image="'.$article["avatar1"].'">'.
				'<div class="pj-project-hover"></div>'.
				getSkilss($article['id_article']).
				'</div>';
	}



	function getSkilss($idArticle){
		global $conn;
		$skills = getSkillsByArticleId($conn, $idArticle);
		if(count($skills) == 0){
			return '';
		}
		$html = '<div class="pj-project-tech">';
		for($i = 0; count($skills) > $i; $i++){
			$html .= '<div class="'.getSkillCssClasses($skills[$i]).'">'.$skills[$i]['name'].'</div>';
		}
		return $html."</div>";
	}

	function getSkillCssClasses($skill){
		return 'pj-'.$skill["code"].(isset($skill["css_class"]) ?  ' ' .$skill["css_class"] : '');
	}
	
?>