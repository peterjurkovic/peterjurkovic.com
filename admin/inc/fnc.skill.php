<?php


	function getAllSkills($conn, $enabledOnly = fase){
		$sql = "SELECT * FROM  `skill` s ";
		if(!$enabledOnly){
			$sql .= " WHERE s.enabled = 1";
		}
		return $conn->select($sql, array());
	}


	function getPublishedSkills($conn){
		return getAllSkills($conn, true);
	}
	

	function getSkillsByArticleId($conn, $id){
		$sql = "SELECT s.* ".
			   "FROM  `project_skill` pj ".
			   "LEFT JOIN skill s ON s.id = pj.id_skill ".
			   "WHERE id_article =?";
		return $conn->select($sql, array($id));
	}

	function getProjectsBySkills($conn, $skillIDs){
		$strWhere = join(',', $skillIDs);  
		return $conn->select(
					"SELECT * FROM article a ".
					"LEFT JOIN project_skill pj ON a.id_article = pj.id_article ".
					"WHERE pj.id_skill IN ( $strWhere ) ".
					"GROUP BY a.id_article ".
					"HAVING count(pj.id_article)=" . count($skillIDs));
	}


	function getSkillsByIds($conn, $ids){
		$strWhere = join(',', $ids);  
		return $conn->select("SELECT * FROM skill s WHERE s.id IN ( $strWhere ) ");
	}
?>