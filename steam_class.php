<?php  
require_once "steamkey.php"; // tutaj jest klucza do api 

function my_var_dump($var){

  	echo "<pre>".var_export($var,true)."</pre>";
  }

class PomiarCzasu{
	public $timeStart;
	public $timeStop;
	public $timePass;

	function pomiarStart(){
		$timeStart = time();
		return $timeStart;
	}

	function pomiarStop(){
		$timeStop = time();
		return $timeStop;
	}

	function timePass(){
		$timePass =  $timeStop - $timeStart;
		return $timePass;
	}
	
		

}//koniec klasy PomiarCzasu


class SteamApi {
   
	private $steamUserId;
	private $steamKey;	
	private $gameData;

	function __construct ($steamUserId, $steamKey){
		$this -> steamUserId = $steamUserId;
		$this -> steamKey = $steamKey;		
	} 

	function getSteamUserGames(){

	}

    function getSteamUserInfo(){} 

    function getSteamUserName(){
    	return $this->steamUserId;
    }

    function createSteamUserGamesRequest(){
    	global $steam_api_key;
 	    $steamGamesRequest ="http://api.steampowered.com/IPlayerService/GetOwnedGames/v0001/?key=".$this -> steamKey."&format=json&input_json={\"steamid\":".$this -> steamUserId.",\"include_appinfo\":true,\"include_played_free_games\":false}";     		 		
 	    return $steamGamesRequest;   
    }

    function createSteamUserInfoRequest(){
    	global $steam_api_key;
	    $steamUserInfoRequest = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".$this -> steamKey."&steamids=".$this -> steamUserId."&format=json";   		
	    return $steamUserInfoRequest;
    }

    function createSteamUserFriendsRequest(){
    	global $steam_api_key;
	$steamUserFriendsRequest = "http://api.steampowered.com/ISteamUser/GetFriendList/v0001/?key=".$this -> steamKey."&steamid=".$this -> steamUserId."&relationship=friend";
		return $steamUserFriendsRequest;
    }

    function getResponse($url){
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL,$url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    $output = curl_exec($ch);
	    if ($output === false)
	     { 
	       echo "Crul error: ".crul_error($ch);
	     } 
	     else{	
	       $data = json_decode($output,true);
	       curl_close ($ch);      
	       return $data;
	     }     	
    }

    function closeSteamConnect(){}

    
}//koniec klasy

class SteamConnect extends SteamApi  {

		private $user_info_request;
		private $steamKey;
		function __construct (){}

}


class SteamConn {

	private $url;
	private $crul_init;
	private $output;
	private $crul_close;
	private $curl_setopt;
	private $stemaUserInfo;


	public function __construct ($url){
		$this -> url = $url;
		$this -> curl_init = $crul_init;
		$this -> curl_close = $curl_close;
		$this -> crul_setopt = curl_setopt($this->curl_init, CURLOPT_URL,$this->url);

		$this ->output = curl_exec($this ->curl_init);
	}	
	
}

$suzin = new SteamApi("76561198014765204", $steam_api_key);
//echo $suzin -> getSteamUserName()."</br>";
//echo $suzin -> steamUserId;
$gamesRequest = $suzin -> createSteamUserGamesRequest();
$data = $suzin -> getResponse($gamesRequest);
//my_var_dump($data);
//my_var_dump($suzin);


?>