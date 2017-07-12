<?php
require_once "steamkey.php";

 function my_var_dump($var){

  	echo "<pre>".var_export($var,true)."</pre>";
  }


 function steamConnect ($url){

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


function getSteamGames ($steam_user_id){

 	global $api_key;
 	$get_games ="http://api.steampowered.com/IPlayerService/GetOwnedGames/v0001/?key=".$api_key."&format=json&input_json={\"steamid\":".$steam_user_id.",\"include_appinfo\":true,\"include_played_free_games\":false}"; 	
 	
 	$user_game = steamConnect($get_games);
   
 }


function getUserInfo ($steam_user_id){
	global $api_key;
	$user_info_request = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".$api_key."&steamids=".$steam_user_id."&format=json";

	$user_info = steamConnect($user_info_request);	
	return $user_info;  
}


function getFriendList ($steam_user_id){
	global $api_key;
	$friend_list = "http://api.steampowered.com/ISteamUser/GetFriendList/v0001/?key=".$api_key."&steamid=".$steam_user_id."&relationship=friend";
	$user_data = steamConnect($friend_list);	
	return $user_data;

}


function getFriendsListView(){}

?>
<!DOCTYPE html>
<html>
<head>
	<title>STEAM</title>
	<link href="css/steamapi.css" type="text/css" rel="stylesheet" />
</head>
<body>
<h1> Skypt wyświetla informacje o uzytkowniku usługi STEAM.</h1>
  <p>Przydatne linki</p>
  <p><a href="http://serwer1725317.home.pl/jakiemetody.php" target="_blank">Lista metod</a></p>
  <p>przykaladowe ID 76561198014765204, 76561197970373921, 76561198002500907, 76561198008991804, 76561198018826719</p>	
	<form action="?action=search" method="POST">
	  <label>Please enter Steam user APP ID  </label><input type="text" name="user_appid"/>
	    <input type="submit" name="search" value="szukaj"/>
	</form>
	    
<?php

/*
*
*
*
*
*
*
 */

if (isset($_GET['action'])&& ($_GET['action']=="search")){
	if($_POST['user_appid']!=""){	 
	  $user_info = getUserInfo($_POST['user_appid']);	  
	  if (isset($user_info['response']['players'][0])){	  	  
	    getSteamGames($_POST['user_appid']);
	    $steam_friend_list = getFriendList($_POST['user_appid']);

	    $personaname  = $user_info['response']['players'][0]['personaname'];
        $steamid      = $user_info['response']['players'][0]['steamid'];
        $lastlogoff   = $user_info['response']['players'][0]['lastlogoff'];
        $avatarmedium = $user_info['response']['players'][0]['avatarmedium'];
        $timecreated  = $user_info['response']['players'][0]['timecreated'];
        $create_date  =  date('Y.m.d', $timecreated);
	  
	    $friends_id = $steam_friend_list['friendslist']['friends'][0]['steamid'];
        $lista_lista = $steam_friend_list['friendslist']['friends'];

        $friend_table ="<table class=\"friend_table\">
        				<tr>
        					<th> Nick znajomego </th> 
        					<th> Steam ID znajomego</th>
        					<th> Data rozpoczecia znajmości</th>";

        $friend_steam_id ="";


        foreach ($lista_lista as $row => $m){
        	$friend_since    = $lista_lista[$row]['friend_since'];
        	$friend_steam_id = $lista_lista[$row]['steamid'];
        	$friend_data = getUserInfo($friend_steam_id);
        	$friend_table .= "<tr>
        						<td>".$friend_data['response']['players'][0]['personaname']."</td>
        						<td>".$friend_steam_id."</td>
	                            <td>".date('Y.m.d', $friend_since)."</td>	                            
        					  </tr>";	
        }
        $friend_table .="</table>";

        echo <<<_END
        <div class ="user_info_wrapper">
        <h4>Informacje o użytkowniku</h4>
        <img src='$avatarmedium' />
        <p>Name: $personaname</p>
        <p>Steam ID: $steamid </p>
        <p>On Steam since: $create_date</p>
        <p>Znajomi:</p>
        <p>Lista z poza bloku $friend_table</p> 
_END;

        foreach ($lista_lista as $fr =>$p){
	       $friend_since = $lista_lista[$fr]['friend_since'];
	       echo "<p> Steam ID of friend: ".$lista_lista[$fr]['steamid']
	       ." friends since: ".date('Y.m.d', $friend_since)."</p>";
         }
       }
       else{echo "Podano nieprawidlowy ID";}  
      }
    else {echo "nie podano ID usera, prosze uzupelnic ";}
} 

echo <<<_END
    
</div>
</body>
<html>
_END;

?>