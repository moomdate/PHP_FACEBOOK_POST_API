<?php
class FacebookFunc{
	private $accessToken;
	public function __construct($accessToken){
		$this->accessToken = $accessToken;
	}
	private function curl_get_file_contents($URL){
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
	private function getAccessToken(){ //get facebook app token
		return $this->accessToken;
	}
	public function postMessage($message){ //action post message to timeline
		$url = "https://graph.facebook.com/me/feed?method=POST&message=".$message."&access_token=".$this->getAccessToken();
		$response = $this->curl_get_file_contents($url);
		$result = json_decode($response,true);
		return $result;
	}
}
?>