<?php
session_start(); 
ini_set("display_errors", 1);

require_once dirname(__FILE__)."/../admin/config.php";
require_once dirname(__FILE__)."/../admin/page/fnc.page.php";
require_once dirname(__FILE__)."/../admin/inc/class.Database.php";
require_once dirname(__FILE__)."/../admin/inc/class.MysqlException.php";
require_once dirname(__FILE__)."/../admin/inc/mailer/class.phpmailer.php";
require_once dirname(__FILE__)."/../admin/inc/fnc.main.php";
require_once dirname(__FILE__)."/../admin/inc/fnc.skill.php";
require_once dirname(__FILE__)."/../inc/fnc.php";
require_once dirname(__FILE__)."/../inc/messageSource.php";

$lang = "en";

if(isset($_GET['lang'])){
  $lang = $_GET['lang'];
}

try{
    
    if(!isset($_GET)){
      throw new Exception(getMessage("unexpectedError"));
    }

    $conn = Database::getInstance($config['db_server'], $config['db_user'], $config['db_pass'], $config['db_name']);
    
    if($_GET['act'] == 1){
      
      

      $data = array(
        "err" => 0,
        "html" => getProjects(9)
      );
    }

    if($_GET['act'] == 2){
      
      if(empty($_GET['email']) || empty($_GET['name']) ||
         empty($_GET['message']) || !isEmail($_GET['email'])){
          echo json_encode( array( "err" => 1, "msg" => getMessage("invalidDataError")) );
          exit;
      } 
      $conf = getConfig($conn, "config", "page");
      if($_SERVER['REMOTE_ADDR']  != "127.0.0.1"){
            $mail = new PHPMailer();
            $mail->From = $_GET['email'];
            $mail->FromName = $_GET['name'];
            $mail->AddAddress( $conf['c_email'] ); 
            $mail->WordWrap = 120; 
            $mail->IsHTML(false);
            $mail->Subject = "Message form peterjurkovic.com";
            $mail->Body    = $_GET['message'];
            $mail->Send(); 
      }

      $data = array(
        "err" => 0,
        "msg" => getMessage("emailSent")
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
