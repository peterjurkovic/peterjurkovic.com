<?php

class Authenticate 
{
	private $conn;
	private $expiration = 3600;
	private $cookieExpiration = 5184000; // 60 dni
	
	public function __construct($conn){
		$this->conn = $conn;
	}
	
	public function login($login, $pass, $rememberMe, $token){
	    
	    if (!get_magic_quotes_gpc()) {
     		$login = trim(addslashes($login));
     		$pass = trim(addslashes($pass));
   		}      

		if(session_id() !=  $token){
			throw new AuthException("Platnosť formulára vypršala." , true);	
		}	
			
		$data = $this->conn->select("SELECT `id_user`,`id_user_type`, `login`, `pass`, `salt` FROM `user` WHERE `login`=?  AND `active`=1 AND `blocked`=0 LIMIT 1", array ( $login ) );
		if(count($data) == 1){
	
			if(	hash_hmac( 'sha256', $pass , $data[0]['salt']) == $data[0]['pass']){
			
				$this->setSessionData($data[0]);
				$this->deleteSessionById($data[0]['id_user']);
				$this->newSession($data[0]['id_user']);
				if($data[0]['id_user_type'] != 5){
					$this->logEntry($data[0]['id_user']);
				}
				if($rememberMe){
					$this->setCookies($data[0]);
				}
				return true;
			}else{
				throw new AuthException("Neplatné uživateľské heslo." );	
			}
			
			
		}else{
			throw new AuthException("Neplaté uživateľské meno." );	
		}
		return false;
	}
	
	
	
	public function isLogined(){
		if($this->areCookiesValid()){
			return true;
		}
		if(!isset($_SESSION['id']) || !isset($_SESSION['type'])){ 
			return false;
		}
		if(count( $this->getSession($_SESSION['id'])) != 1){
			return false;
		}
		$this->updateSession($_SESSION['id']);	
		return true;
	}
	
	public function logout(){
		$this->destroyCookies();
		$this->deleteOldSessions();
		$_SESSION = array();
		session_destroy();
	}
	
	
	
	public function logEntry($id){
		$this->conn->insert("INSERT INTO `user_log` (`id_user`, `user_agent`, `ip`, `time`) VALUES (?,?,?,?)", array($id, $_SERVER['HTTP_USER_AGENT'], $_SERVER['REMOTE_ADDR'], time()));
	}
	
	
	private function getHash(){
		if($_SESSION['login'] == "demo"){
			return 1;
		}
		return md5( $_SERVER['REMOTE_ADDR']. "*" .$_SERVER['HTTP_USER_AGENT'] );
	}
	
	
	private function newSession($uid){
		$this->conn->insert("INSERT INTO `user_session` (`id_user`, `session`, `time`) VALUES (? , ? , ?)", array( $uid, $this->getHash(), time() ));	
	}
	
	
	private function getSession($uid){
		return $this->conn->select("SELECT `session` FROM `user_session` WHERE `id_user`=? AND `time`>? AND `session`=? LIMIT 1", array( $uid, time() - $this->expiration, $this->getHash() ));	
	}
	
	
	private function deleteOldSessions(){
		$this->conn->insert("DELETE FROM `user_session` WHERE `time`<?", array( time() - $this->expiration ));	
	}
	
	private function deleteSessionById($uid){
		$this->conn->insert("DELETE FROM `user_session` WHERE `id_user`=? LIMIT 1	", array($uid));	
	}	
	
	private function updateSession($uid){
		$this->conn->update("UPDATE `user_session` SET `time`=".time()." WHERE `id_user`=? LIMIT 1", array( $uid ));	
	}
	
	private function generateCookieId($uid){
		return md5(sha1($uid."*".$_SERVER['REMOTE_ADDR']));
	}

	private function setCookies($userData){
		setcookie('uid', $userData['id_user'], time() + $this->cookieExpiration, "/", $this->getCookieDomain());
		setcookie('at', $this->generateCookieId($userData['id_user']), time() + $this->cookieExpiration, "/", $this->getCookieDomain()); 
	}


	private function areCookiesValid(){
		$uid = $this->getCookieUid();
		//print_r($_COOKIE);exit;
		if($uid != null && $uid != 0 && $this->generateCookieId($uid) == $this->getCookieHash()){
			if(!isset($_SESSION['id'])){
				$this->getUserAndSetSessionData($uid);
			}
			return true;
		}
		return false;
	}

	

	private function getCookieHash(){
		if(!isset($_COOKIE['at']) || strlen($_COOKIE['at']) == 0){
			return null;
		}
		return $_COOKIE['at'];
	}




	private function getCookieUid(){
		if(!isset($_COOKIE['uid']) || intval($_COOKIE['uid']) == 0){
			return null;
		}
		return (int)$_COOKIE['uid'];
	}

	private function destroyCookies(){
		setcookie("at", "", 1, "/", $this->getCookieDomain());
		setcookie("uid", "", 1, "/", $this->getCookieDomain());
	}


	private function getUserAndSetSessionData($uid){
		$data = $this->conn->select("SELECT `id_user`,`id_user_type`, `login` FROM `user` WHERE `id_user`=?  AND `active`=1 AND `blocked`=0 LIMIT 1", array ( $uid ) );
		if($data == null || sizeof($data) == 0){
			throw new AuthException("Užívateľ je blokovaný / neexistuje." );	
		}
		$this->setSessionData($data[0]);
		$this->logEntry($uid);
	}


	private function setSessionData($userData){
		$_SESSION = array();
		$_SESSION['login'] 	= $userData['login'];
		$_SESSION['type'] 	= $userData['id_user_type'];
		$_SESSION['id'] 	= $userData['id_user'];
	}


	public function isAdmin(){
		return isset($_SESSION['type']) && $_SESSION['type'] > 1;
	}


	private function getCookieDomain(){
		if($_SERVER['REMOTE_ADDR'] == "127.0.0.1"){
			return ".drilapp.dev";
		}else{
			return  str_replace("www.", "", $_SERVER['SERVER_NAME']);
		}
	}
}
?>