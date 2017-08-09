<?php
require_once "steamkey.php";
require_once "steam_class.php";

function getGameListCsv ($gamesList){
// zapis gier do pliku 
//dajemy nagówki  
$headers_list = $gamesList['response']['games'][0];
$first_line = array_keys($headers_list);

$gamesFile = fopen('games.csv','w');
fputcsv($gamesFile,$first_line, ";");
//doajemy tabele
$list = $gamesList['response']['games'];
foreach ($list as $fields) {
    fputcsv($gamesFile, $fields, ";");
}
fclose($gamesFile);
}

function getFriendsListView(){}


?>
<!DOCTYPE html>
<html>
<head>
	<title>STEAM</title>
	<link href="css/steamapi.css" type="text/css" rel="stylesheet" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
</head>
<body>
<h1> Skrypt wyświetla informacje o uzytkowniku usługi STEAM.</h1>
  <p>Przydatne linki</p>
  <p>Link do listy metod dostępnych w API Steam: <a href="http://serwer1725317.home.pl/jakiemetody.php" target="_blank">Lista metod</a></p>
  <p>przykaladowe ID 76561198014765204, 76561197970373921, 76561198002500907, 76561198008991804, 76561198018826719</p>	
	<form action="steamapi.php" method="POST">
	  <label>Please enter Steam user APP ID  </label><input type="text" name="user_appid"/>
	    <input type="submit" name="search" value="szukaj"/>
	</form>

<?php

/*
 */

//$_POST['user_appid']="";
//filter_var($_POST['user_appid'],FILTER_SANITIZE_NUMBER_INT);
	if(isset($_POST['user_appid'])){
    filter_var($_POST['user_appid'],FILTER_SANITIZE_NUMBER_INT);	 
	  $user = new SteamApi($_POST['user_appid'], $steam_api_key);	
    $userInfo = $user -> getSteamUserInfo();        
	  if (isset($userInfo['response']['players'][0])){
      $userGameRequest = $user -> createSteamUserGamesRequest();
      $userGameData = $user -> getResponse($userGameRequest);	  	  	    
      $userFriendRequest = $user -> createSteamUserFriendsRequest();
      $userFriendsData = $user -> getResponse($userFriendRequest);

      $test = $user -> getSteamUserInfo();
            
      $tableTest = new GameTableRenderer($userGameData);
      $gameTable = $tableTest -> CreateTable();
      	    
      $personaname  = $userInfo['response']['players'][0]['personaname'];
      $steamid      = $userInfo['response']['players'][0]['steamid'];
      $lastlogoff   = $userInfo['response']['players'][0]['lastlogoff'];
      $avatarmedium = $userInfo['response']['players'][0]['avatarmedium'];
      $timecreated  = $userInfo['response']['players'][0]['timecreated'];
      $create_date  = date('Y.m.d', $timecreated);
	  
	    $friends_id   = $userFriendsData['friendslist']['friends'][0]['steamid'];
      $lista_lista  = $userFriendsData['friendslist']['friends'];

      $friend_table ="<table class=\"table\">
        				  <thead class=\"thead-inverse\">
        				   <tr>
        					 <th> Nick znajomego </th> 
        					 <th> Steam ID znajomego</th>
        					 <th> Data rozpoczecia znajmości</th>
        				    </tr>
        				   </thead>";

      $friend_steam_id ="";


      foreach ($lista_lista as $row => $m){
        	$friend_since    = $lista_lista[$row]['friend_since'];
        	$friend_steam_id = $lista_lista[$row]['steamid'];
          $friend = new SteamApi($friend_steam_id,$steam_api_key);
          $firendReq = $friend -> createSteamUserInfoRequest();
          $friendData = $friend -> getResponse($firendReq);
        	//$friend_data = getUserInfo($friend_steam_id);
        	$friend_table .= "<tr>
        						<td>".$friendData['response']['players'][0]['personaname']."</td>
        						<td>".$friend_steam_id."</td>
	                            <td>".date('Y.m.d', $friend_since)."</td>	                            
        					  </tr>";	
      }
        $friend_table .="</table>";

        // dodajemy eksport do pliku.
       
        getGameListCsv($userGameData);
        
if (isset($_GET['file'])) {
  //writecsv();
    // Wszystko co w tym bloku zostanie wyswietlone zostanie zapisane do pliku
    header('Content-Disposition: attachment; filename="abc.csv"');                              
    readfile('proba.csv');
} else {
   $tekst = "Funkcja zapisuje tablice do pliku file.csv na serwerze.\n Plik ma taka zawartosc jak trzeba.\n
     <a href=\"?file=1\">Link</a>";
}        
        echo <<<_END
        <div class ="userInfoData_wrapper">
          <h4>Informacje o użytkowniku</h4>
          <img src='$avatarmedium' />
          <p>Name: $personaname</p>
          <p>Steam ID: $steamid </p>
          <p>On Steam since: $create_date</p>
        </div>
        <p>Lista gier w CSV <a href="http://serwer1725317.home.pl/proba.csv" target="_blank">pobierz</a></p>
        <a href="http://serwer1725317.home.pl/proba.csv" target="_blank">pobierz</a>        
        <p>Znajomi:</p>
        $friend_table
        <p>Lista gier</p>
        $gameTable
         
_END;
               
       }
       else{echo "Podano nieprawidlowy ID";}  
      }
    else {}
 

echo <<<_END
    
</div>
</body>
<html>
_END;

?>