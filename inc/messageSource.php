
<?php
	
	function getMessage(){
		global $lang;
		$args = func_get_args();
		$argsSize = func_num_args();

		
		$messages["en"] = array(
			"yes" => "Yes",
			"no" => "No",
		);

		$key = $args[0];
		$msg = "";
		if(!array_key_exists($key, $messages[$lang])){
			if(!array_key_exists($key, $messages["sk"])){
				return "";
			}
			$msg = $messages["sk"][$key];
		}else{
			$msg = $messages[$lang][$key];
		}
		if($argsSize > 1){
			for ($i = 1; $i < $argsSize; $i++) {
				$msg = str_replace("{".($i-1)."}", $args[$i], $msg);
			}
		}
		return $msg;
	}

   function printMessage($key){
   		echo getMessage($key);
   }


?>