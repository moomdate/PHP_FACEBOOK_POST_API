# Facebook Post PHP API
##เขียนขึ้นเพื่อใช้งานอุปกรณ์ IOT
#### Example

``` php
<?php
  $AccessToken = "Your Access Token"; //get access token form 'https://developers.facebook.com/tools/debug/accesstoken/?app_id=41158896424'
  $Func = new Fac($AccessToken);
  $Func->postMessage("Hello!! Facebook");
?>
```
