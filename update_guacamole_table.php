<?php
//.. create your data model & connection 
$string_connect='./connection.php';
require_once "$string_connect";
$connect = dbConnection();

$username="username"; //Your credentials goes here
$password="password";
$base_url='https://myguacamoleserver.mydomain.com/guacamole';
$service_url = $base_url . '/api/tokens';
$curl = curl_init($service_url);
curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($curl, CURLOPT_USERNAME, $username);
curl_setopt($curl, CURLOPT_PASSWORD, $password); 
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
//curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
//curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //IMP if the url has https and you don't want to verify source certificate
$curl_response = curl_exec($curl);
$response = json_decode($curl_response);
curl_close($curl);

//echo "authToken: ";
//echo $response->authToken;

$complete_url=$base_url.'/api/session/data/mysql/users?token='.$response->authToken;

$ch_guacamole = curl_init();
curl_setopt($ch_guacamole, CURLOPT_RETURNTRANSFER, 1);
//curl_setopt($ch_guacamole, CURLOPT_HTTPHEADER, array('PRIVATE-TOKEN:'));
curl_setopt($ch_guacamole, CURLOPT_URL, $complete_url);
$res = curl_exec($ch_guacamole);
curl_close($ch_guacamole);

$data = json_decode($res);

$searched_attribute="guac-email-address"; //2nd level of json
$id=1;
foreach ($data as $row){
	$json = $row->attributes;
	$value=$json->$searched_attribute;

$sql = <<<TAG
INSERT INTO guacamole_users (id, email) VALUES ('$id', '$value')
TAG;
	$result = mysqli_query($connect, $sql);
  $id++;
}
?>
