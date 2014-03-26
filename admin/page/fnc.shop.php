<?php
function nav($id, $text = false){
	global  $conn, $config;
	$html = "";
	$arr = getCateg( "categ", 0);		  
	if($text){
		for($i = 0; $i < count($arr); $i++){
			$html .= '<li><a href="/'.$config['shop_prefix'].'/'.$arr[$i]['link_sk'].'/"  title="'.$arr[$i]['category_name'].'" >'.$arr[$i]['category_name'].'</a>';	
		}
	}else{
		for($i = 0; $i < count($arr); $i++){
			$html .= '<a href="/'.$config['shop_prefix'].'/'.$arr[$i]['link_sk'].'/" class="abs c'.$arr[$i]['id_shop_category'].' '.
			($id == $arr[$i]['id_shop_category'] ? ' curr' : '').'" title="'.$arr[$i]['category_name'].'"></a>';	
		}
	}
	return $html;
}



function subNav($subId, $class = ""){
	global  $conn, $meta;
	$html = "";
	$arr = getCateg("categ", $subId);			  
	for($i = 0; $i < count($arr); $i++){
		$html .= '<li><a href="'.shopLinker($arr[$i]['id_shop_category']).'" class="'.$class.' '.
				 ($meta["id_shop_category"] == $arr[$i]['id_shop_category'] ? " cr" : '').'">'.$arr[$i]['category_name'].'</a></li>';	
	}
	return $html;
}



function getProduct($type = "basic", $id = null){
	global $conn;
	
	switch($type){
		case "link" :
			return $conn->select("SELECT `id_shop_product_status`, `id_shop_category`, `title_sk`, `header_sk` FROM  `shop_product` WHERE `id_shop_product`=? AND `active`=1 LIMIT 1", array( $id ));
		case "basic" :
			return $conn->select("SELECT `id_shop_product_status`, `id_shop_category`, `title_sk`, `header_sk`, `price`, `avatar1` FROM `shop_product` WHERE `id_shop_product`=? AND active=1", array( $id ));
		case "categ" :
			return $conn->select("SELECT `id_shop_product`,`id_shop_product_status`, `id_shop_category`, `title_sk`, `price`, `price_sale`, `avatar1` 
								  FROM `shop_product` 
								  WHERE `id_shop_category`=? AND active=1".($home ? " AND `home`=1" : ""), array( $id ));
		case "full" :
		default: 
			$p = $conn->select("SELECT * FROM  `shop_product` WHERE `id_shop_product`=? AND `active`=1 LIMIT 1", array( $id ));
			if(!$p) break; 
			$p[0]['shop_manufacturer_name'] = getManufacturerById( $p[0]['id_shop_manufacturer'] );
			return $p;		
	}
}

// ----------------------------------------------------------

function getCateg($type = "categ", $id = NULL){
	global $conn;
	
	switch($type){
		default:
		case "full" :
			return $conn->select("SELECT * FROM  `shop_category` WHERE `id_shop_category`=? AND `active`=1 LIMIT 1", array( $id ));	
		case "categ" :
			return $conn->select("SELECT `id_shop_category`,`category_name`, `link_sk` FROM `shop_category` WHERE `sub_id`=? AND active=1", array( $id ));		
		case "link" :
			return $conn->select("SELECT `id_shop_category`,`category_name`, `link_sk`, `sub_id`, `label` FROM  `shop_category` WHERE `id_shop_category`=? AND `active`=1 LIMIT 1", array( $id ));	
	}
}

// ----------------------------------------------------------


function shopLinker($cid){
	global  $meta, $conn,$config;
	
	$cat = getCateg($type = "link", $cid);	
	
	if($cat[0]["sub_id"] == 0){
	   return  "/".$config['shop_prefix']."/".$cat[0]["link_sk"]."/"; 
	}else{
	   return  "/".$config['shop_prefix']."/".parentCat($cat[0]["sub_id"])."/".$cat[0]["id_shop_category"]."/".$cat[0]["link_sk"]."/"; 
	}
	
}

// ----------------------------------------------------------

function parentCat($subID){
	global $conn;
	$cat = getCateg($type = "link", $subID);
	if($cat[0]["sub_id"] == 0){
		return ($cat[0]["link_sk"]);
	}else{
		return(parentCat($cat[0]['sub_id'])); 
	}
}

// ----------------------------------------------------------

function getManufacturerById($id){
	if(!isset($id) || $id == 1 || $id == 0) return;
	global $conn;
	$r = $conn->select("SELECT `shop_manufacturer_name` FROM `shop_manufacturer` WHERE `id_shop_manufacturer`=? LIMIT 1", array( $id ));
	return $r[0]['shop_manufacturer_name'];
}

// ----------------------------------------------------------

function printPrice($data){
	global $meta;
	if(($data['id_shop_product_status'] == 2 || $data['id_shop_product_status'] == 3) && $data['price_sale'] != 0){
		return $data['price_sale'].' '.$meta['s_currency'];
	}else{
		return $data['price'].' '.$meta['s_currency'];
	}
}

// ----------------------------------------------------------

function printVariants($pid){
	global $conn;
	$html = "";
	$data = $conn->select("SELECT `id_shop_variant`, `shop_variant_name` FROM `shop_variant` WHERE `id_shop_product`=? AND `active`=1", array( $pid ));
	for($i=0; $i < count($data); $i++) {   
		$html .= "<option value=\"".$data[$i]["id_shop_variant"]."\">".htmlspecialchars($data[$i]["shop_variant_name"])."</option>\n";
	}
	return $html; 
}

// ----------------------------------------------------------

function printStatus($id){
	if($id == 2){
		return '<img class="s" src="/img/s/2.png" alt="Akcia!" />'; // akcia
	}elseif($id == 3){
		return '<img class="s" src="/img/s/3.png" alt="Výpredaj!" />'; // vypredaj
	}elseif($id == 4){
		return '<img class="s" src="/img/s/1.png" alt="Novinka!" />'; // novinka
	}
}

// ----------------------------------------------------------

function printPageProducts($home = false){
	global $conn, $meta;
	$html = "";
	if(!$home){

		$ids = getChildersID($meta["id_shop_category"]);
		$where = "WHERE `active`=1 AND (`id_shop_category`=".implode(" OR `id_shop_category`=", $ids).")";
		
		$data = $conn->select("SELECT count(*) FROM `shop_product` ".$where);
		$count = $data[0]["count(*)"];
		$offset = ($_GET['s'] == 1 ? 0 :  ($_GET['s'] * $meta["s_shopPagi"]) - $meta["s_shopPagi"]);
		$data = $conn->select("SELECT `id_shop_product`,`id_shop_product_status`, `id_shop_category`, `title_sk`, `price`, `price_sale`, `avatar1` 
							   FROM `shop_product` $where LIMIT $offset, ".$meta["s_shopPagi"]);
		 $nav = new Navigator($count, $_GET['s'], shopLinker($meta["id_shop_category"]) , $meta["s_shopPagi"]);
		 $nav->setSeparator("");
		 $nav->setLabelNext("&raquo;");
		 $nav->setLabelPrev("&laquo;");
		 $html .= $nav->smartNavigator();
	}else{
		$data = $conn->select("SELECT `id_shop_product`,`id_shop_product_status`, `id_shop_category`, `title_sk`, `price`, `price_sale`, `avatar1` 
							   FROM `shop_product` 
							   WHERE `home`=1 AND active=1
							   LIMIT ".$meta["s_shopPagi"]);
	}
	for($i = 0; $i < count($data); $i++){
		$html .= '<div class="cover"><div class="sitem">';
		if(strlen($data[$i]['avatar1']) !=0){
			$html .= '<a href="/data/avatars/'.$data[$i]['avatar1'].'" class="thumb">'.printStatus($data[$i]['id_shop_product_status']).'<img class="zoom" src="/img/zoom.png" alt="Zobraziť" />
       		<img src="/i/242-242-crop/avatars/'.$data[$i]['avatar1'].'" alt="'.$data[$i]['title_sk'].'" /></a>';
		}else{
			$html .= '<img src="/i/242-242-crop/avatars/noimage.jpg" />';
		}
        $html .= '<a href="/shop/'.$data[$i]['id_shop_product'].'/'.SEOlink($data[$i]['title_sk']).'" class="detail" >detail</a><p class="price">'.
				 printPrice($data[$i]).'</p><a href="#'.$data[$i]['id_shop_product'].'" class="buy">kúpiť</a><div class="clear"></div></div></div>';
	}
	//$html .= $nav->smartNavigator();
	return $html;
}

// ----------------------------------------------------------

function getChildersID($id){
	global $conn;
	$ids = array( $id );
	if(hasChild($id)){
		$ids = iterate($id, $ids);
	}
	return $ids;	
}

function iterate($id, $ids){
	global $conn;
	$data =  $conn->select("SELECT `id_shop_category`, `sub_id` FROM `shop_category` WHERE `sub_id`=? AND `active`=1", array( $id) );
	for($i =0; $i < count( $data ); $i++){
		$ids[] = $data[$i]['id_shop_category'];
		if(hasChild($data[$i]['id_shop_category'])){
			$ids = iterate( $data[$i]['id_shop_category'], $ids);
 		}
	}
	return $ids;	
}

function hasChild($id){
	global $conn;
	return (count($conn->select("SELECT `id_shop_category` FROM `shop_category` WHERE `sub_id`=".$id. " AND `active`=1 LIMIT 1")) == 1 ? true : false);
}

// ----------------------------------------------------------
// BACKET fncs
	function getDeliveryPrice($id){
		global $conn; 
		$data = $conn->select("SELECT `price` FROM `shop_delivery` WHERE `id_shop_delivery`=? LIMIT 1", array( $id ));
		return $data[0]['price'];
	}

	function getBacketItem($pid, $vid){
		global $conn;
		
		if($vid == 0){
			$data = $conn->select("SELECT `title_sk`, `avatar1` FROM `shop_product` WHERE `id_shop_product`=? LIMIT 1", array( $pid ));
		}else{
			$data = $conn->select("SELECT p.`title_sk`, p.`avatar1`, v.`shop_variant_name` FROM `shop_product` p, `shop_variant` v WHERE 
			p.`id_shop_product`=? AND v.`id_shop_variant`= ? LIMIT 1", array( $pid , $vid ));
		}
		return $data[0];
	}
	
	
	function printBacket($dph, $curr){
		$html = "";
		foreach($_SESSION['cart'] as $item => $val){
			$i = explode("-", $item);
			$v = explode("-", $val);
			$data = getBacketItem($i[0], $i[1]);
			$html .= 
			'<tr>
				<td><img src="/i/30-30-crop/avatars/'.($data['avatar1'] != "" ? $data['avatar1'] : "noimage.jpg").'" alt="'.($data['avatar1'] != "" ? $data['avatar1'] : "noimage.jpg").'" /></td>
				<td>'.$data['title_sk'].(isset($data['shop_variant_name']) ? ', '.$data['shop_variant_name'] : '').'</td>
				<td class="c">'.number_format($v[1] * ($dph / 100 + 1), 2).' '.$curr.'</td>
				<td><input type="text" maxlength="4" value="'.$v[0].'"/><a href=#'.$item.' class="edit" title="Upraviť počet kusov?"></a></td>
				<td class="c"><a href=#'.$item.' class="del" title="Odstrániť položku z košíka?"></a></td>
			</tr>';
		}
		return $html;
	}
	
	
	function sum($dph, $curr,  $totalPrice){
		return '<table><tr><td class="w200">Celková sumna: </td><td class="w100">'.$totalPrice.' '.$curr.'</td></tr>'.
				'<tr><td class="w200">DPH '.$dph.'%:</td><td class="w100">'.number_format($totalPrice * ($dph / 100 + 1) - $totalPrice, 2).' '.$curr.'</td></tr>'.
				'<tr><td class="w200">Celková suma s DPH:</td><td class="w100">'.number_format(($totalPrice * ($dph / 100 + 1 ) ), 2).' '.$curr.'</td></tr></table><div class="clear"></div>';
	}

// ----------------------------------------------------------

function getMailText($tID){
	global $conn;
	$r = $conn->select("SELECT `val` FROM `shop_config_text` WHERE `key`='$tID' LIMIT 1");
	return $r[0]["val"];
}

function printMailItems($dph, $curr, $totalPrice){
	$html = '<br><h3>Položky objednávky</h3><table border="1" cellspacing="0" cellpadding="0"><tr><td align="left"><b>Názov tovaru</b></td><td align="right"><b>Cena s DPH</b></td><td><b>Počet</b></td><td align="right"><b>Cena spolu</b></td></tr>';
	
	foreach($_SESSION['cart'] as $item => $val){
			$i = explode("-", $item);
			$v = explode("-", $val);
			$data = getBacketItem($i[0], $i[1]);
			$html .= 
			'<tr>'.
				'<td>'.$data['title_sk'].(isset($data['shop_variant_name']) ? ', '.$data['shop_variant_name'] : '').'</td>'.
				'<td align="right">'.number_format($v[1] * ($dph / 100 + 1), 2).' '.$curr.'</td>'.
				'<td align="center">'.$v[0].'</td>'.
				'<td align="right">'.number_format( $v[1] * ($dph / 100 + 1) * $v[0], 2).' '.$curr.'</td>'.
			'</tr>';
		}
		$d = explode("-", $_SESSION['dp']);
		if($d[0] > 1){	
		$html .= '<tr>'.
				 '<td>'.getDeliveryName( $d[0] ).'</td>'.
				 '<td align="right">'.($d[0] == 2 ? $d[2] : number_format($d[2] * ( $dph / 100 + 1), 2)).' '.$curr.'</td>'.
				 '<td align="center">1</td>'.
				 '<td align="right">'.($d[0] == 2 ? $d[2] : number_format($d[2] * ( $dph / 100 + 1), 2)).' '.$curr.'</td></tr>';	 
		}
		
		$html .= '<tr><td><b>Celkom k úhrade:</b></td><td align="right"><b>'.number_format(getTotalOrderPrice($dph, $totalPrice, $d[0],  $d[2]), 2).' '.$curr.'</b></td></tr>';
		return $html.'<table>';
	
}


function getTotalOrderPrice($dph, $itemsPrice, $dID, $dPrice){
		if($dID == 2){
			return $itemsPrice * ($dph / 100 + 1) + $dPrice;
		}
		return ($itemsPrice + $dPrice) * ($dph / 100 + 1);
	}


function getDeliveryName( $id ){
	global $conn;
	$r =  $conn->select("SELECT `delivery_name` FROM `shop_delivery` WHERE `id_shop_delivery`=? LIMIT 1", array( $id ) );	
	return $r[0]['delivery_name'];
}
?>
