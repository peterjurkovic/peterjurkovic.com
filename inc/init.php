<?php
 if(!isset($_GET['s']))	{ $_GET['s'] = 1; }else{  $_GET['s'] = intval( $_GET['s'] ); }

    require_once dirname(__FILE__)."/../admin/config.php";
    require_once dirname(__FILE__)."/../admin/inc/fnc.main.php";
    require_once dirname(__FILE__)."/../admin/page/fnc.page.php";
    require_once dirname(__FILE__)."/../admin/inc/fnc.skill.php";
    require_once dirname(__FILE__)."/../inc/fnc.php";
    require_once dirname(__FILE__)."/../inc/messageSource.php";

    function __autoload($class) {
            require_once dirname(__FILE__).'/../admin/inc/class.'.$class.'.php';
    }
    
    try{
        $lang = (!isset($_GET['lang']) ? "en" : $_GET['lang']);
        $conn = Database::getInstance($config['db_server'], $config['db_user'], $config['db_pass'], $config['db_name']);
        $meta = MAIN();
    }catch(MysqlException $e){
        echo '<!DOCTYPE HTML><html><head><meta charset="utf-8" /></head><body>'.
             '<h1>Some error occured</h1>'.
             '<p>Some error occured, try it again later please.</p>'.
             '</body></html>';
        exit;
    } 
?>