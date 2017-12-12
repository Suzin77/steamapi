<?php  
require_once "steamkey.php"; // tutaj jest klucza do api 

function my_var_dump($var){
    echo "<pre>".var_export($var,true)."</pre>";
}

function suma($a,$b)
{
   return $a+$b;
}

class SteamUserApi {
   
	private $steamUserId;
	private $steamKey;	
	private $gameData;
	
	function __construct ($steamUserId, $steamKey){
		$this -> steamUserId = $steamUserId;
		$this -> steamKey = $steamKey;
		//$this -> setSteamUserInfo();
		if ($this -> idChecker($steamUserId)){
		    $userInfo = $this -> getSteamUserInfo();
    	            $this -> personaname = $userInfo['response']['players'][0]['personaname'];
    	            $this -> steamId = $userInfo['response']['players'][0]['steamid'];    	
    	            $this -> avatarmedium = $userInfo['response']['players'][0]['avatarmedium'];
    	            $this -> timecreated  = $userInfo['response']['players'][0]['timecreated'];
    	            $this -> createDate  = date('Y.m.d',$this -> timecreated);
    	}
	else{
    	    echo "Podano niepoprawny Steam ID";
    	}
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
    	/* struktura odpowiedzi
    	array (
  'response' => 
  array (
    'players' => 
    array (
      0 => 
      array (
        'steamid' => '76561198014765204',
        'communityvisibilitystate' => 3,
        'profilestate' => 1,
        'personaname' => 'suzin77',
        'lastlogoff' => 1503181345,
        'commentpermission' => 1,
        'profileurl' => 'http://steamcommunity.com/profiles/76561198014765204/',
        'avatar' => 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/bb/bbe70c4fd7e03fbbe272d9a566ba887c0f0a36d3.jpg',
        'avatarmedium' => 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/bb/bbe70c4fd7e03fbbe272d9a566ba887c0f0a36d3_medium.jpg',
        'avatarfull' => 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/bb/bbe70c4fd7e03fbbe272d9a566ba887c0f0a36d3_full.jpg',
        'personastate' => 1,
        'realname' => 'Patryk Sos',
        'primaryclanid' => '103582791433833633',
        'timecreated' => 1256240246,
        'personastateflags' => 0,        
        'loccountrycode' => 'PL',
        'locstatecode' => '62',
      ),
    ),
  ),
)
    	*/
    } 

    function getSteamUserFriends(){
        $request = $this -> createSteamUserFriendsRequest();
    	$response = $this -> getResponse($request);
    	return $response;

    	/*
		struktura odpowiedzi :
		array (
  'friendslist' => 
  array (
    'friends' => 
    array (
      0 => 
      array (
        'steamid' => '76561197976083738',
        'relationship' => 'friend',
        'friend_since' => 1261516327,
      ),
      1 => 
      array (
        'steamid' => '76561197989037112',
        'relationship' => 'friend',
        'friend_since' => 1324896738,
      ),
      2 => 
      array (
        'steamid' => '76561198012964228',
        'relationship' => 'friend',
        'friend_since' => 1263761162,
      ),
      3 => 
      array (
        'steamid' => '76561198018826719',
        'relationship' => 'friend',
        'friend_since' => 1266342889,
      ),
    ),
  ),
)
    	*/
	}

    function setSteamUserInfo (){
    	$userInfo = $this -> getSteamUserInfo();
    	$this -> personaname = $userInfo['response']['players'][0]['personaname'];
    	$this -> steamId = $userInfo['response']['players'][0]['steamid'];    	
    	$this -> avatarmedium = $userInfo['response']['players'][0]['avatarmedium'];
    	$this -> timecreated  = $userInfo['response']['players'][0]['timecreated'];
    	$this -> createDate  = date('Y.m.d',$this -> timecreated);
    }



    function getSteamUserName(){
    	$name =  $this->getSteamUserInfo();
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
	if ($output === false){ 
	    echo "Crul error: ".crul_error($ch);
	} 
	else{	
	   $data = json_decode($output,true);
	   curl_close ($ch);      
	   return $data;
	}     	
    }

    function closeSteamConnect(){}

    function idChecker($steamId){
    	$userInfo = $this -> getSteamUserInfo();
    	$userId = @$userInfo['response']['players'][0]['steamid'];

    	if ($steamId === $userId){
    		return true;
    	}
    	return false;

    }
   
}//koniec klasy SteamApi

class SteamConnect extends SteamUserApi  {

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
