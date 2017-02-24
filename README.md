# Facebook Post API (PHP)  
## This API i's not Secure. write for User with IOT report to Facebook.

### Can get Facebook access token there are 2 steps!!
1) Agree for use app (HTC App): [Click!!](https://www.facebook.com/v1.0/dialog/oauth?redirect_uri=fbconnect%3A%2F%2Fsuccess&scope=user_videos%2Cfriends_photos%2Cfriends_videos%2Cpublish_actions%2Cuser_photos%2Cfriends_photos%2Cuser_activities%2Cuser_likes%2Cuser_status%2Cfriends_status%2Cpublish_stream%2Cread_stream%2Cstatus_update&response_type=token&client_id=41158896424&_rdr) 

2) get access token: [Click!!](https://developers.facebook.com/tools/debug/accesstoken/?app_id=41158896424)


#### Example 

``` php
<?php
  require_once('class.FacebookPost.php');
  $AccessToken = "Your Access Token";
  $Func = new FacebookFunc($AccessToken);
  $Func->postMessage("Hello!! Facebook");
?>
```

``
---------SnailBot----------
``
