
<?php
include("includes/config.db.php");
$date_a_time=date("Y-m-d H:i:s");
$array=file_get_contents("HTC.txt");
$token=explode("\n", $array);
echo $token[0];
$total=count($token);
for($x=0;$x<$total;$x++)
{
	set_time_limit(160);
	mysql_query("INSERT INTO `sb_user` (`user_id` ,`user_fb_id` ,`user_name` ,`user_token` ,`user_datetime`)VALUES ('',  '001',  'sb-htc',  '$token[$x]',  '$date_a_time')");
}
echo $total;
?>