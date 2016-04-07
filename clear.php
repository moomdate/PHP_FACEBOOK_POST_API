<p>&nbsp;</p>
<meta charset='utf-8'/>
<?php
include("includes/config.db.php");
$sql = "SELECT * FROM sb_user";
$query = mysql_query($sql);
$nums=mysql_num_rows($query);
$bad = 0;
while($token = mysql_fetch_assoc($query)) {
set_time_limit(0);
        $url = "https://graph.facebook.com/me/?access_token=".$token['user_token'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        $result = curl_exec($ch);
        curl_close ($ch);

        $array = json_decode($result,true);

        if(is_array($array['error'])) {

            $delete = "DELETE FROM sb_user WHERE user_token = '".$token['user_token']."'";
            $query2 = mysql_query($delete);
            if($query2){ 
                $bad++; 
            }
        }

}

//echo $bad;


if($bad==0)
{
?>
<table width="1145" border="0" align="center">
  <tr>
    <td width="1139" align="center"><div class="alert alert-success" role="alert">ไม่มี Token เสีย</div></td>
  </tr>
</table>
<?php } else {?>
<p>&nbsp;</p>
<table width="1145" border="0" align="center">
  <tr>
    <td width="912" align="center"><div class="alert alert-danger" role="alert">คุณได้ทำการ เคลียร์ Token จำนวน <?=$bad?>เรียบร้อยแล้ว</div></td>
  </tr>
</table>
<p>&nbsp;</p>
<?php } ?>
<center><div class="center"><label class="label label-success">จาก <?=$nums?>Token</label></div></center>
<p>&nbsp;</p>
<table width="200" border="0" align="center">
  <tr>
    <td align="center"><button onclick="location.href = '?result=admin_pa';" type="button" class="btn btn-success  btn-lg btn-block">ย้อนกลับ</button></td>
  </tr>
</table>
<p>&nbsp;</p>
