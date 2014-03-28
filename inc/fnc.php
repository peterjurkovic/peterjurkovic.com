<?php

	
	function printContent($id = null){
		global $meta, $lang;
		if($id == null){
			$id = $meta['id_article'];
		}
		$article = getArticle("fullHidden", $id, $lang);
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
		return getHTMLSkill( getPublishedSkills($conn) );
	}


	function getHTMLSkill($skills, $withName = false){
		$html = "";
		for($i = 0; count($skills) > $i; $i++){
			$html .= '<div class="'.getSkillCssClasses($skills[$i]).'" data-id="'.$skills[$i]["id"].
					 '" data-skill="'.$skills[$i]["name"].'">'.($withName ? $skills[$i]["name"] : '').'</div>'."\n";			
		}	
		return $html;	
	}

	function getHTMLSkillsByIDs($ids){
		global $conn;
		return getHTMLSkill( getSkillsByIds($conn, $ids), true );
	}

	function printProjects($limit = 8){
		echo getLimitedProjects($limit);
	}

	function getLimitedProjects($limit = 8){
		global $conn, $lang;
		$projects = getArticle("categ", 34, $lang);
		$html = '';
		if(count($projects) < $limit){
			$limit = count($projects);
		}
		for($i = 0; $limit > $i; $i++){
			$html .= getProject($projects[$i]);
		}
		return $html;
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
		global $lang;
		if(!isset($article['id_article'])){
			return '';
		}
		return '<div class="pj-project hidden" title="'.$article["title_$lang"].'" data-id="'.$article["id_article"].'" '.
			   'style=" background-image: url(../data/avatars/'.$article["avatar1"].')">'.
				'<a href="#project-'.$article["id_article"].'" class="pj-project-hover"></a>'.
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
		for($i = 0; count($skills) > $i && 8 > $i; $i++){
			$short =  $skills[$i]['id'] == 10;
			$html .= '<div class="'.getSkillCssClasses($skills[$i]).($short ? ' pj-padding ': '').'">'.
					  ( $short ? 'Spring' : $skills[$i]['name']) .'</div>';
		}
		return $html."</div>";
	}


	function getSkillCssClasses($skill){
		return 'pj-'.$skill["code"].(isset($skill["css_class"]) ?  ' ' .$skill["css_class"] : '');
	}



	function incrementHit($articleId){
		global $conn;
		$conn->update("UPDATE article set hits=hits+1 where id_article=? LIMIT 1", array( $articleId ));
	}
	


	function filterProjectBySkills($skillsIds){
		global $conn;
		if(empty($skillsIds)){
			return getLimitedProjects();	
		}else{
			$projects = getProjectsBySkills($conn, $skillsIds);
		}
		$html = '';
		for($i = 0; count($projects) > $i; $i++){
			$html .= getProject($projects[$i]);
		}
		return $html;
	}

?>