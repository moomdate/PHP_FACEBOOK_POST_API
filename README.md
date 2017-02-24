# Facebook Post PHP API 
## This API i's not Secure. write for User with IOT report to Facebook.
#### Example 

``` php
<?php
  require_once('class.FacebookPost.php');
  $AccessToken = "Your Access Token"; //get access token form 'https://developers.facebook.com/tools/debug/accesstoken/?app_id=41158896424'
  $Func = new FacebookFunc($AccessToken);
  $Func->postMessage("Hello!! Facebook");
?>
```
