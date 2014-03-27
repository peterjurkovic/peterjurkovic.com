<?php 
function crop($str, $len) {
    if ( strlen($str) <= $len ) {
        return $str;
    }

    // find the longest possible match
    $pos = 0;
    foreach ( array('. ', '? ', '! ') as $punct ) {
        $npos = strpos($str, $punct);
        if ( $npos > $pos && $npos < $len && $npos > ($len + 50)) {
            $pos = $npos;
        }
    }

    if ( $pos == 0 ) {
        // substr $len-3, because the ellipsis adds 3 chars
        return substr($str, 0, $len-3) . '...'; 
    }
    else {
        // $pos+1 to grab punctuation mark
        return substr($str, 0, $pos+1);
    }
}
// ----------------------------------------------------------
function SEOlink($string){
		return friendlyURL($string);
}

function friendlyURL($nadpis) {
    $url = $nadpis;
    $url = preg_replace('~[^\\pL0-9_]+~u', '-', $url);
    $url = trim($url, "-");
    $url = iconv("utf-8", "us-ascii//TRANSLIT", $url);
    $url = strtolower($url);
    $url = preg_replace('~[^-a-z0-9_]+~', '', $url);
    return $url;
}

// ----------------------------------------------------------


function getOptions( $conn, $table, $colum, $first = 0, $skip = NULL){
	$html = "";
	$array =  $conn->select("SELECT `id_$table`, `$colum` FROM `$table`");	
	$c = count($array); 
	
	for($j=0; $j < $c;$j++){
		if($array[$j]["id_${table}"] == $first){
			$html .= "<option value=\"".$array[$j]["id_${table}"]."\">".$array[$j]["$colum"]."</option>\n";
			break;
		}
	}	
	
	
	for($j=0; $j < $c;$j++) {   
		if($array[$j]["id_${table}"] == $first || $array[$j]["id_${table}"] == $skip){ continue; }
			 $html .= "<option value=\"".$array[$j]["id_${table}"]."\">".$array[$j]["$colum"]."</option>\n";
	}   
	return $html;
}

// ----------------------------------------------------------
// GALLERY
function printImages($array, $id, $baseDir){
		$html = "";
		foreach($array as $name){
			$url = 		 $baseDir.'/'.$id.'/';
			$html .= 	'<div class="ibox"><a href="/i/900-900-auto/'.$url.$name.'" title="Zobraziť obrázok" class="show hidden"></a>'.
						'<a href="#aid'.$id.'" title="'.$url.$name.'" class="del hidden"></a>'. 
						'<img class="img" src="/i/100-100-crop/'.$url.$name.'" /></div>';
		}
		return $html;
}
	

function gallery($config, $id, $baseDir = "gallery"){
		$html = "";
		$file 	= new File();
		$array 	= $file->scanFolder($config["root_dir"]."/../data/".$baseDir."/".$id."/");
		$count 	= count($array);
		if(!isset( $_GET['gs'])) {  $_GET['gs'] = 1; }
		if($_GET['gs'] == 1) { $totaloffset = 0; } else { $totaloffset = ($_GET['gs'] * $config["galleryPagi"]) - $config["galleryPagi"]; } 
	
		if($count != 0){
				
				$html .= '<p>V galérii sa nachádza: <strong>'.$count.'</strong> fotografií.</p>';

				if($count > $config["galleryPagi"]) {
					 $nav = new Navigator($count, $_GET['gs'] , './index.php?'.preg_replace("/&gs=[0-9]/", "", $_SERVER['QUERY_STRING']) , $config["galleryPagi"]);
						$nav->setSeparator("&amp;gs=");
						$html .= $nav->simpleNumNavigator();
				}
				$array = array_slice($array, $totaloffset, $config["galleryPagi"]);
				$html .= printImages($array, $id, $baseDir).'<div class="clear"></div>';
				
		}else{
				$html .= '<span class="alert">Galéria neobsahuje žiadne obrázky.</span>';
		} 
		return $html;
	}

function checkUserRights($conn, $idCurrent, $newRigths , $idUser = null){
		if($idUser != null && $idCurrent != $idUser){
			$data = $conn->select('SELECT `id_user_type` FROM `user` where `id_user`=? OR `id_user`=? ORDER by `id_user` ASC LIMIT 2', array($idCurrent, $idUser ));
			if($idCurrent <= $idUser){
				$r_curr = $data[0]["id_user_type"];
				$r_user = $data[1]["id_user_type"];
			}else{
				$r_curr = $data[1]["id_user_type"];
				$r_user = $data[0]["id_user_type"];
			}
		return ($r_curr > 2 && $r_curr >= $r_user && $r_curr >= $newRigths );
		}else{
			$data = $conn->select('SELECT `id_user_type` FROM `user` where `id_user`=? LIMIT 1', array($idCurrent));
			return $data[0]['id_user_type'] >=  $newRigths && $data[0]['id_user_type'] > 2;
		}
}
// ----------------------------------------------------------			

function checkUserEmail($conn, $email, $id = null){
		if($id != null){
			$c = count($data = $conn->select("SELECT `id_user` FROM `user` WHERE `email`=? AND `id_user`!=? LIMIT 1", array($email, $id)));
		}else{
			$c = count($conn->select("SELECT `id_user` FROM `user` WHERE `email`=? LIMIT 1", array($email)));
		}
	return ($c == 1 ? true : false );
}
// ----------------------------------------------------------			

function createSalt($size = 5)
{
    $string = md5(uniqid(rand(), true));
    return substr($string, 0, $size);
}

// ----------------------------------------------------------	

function loginExists($conn, $login){
	return (count($conn->select("SELECT `id_user` FROM `user` WHERE `login`=?", array($login))) != 0 ? true : false );
}

// ----------------------------------------------------------

function getUserById($conn, $id, $colum = null){
	if($colum != null){
		$data = $conn->select("SELECT `".$colum."` FROM `user` WHERE `id_user`=? LIMIT 1", array( $id ));
	}else{
		$data = $conn->select("SELECT * FROM `user` WHERE `id_user`=? LIMIT 1", array( $id ));
	}
	return $data[0];
}

// ----------------------------------------------------------

function getConfig($conn, $table = "`config`", $type = "basic"){
	$sql = "SELECT `key`, `val` FROM ".$table;
	
	switch($type){
		case "basic":
			$sql .= " WHERE `key` LIKE 'c_%'";
		break; 
		case "shop":
			$sql .= " WHERE `key` LIKE 's_%'";
		break;
		case "full":
		default:	
	}
	$array = $conn->select($sql);
	$result = array();
	for($i = 0; $i < count($array); $i++){
			$result[ $array[$i]["key"]] = htmlspecialchars($array[$i]["val"]) ; 
	}
	return $result;
}

// ----------------------------------------------------------
function isInt($int){
        if(is_numeric($int) === TRUE){
            if((int)$int == $int){
                return TRUE;
            }
		}
		return FALSE; 
    }
	

// ----------------------------------------------------------
function clean($item){	
	return htmlspecialchars($item, ENT_QUOTES);
}	

// ----------------------------------------------------------
function cleanArticle($array){
	if(!is_array($array)) return;
	foreach($array as $key => $val){
		if(strpos($key, "content") !== false){
			continue;
		}else{
			$array[$key] = htmlspecialchars($val, ENT_QUOTES);
		}
	}
	return $array;
}


// ----------------------------------------------------------
function isUnique($conn, $table, $colum, $val, $id = NULL){
	if($id == NULL || $id == 0){
		$r =  $conn->select("SELECT count(*) FROM `".$table."` WHERE `".$colum."`=? LIMIT 1", array( $val ));
	}else{
		$r =  $conn->select("SELECT count(*) FROM `".$table."` WHERE `".$colum."`=? AND `id_".$table."`<>? LIMIT 1", array( $val, (int)$id ));
	}
	return ($r[0]["count(*)"] == 0 ? true : false );
}

// ----------------------------------------------------------
function isUsed($table, $colum, $val){
	global $conn;
	$r =  $conn->select("SELECT `id_".$table."` FROM `".$table."` WHERE `".$colum."`=? LIMIT 1", array( $val ));
	return (count( $r ) == 1 ? true : false );
}


// ----------------------------------------------------------------------

function isFloat($n, $len = 2){
	return (preg_match ("/^[+]?(([0-9]+)|([0-9]+[\.,]{1}[0-9]{0,$len}))$/" ,$n) == 1) && $n >= 0;
}

// ----------------------------------------------------------------------

function isDate($d){
	return (preg_match ("/^\d{1,2}\.\d{1,2}\.\d{4}$/" ,$d) == 1);

}

function parseToFloat($n){
	return floatval(str_replace(",",".",$n));
}

function curPageURL() {
	 $pageURL = "http://";
	 if ($_SERVER["SERVER_PORT"] != "80") {
	  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	 } else {
	  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	 }
	 return $pageURL;
	}

?>