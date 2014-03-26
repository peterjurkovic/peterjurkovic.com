<?php
session_start(); 
ini_set("display_errors", 0);

require_once dirname(__FILE__)."/../admin/config.php";
require_once dirname(__FILE__)."/../admin/page/fnc.page.php";
require_once dirname(__FILE__)."/../admin/inc/class.Database.php";
require_once dirname(__FILE__)."/../admin/inc/class.MysqlException.php";
require_once dirname(__FILE__)."/../admin/inc/mailer/class.phpmailer.php";
require_once dirname(__FILE__)."/../admin/inc/fnc.skill.php";
require_once dirname(__FILE__)."/../inc/fnc.php";

$lang = "en";

if(isset($_GET['lang'])){
  $lang = $_GET['lang'];
}

try{
    
    if(!isset($_GET)){
      throw new Exception(getMessage("unexpectedError"));
    }

    if($_GET['act'] == 1){
      
      $conn = Database::getInstance($config['db_server'], $config['db_user'], $config['db_pass'], $config['db_name']);

      $data = array(
        "err" => 0,
        "html" => getProjects(9)
      );
    }

}catch(MysqlException $ex){
    $data = array( "err" => 1, "msg" => $str[$lang]["err_db"] );
}catch(Exception $ex){
    $data = array( "err" => 1, "msg" => $ex->getMessage() );
}

echo json_encode( $data );

exit;
?>
