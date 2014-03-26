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
	

?>