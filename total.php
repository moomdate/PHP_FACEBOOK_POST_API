<?php
include("includes/config.db.php");
$sql=mysql_num_rows(mysql_query("SELECT * FROM sb_user"));
echo $sql;
?>