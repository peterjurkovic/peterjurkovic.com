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

?>