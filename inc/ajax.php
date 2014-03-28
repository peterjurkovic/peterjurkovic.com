<?php
session_start(); 
ini_set("display_errors", 1);

require_once dirname(__FILE__)."/../admin/config.php";
require_once dirname(__FILE__)."/../admin/page/fnc.page.php";
require_once dirname(__FILE__)."/../admin/inc/class.Database.php";
require_once dirname(__FILE__)."/../admin/inc/class.File.php";
require_once dirname(__FILE__)."/../admin/inc/class.MysqlException.php";
require_once dirname(__FILE__)."/../admin/inc/mailer/class.phpmailer.php";
require_once dirname(__FILE__)."/../admin/inc/fnc.main.php";
require_once dirname(__FILE__)."/../admin/inc/fnc.skill.php";
require_once dirname(__FILE__)."/../inc/fnc.php";
require_once dirname(__FILE__)."/../inc/messageSource.php";

if($_SERVER["CONTENT_TYPE"] != "application/json"){
  die();
}

$lang = "en";

if(isset($_GET['lang'])){
  $lang = $_GET['lang'];
}

try{
    
    if(!isset($_GET)){
      throw new Exception(getMessage("unexpectedError"));
    }

    $conn = Database::getInstance($config['db_server'], $config['db_user'], $config['db_pass'], $config['db_name']);

    switch (intval($_GET['act'])) {
      
      /* Loading project items */
      case 1:
          $data = array(
            "err" => 0,
            "html" => getProjects(9)
          );
      break;



      /* SENDS EMAIL */
      case 2:
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
        break;



        /* Prepare project detail */
        case 3:
          $id = intval($_GET['id']);
          $article = getArticle("fullHidden", $id , $lang);
          if(empty($article)){
            break;
          }
          $html = '<h3>'.$article[0]["title_$lang"].'</h3>';
          $html .= $article[0]["content_$lang"];
          $html .= getSkilss($id);
          $html .= pritGallery($id, 200, 200, $article[0]["title_$lang"]);
          incrementHit($id);
          $data = array(
            "err" => 0,
            "html" => $html
          );
        break;

        /* Skills filtering */
        case 4:
            $ids = !isset($_GET['items']) ? array() : $_GET['items']; 
            $html = '';

            if( ! empty($ids) ){
               $html .= '<div id="pj-selected-skills">'.  getHTMLSkillsByIDs($ids). '</div>';
            }
            $projects = filterProjectBySkills( $ids );

            if(strlen($projects) == 0){
              $html .= '<p class="pj-notfound">'.getMessage("noProjectFound").'</p>';
            }else{
              $html .= $projects;  
            }

            


            $data = array(
            "err" => 0,
            "html" =>  $html
          );
        break;



      default:
        throw new Exception("Unknown operation", 1);
      break;
    }
    
}catch(MysqlException $ex){
    $data = array( "err" => 1, "msg" => $ex->getMessage() );
}catch(Exception $ex){
    $data = array( "err" => 1, "msg" => $ex->getMessage() );
}

echo json_encode( $data );

exit;
?>
