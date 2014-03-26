<?php 

// ----------------------------------------------------------


function MAIN(){
	global $conn, $lang, $config;
	$page = array( );	
	
	$first = $conn->select("SELECT `id_article` FROM `article` WHERE `sub_id`=0 AND `active`=1 ORDER BY `order` LIMIT 1");
	$conf = getConfig($conn, "config", "page");
	
	if(count($first) != 1 && $_GET['p'] != $config['shop_prefix']){
		return $conf;
	}
	
	$conf["fid"] = $first[0]["id_article"];
	if($_GET['p'] != $config['shop_prefix'])
	{
		$q = "SELECT `id_article`, `sub_id`, `type`, `title_${lang}`, `subtitle_${lang}`, `header_${lang}` FROM `article` ";
		if($_GET['p'] == "home" || $_GET['p'] == "search")
		{
			$page = $conn->select("$q WHERE `id_article`=? AND `active`=1 LIMIT 1", array( $conf["fid"] ));
			$page[0]['id_article'] = $first[0]["id_article"];
			$page[0]['id_parent'] = $first[0]["id_article"];
			$page[0]['sub_id'] = 0;
			$page[0]['type'] = 1; 
			$page[0]["title_${lang}"] = $conf["c_title"];
			$page[0]["header_${lang}"] = $conf["c_descr"];
		}else{
			$q = "SELECT `id_article`, `sub_id`, `type`, `title_${lang}`, `subtitle_${lang}`, `header_${lang}` FROM `article` ";
			if($_GET['a'] == "index")
			{
				$page = $conn->select("$q WHERE `link_${lang}`=? AND `active`=1 LIMIT 1", array( $_GET['p'] ));	
				if(!$page) header("Location: /error/page.php?p=404");	
				$page[0]['id_parent'] = $page[0]["id_article"];	
			}else{
				$page = $conn->select("$q WHERE `id_article`=? AND `active`=1 LIMIT 1", array( $_GET['a'] ));
				if(!$page) header("Location: /error/page.php?p=404");	
				$page[0]['id_parent'] = parentID($page[0]['sub_id']);
			}
			
		}
		
	}else{ // shop index
			$q = "SELECT `id_shop_category`, `sub_id`, `category_name`, `link_sk`, `label` FROM `shop_category` WHERE";
		if($_GET['cn'] == "home" || $_GET['cn'] == "search" || $_GET['cn'] == "kosik" || $_GET['cn'] == "kosik2" || $_GET['cn'] == "kosik3"){
			$page[0]['id_shop_category'] = 0;
			$page[0]['sub_id'] = 0;
			$page[0]['parentID'] = 0;
			$page[0]['parentSubID'] = 0;
			$page[0]["header_sk"] = $conf["s_descr"]; 
			$page[0]["title_sk"] = $conf["s_title"];
		}elseif($_GET['cn'] == "pview"){
			// PRODUCT ---
			$page = getProduct("full", $_GET['pid']);
			if(!$page) header("Location: /error/page.php?p=404");
			$p = parentMETA($page[0]["id_shop_category"]);
			$page[0]['parentID'] = $p["id_shop_category"];
			$page[0]['parentSubID'] = $p["sub_id"];
			$page[0]["header_sk"] = substr($page[0]["header_sk"], 0, 200); 
			$page[0]["title_sk"] = (isset($page[0]["shop_manufacturer_name"]) ? $page[0]["shop_manufacturer_name"]." " :  "").$page[0]["title_sk"];
		}elseif($_GET['cid'] == "index"){
			// main category ---
			$page = $conn->select("$q `link_sk`=? AND `active`=1 LIMIT 1", array( $_GET['cn'] ));
			if(!$page) header("Location: /error/page.php?p=404");
			$page[0]['parentID'] = $page[0]["id_shop_category"];
			$page[0]['parentSubID'] = $page[0]["sub_id"];
			$page[0]["header_sk"] = substr($page[0]["label"], 0, 200); 
			$page[0]["title_sk"] = $page[0]["category_name"];
		}else{
			// SUB CATEG ---
			$page = $conn->select("$q `id_shop_category`=? AND `active`=1 LIMIT 1", array( $_GET['cid'] ));
			if(!$page) header("Location: /error/page.php?p=404");
			$p = parentMETA($page[0]["sub_id"]);
			$page[0]['parentID'] = $p["id_shop_category"];
			$page[0]['parentSubID'] = $p["sub_id"];
			$page[0]["header_sk"] = substr($p["label"], 0, 200); 
			$page[0]["title_sk"] = $p["category_name"]." - ". $page[0]["category_name"];
		}
	}	
	$conf = array_map("clean", $conf);
	$page[0] = cleanArticle($page[0]);
	return  array_merge($page[0], $conf);
	
}

// ----------------------------------------------------------

function getArticle($type = "basic", $id = null, $lang = "en"){
	global $conn;
	
	switch($type){
		case "full" :
			$article = $conn->select("SELECT `id_article`, `sub_id`, `type`, `avatar1`, `avatar2`, `avatar3`, `edit`, `create`, `hits`, `title_$lang`, `subtitle_$lang`, `header_$lang`, `content_$lang`, `link_$lang`
							FROM  `article` WHERE `id_article`=? AND `active`=1 LIMIT 1", array( $id ));
			$article[0] = cleanArticle($article[0]);
			break;
		case "fullHidden" :
			$article = $conn->select("SELECT `id_article`, `sub_id`, `type`, `avatar1`, `avatar2`, `avatar3`, `edit`, `create`, `hits`, `title_$lang`, `subtitle_$lang`, `header_$lang`, `content_$lang`, `link_$lang`
							FROM  `article` WHERE `id_article`=? LIMIT 1", array( $id ));
			$article[0] = cleanArticle($article[0]);
			break;
		case "set" :
			$article = $conn->select("SELECT `id_article`, `sub_id`, `type`, `avatar1`, `avatar2`, `avatar3`, `edit`, `create`, `hits`, `title_$lang`, `subtitle_$lang`, `header_$lang`, `content_$lang`, `link_$lang`
							FROM  `article` WHERE `id_article`=".implode(" OR `id_article`=", $id)." AND `active`=1 LIMIT ".count($id));
		break;
	
		case "basic" :
			$article = $conn->select("SELECT `id_article`, `type`, `avatar1`, `title_$lang`, `header_$lang`, `link_$lang` FROM  `article` WHERE `id_article`=? AND `active`=1 LIMIT 1", array( $id ));
			$article[0] = cleanArticle($article[0]);
			break;
		
		case "categ" :
			$article = $conn->select("SELECT `id_article`, `sub_id`, `type`, `title_$lang`,`subtitle_$lang`,`header_$lang`, `content_$lang`, `avatar1`, `link_$lang` FROM  `article` WHERE `sub_id`=? AND `active`=1 ORDER BY `order`", array( $id ));
			break;
			
		case "link" :
			$article = $conn->select("SELECT `id_article`, `sub_id`, `type`, `title_$lang`, `link_$lang` FROM  `article` WHERE `id_article`=? LIMIT 1", array( $id ));
			$article[0] = cleanArticle($article[0]);
			break;	
	}
	
	return $article;
}

// ----------------------------------------------------------

	function printMenu($subID, $class = "", $showSub = true){
		global $conn, $lang, $meta;
		$html = "";
		$categ = getArticle("categ", $subID, $lang);
		for($i =0; $i < count($categ); $i++ ){
			
			$url = linker($categ[$i]["id_article"], $categ[$i]["type"], $lang);
			
			if(!$url) continue; 
			
			$html .= '<li class="'.$class.($categ[$i]["id_article"] == $meta["id_parent"] || $categ[$i]["id_article"] == $meta["sub_id"] || $categ[$i]["id_article"] == $meta["id_article"] ? " curr" : "").
					'"><a href="'.$url.'">'.$categ[$i]["title_${lang}"].'</a>';
			
			if($showSub && $conn->simpleQuery("SELECT `id_article` FROM `article` WHERE `active`=1 AND `sub_id`=".$categ[$i]["id_article"]." LIMIT 1")){
				$html .= '<ul>'.printMenu($categ[$i]["id_article"], "").'</ul>';
			
			}
			$html .= '</li>';		
		}
		return $html;
	}

// ----------------------------------------------------------

function getReviews(){
    global $conn;
    $html = '';
    $data = $conn->select("SELECT * from review ORDER BY RAND() LIMIT 1");
    for($i =0; $i < count($data); $i++ )
        $html .= '<li><strong>'.$data[$i]["text"].'</strong><em>'.$data[$i]["name"].', '.$data[$i]["device"].', '.date("d.m.Y", strtotime ($data[$i]["date"])).'</em></li>';
    return $html;
}

function linker($aid, $type, $lang = "sk"){
	global  $meta, $conn;
	
	if($meta['fid'] == $aid && $lang == "sk"){ return "/"; }
	
	if($type == 2){
		$ids = $conn->select("SELECT `id_article` FROM `article` WHERE `sub_id`=? AND `active`=1 LIMIT 1" , array( $aid ));
		if(count($ids) !=0){
			$aid = $ids[0]["id_article"];
		}
	}
	
	$article =  getArticle("link", $aid, $lang);
	
	if($article[0]["link_${lang}"] == "") return false;
	
	if($article[0]["sub_id"] == 0){
	   return  "/".$lang."/".$article[0]["link_${lang}"]; 
	}else{
	   return   "/".$lang."/".parentPage($article[0]["sub_id"], $lang)."/".$article[0]["id_article"]."/".$article[0]["link_${lang}"]; 
	}
	
}
// ----------------------------------------------------------


function parentPage($sub_id, $lang){
	global $conn;
	$article = getArticle("link", $sub_id, $lang); 
	if($article[0]["sub_id"] == 0){
		return ($article[0]["link_${lang}"]);
	}else{
		return(parentPage($article[0]['sub_id'], $lang)); 
	}
}

function isChildren($parentId, $childrenId){
	global $conn;
        if($parentId == $childrenId) return true;
	$article = getArticle("link", $childrenId); 
	if($article[0]["sub_id"] == $parentId){
		return true;
	}else{
            if($article[0]["sub_id"] == 0) return false;
            return(isChildren($parentId, $article[0]['sub_id'])); 
	}
}


function parentID($subID){
	global $conn, $lang;
	$article = getArticle( "link", $subID, $lang);
	if($article[0]["sub_id"] == 0){
		return $article[0]['id_article'];
	}else{
		return(parentID($article[0]['sub_id'])); 
	}
}

function parentMETA($subID){
	global $conn;
	$cat = getCateg($type = "link", $subID);
	if($cat[0]["sub_id"] == 0){
		return $cat[0];
	}else{
		return(parentMETA($cat[0]['sub_id'])); 
	}
}

function isPositiveInt($n, $min = 0, $max = 2){
	return (preg_match ("/^[0-9]{".$min.",".$max."}$/" ,$n) == 1);
}

function isEmail($email){
	return (preg_match ("/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,3})$/i" ,$email) == 1);
}


function pritGallery($idArticle, $width, $height, $altTitle = ""){
    $gallery = "";
    if(is_dir(dirname(__FILE__)."/../../data/gallery/".$idArticle."/")){
        $file 	= new File();			 
        $files 	= $file->scanFolder(dirname(__FILE__)."/../../data/gallery/".$idArticle."/");
        $count 	= count($files);
        if($count != 0){
            foreach($files as $fileName){
                $gallery .=  '<a href="/i/700-700-auto/gallery/'.$idArticle.'/'.$fileName.'" rel="lightbox">'.
                             '<img src="/i/'.$width.'-'.$height.'-crop/gallery/'.$idArticle.'/'.
                             $fileName.'" alt="'.$altTitle.'"/></a>';
            }
            $gallery = '<div id="gallery">'.$gallery.'<div class="clear"></div></div>';
        }
     return $gallery;
    }
}
function printSlidesImages($idArticle, $width, $height, $altTitle = ""){
    $gallery = "";
    if(is_dir(dirname(__FILE__)."/../../data/gallery/".$idArticle."/")){
        $file 	= new File();			 
        $files 	= $file->scanFolder(dirname(__FILE__)."/../../data/gallery/".$idArticle."/");
        $count 	= count($files);
        if($count != 0){
            foreach($files as $fileName){
                $gallery .=  '<li><img src="/i/'.$width.'-'.$height.'-crop/gallery/'.$idArticle.'/'.
                             $fileName.'" alt="'.$altTitle.'" /></li>';
            }  
        }
     return $gallery;
    }
}

function printBoxes($categoryId, $limit = 3){
    $html = "";
    $data = getArticle("categ", $categoryId);
    for($i = 0; $i < count($data) && $i < $limit; $i++ ){
        $html .=  '<div class="box"><strong>'.$data[$i]['title_sk'].'</strong>'.
                    printAvatar($data[$i]['avatar1']).
                  '<p>'.parseContact($data[$i]['header_sk']).printLink($data[$i]['id_article']).'</p></div>';

    }
    return $html;
}


function printAvatar($avatar, $alt, $w, $h, $type){
    if(strlen($avatar) > 4)
        return '<img src="/i/'.$w.'-'.$h.'-'.$type.'/avatars/'.$avatar.'" alt="'.$alt.'" />';
    return '<img src="/img/noavatar.png" alt="'.$alt.'" />';
}

function printAvatarWithLink($avatar, $alt, $w, $h, $type){
    if(strlen($avatar) > 4)
        return '<a rel="lightbox" href="/i/500-500-auto/avatars/'.$avatar.'">'.
            
               '<img src="/i/'.$w.'-'.$h.'-'.$type.'/avatars/'.$avatar.'" alt="'.$alt.'" /></a>';
    return '<img src="/img/noavatar.png" alt="'.$alt.'" />';
}

function convertToFloat($price){
	if(is_float($price)){
		return number_format($price, 2); 
	}else{
		return number_format(floatval(str_replace(",",".", $price)), 2); 
	}
}


    function prictCategoryProduct($categoryId, $limit = null, $searchQery = null){
        global $conn, $meta, $lang;
        $pagi = 9;
        $pagination = '';
        $html = "";
        
        // RANDOM PRODUCTS ON HOMEPAGE -------------------------- 
        if($limit != null){
            $data = $conn->select("SELECT `id_article`, `sub_id`, `type`, `title_${lang}`, `subtitle_${lang}`, `price`, `avatar1`, `product_text`
                                   FROM `article` 
                                   WHERE `active`=1 AND `sub_id`=? 
                                   ORDER BY  RAND() DESC 
                                   LIMIT $limit", array( $categoryId ));
            
        // SEARCHING PRODUCTS VIA FORM --------------------------     
        }elseif($searchQery != null){
            $searchQery = addslashes($searchQery);
            $data = $conn->select("SELECT count(*) FROM `article`  WHERE `active`=1 AND `type`=5 AND (`title_sk` || `keywords` REGEXP '$searchQery' )");
            $count = $data[0]["count(*)"];

            if($count == 0) return '<p class="alert">Zadanému výrayu: <b>'.$searchQery.'</b> nevyhovuje žiadny záznam</p>';
            
            $offset = ($_GET['s'] == 1 ? 0 :  ($_GET['s'] * $pagi) - $pagi);
            $data = $conn->select("SELECT `id_article`, `sub_id`, `type`, `title_${lang}`, `subtitle_${lang}`, `price`, `avatar1`, `product_text`
                                   FROM `article` 
                                   WHERE `active`=1 AND `type`=5 AND (`title_sk` || `keywords` LIKE '%$searchQery%' )
                                   ORDER BY `title_sk` DESC 
                                   LIMIT $offset, $pagi");
            
            if($pagi < $count){
                $url = ($_GET['s'] == 1 ? $_SERVER['REQUEST_URI'] : substr($_SERVER['REQUEST_URI'], 0, strrpos($_SERVER['REQUEST_URI'], "?") ) );
                $nav = new Navigator($count, $_GET['s'], $url , $pagi);
                $nav->setSeparator("?s=");
                $pagination = $nav->simpleNumNavigator();
            }
            
        // NORMAL CATEGORY PRODUCTS --------------------------         
        }else{
            $data = $conn->select("SELECT count(*) FROM `article` WHERE `active`=1 AND `sub_id`=?", array( $categoryId ));
            $count = $data[0]["count(*)"];

            $offset = ($_GET['s'] == 1 ? 0 :  ($_GET['s'] * $pagi) - $pagi);
            $data = $conn->select("SELECT `id_article`, `sub_id`, `type`, `title_${lang}`, `subtitle_${lang}`, `price`, `avatar1`, `product_text`
                                   FROM `article` 
                                   WHERE `active`=1 AND `sub_id`=? 
                                   ORDER BY `order` DESC 
                                   LIMIT $offset, $pagi", array( $categoryId ));

            if($pagi < $count){
                $url = ($_GET['s'] == 1 ? $_SERVER['REQUEST_URI'] : substr($_SERVER['REQUEST_URI'], 0, strrpos($_SERVER['REQUEST_URI'], "?") ) );
                $nav = new Navigator($count, $_GET['s'], $url , $pagi);
                $nav->setSeparator("?s=");
                $pagination = $nav->simpleNumNavigator();
            }
        }
        
        // GENERATING HTML --------------------------     
        for($i = 0; $i < count($data); $i++ ){
            $href = linker($data[$i]["id_article"], 1);
          $html .= '<div class="product '.(($i+1) % 3 == 0 ? 'no-margin' : '' ).'">'.
                        '<strong class="h1">'.$data[$i]["title_${lang}"].'</strong>'.
                        '<em class="h2">'.$data[$i]["subtitle_${lang}"].'</em>'.
                        printAvatar($data[$i]["avatar1"], $data[$i]["title_${lang}"].' - '. $data[$i]["subtitle_${lang}"], 150,150,"auto").
                        '<span class="pricel" >cena</span>'.
                        '<p class="price">od '.  floatval($data[$i]["price"]).' &euro; / deň</p>'.
                        (strlen($data[$i]["product_text"]) > 1 ? '<p class="sale">'.$data[$i]["product_text"].'</p>' : '').
                        '<a href="'.$href.'">DETAIL</a>'.
                   '</div>';
        }
        return $html.'<div class="clear"></div>'.$pagination;
        
    }
    
function makeLinkByArticleId($articleId){
    global $conn;
    $data = $conn->select("SELECT `title_sk`,`type` FROM `article` WHERE  `id_article`=? LIMIT 1", array( $articleId ));
    return '<a href="'.linker($articleId, $data[0]['type']).'" title="'.$data[0]['title_sk'].'">'.$data[0]['title_sk'].'</a>';
}

function xss($data){
    $cleaned = array();
     foreach ($data as $rs) {
        $cleaned[] = array_map("cleanOutput", $rs);
      
    }
    return $cleaned;
}

function cleanOutput($str){
	return stripslashes(htmlspecialchars($str));
}