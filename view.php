<?php
$non=0;
include("includes/config.db.php");
$res=file_get_contents("token.json");
$array=json_decode($res,true);

echo "<pre>";
print_r($array);
echo "</pre>";
$date_a_time=date("Y-m-d H:i:s");
//echo $array['data']['0']['fb_id'];
$total=count($array['data']);

for($i=0;$i<$total;$i++)
{
	$fb_id=$array['data'][$i]['fb_id'];
	$fb_token=$array['data'][$i]['token'];
	mysql_query("INSERT INTO `sb_user` (`user_id`, `user_fb_id`, `user_name`, `user_token`, `user_datetime`) VALUES ('', '$fb_id', 'sb-followxx', '$fb_token', '$date_a_time');");
}
echo "เพิ่มไป".$total."token";
//test git///
?>