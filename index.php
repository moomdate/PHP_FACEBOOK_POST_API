<?php
function curl_get_file_contents($URL){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$contents = curl_exec($ch);
	curl_close($ch);
	
	if($contents){
		return $contents;
	}else{
		return false;
	}
}
function facebook_post($message,$token){
	$url = "https://graph.facebook.com/me/feed?method=POST&message=".$message."&access_token=".$token;
	$response = curl_get_file_contents($url);
	$result = json_decode($response,true);
	return $result;
}
$messages=$_GET['ms'];
facebook_post($messages,"CAAAACZAVC6ygBAOVI83qZAlwLTXq0aZAKasdLQCyEgMW9pDUoC5o93EVyizE7VpyJBLnZALzZApIymDKDlRxHnA00nVVFPZBnHDZB18wAF32E4vn75VIfW4KepxiaopIphPiWWCzpausA7vqYBYNaqbSK6rG6ZCrwnLfOL6T0o6OkKtVaYj9246H3Ccgw7SNdj4leGS3azV1PQZDZD");

?><!DOCTYPE html>
<html>
<head>
	<title>test</title>
</head>
<body>
<p>testttt</p>
</body>
</html>