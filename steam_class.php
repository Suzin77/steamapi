<?php  
require_once "steamkey.php"; // tutaj jest klucza do api 

function my_var_dump($var){

  	echo "<pre>".var_export($var,true)."</pre>";
  }

class PomiarCzasu{
	/* Do pomiarów wykonywania sie metod z api steam dal użytkowników z wieloma znajomymi ewentualnie sprawdzić microtime()
	aktualnie nieużywana.
	*/
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
		$request = $this -> createSteamUserGamesRequest();
		$response = $this -> getResponse($request);
		return $response;
	}

    function getSteamUserInfo(){
    	$request = $this -> createSteamUserInfoRequest();
    	$response = $this -> getResponse($request);
    	return $response;
    } 

    function getSteamUserFriends(){
    	$request = $this -> createSteamUserFriendsRequest();
    	$response = $this -> getResponse($request);
    	return $response;
	}

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
   
}//koniec klasy SteamApi

class SteamConnect extends SteamApi  {

		private $user_info_request;
		private $steamKey;
		function __construct (){}
}


class SteamConn {
	/*
	klasa nie jest używana aktualnie, zastanowic sie nad nią
	*/ 

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


class GameTableRenderer {

	private $tableHeader = "<table class=\"table\">
				    <thead class=\"thead-inverse\">
        			   <tr>
        			   	  <th> L. p.</th>
        				  <th> Game Name</th> 
        				  <th> Steam Game ID </th>
        				  <th> Game logo </th>
        				  <th> Cena </th>
        			   </tr>
        			</thead>";

	function __construct ($gameList){
		$this -> responseGames = $gameList['response']['games']; 
		$this -> appId = $gameList['response']['games'][0]['appid'];
		$this -> gameName = $gameList['response']['games'][0]['name'];
	    $this -> imgLogoUrl = $gameList['response']['games'][0]['img_logo_url'];
	    $this -> imgIconUrl = $gameList['response']['games'][0]['img_icon_url'];
	    $this -> PlaytimeForever = $gameList['response']['games'][0]['playtime_forever'];           
	}

	function CreateTable(){
		/*
		metoda tworzy html tabeli z lista gier. Kleimy zdefiniowany w klasie nagłówek i doklejami html z foreach idacy po id gry.
		W przyszlosci rozwarzyc generowanie adresów url do osobnej metody.
		zmienic hierarchie, CreateTable na CreateGameTable a klasa na TableRenderer.
		Ma byc tak abysmy mogli generowac dowolne tabele z wybranymi nagłówkami. 
		*/

		$gameTable = $this -> tableHeader;

		foreach ($this -> responseGames as $key => $n){
			$lp = $key+1;
			$steamAppId = $this -> responseGames[$key]['appid'];			
    	    $steamAppShopUrl = "http://store.steampowered.com/app/".$steamAppId;
    	    $steamAppName  = $this -> responseGames[$key]['name'];
    	    $steamAppLogo = $this -> responseGames[$key]['img_logo_url'];
    	    $steamAppLogoUrl = "http://media.steampowered.com/steamcommunity/public/images/apps/".$steamAppId."/".$steamAppLogo.".jpg";
    	    $gameTable .="<tr>
    						<td>".$lp."</td>
    						<td>".$steamAppName."</td>
    						<td>".$steamAppId."</td>
                            <td><img src=\"".$steamAppLogoUrl."\" style=\"padding:1px\"/></td>
                            <td><a href = \"".$steamAppShopUrl."\" target=\"_blank\">Link do strony</a></td>	                            
    					  </tr>";	 
		}
		$gameTable .= "</table>";

		return $gameTable;
	} 
}

?>