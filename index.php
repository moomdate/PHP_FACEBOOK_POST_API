<?php
error_reporting(0);
function transDiffForHumans($string) {
    $vareM5AivkN4oo = array("second" => "วินาทีที่แล้ว", "minute" => "นาทีที่แล้ว", "hour" => "ชั่วโมง", "day" => "วันที่แล้ว", "week" => "สับดาห์ที่แล้ว", "month" => "เดือนที่แล้ว", "year" => "ปีที่แล้ว", "s" => "", "from now" => "", "ago" => "");
    $string = str_replace(array_keys($vareM5AivkN4oo), array_values($vareM5AivkN4oo), $string);
    return $string;
}
defined("BASE_PATH") || define("BASE_PATH", __DIR__);
define("TOKEN_ERROR", 0);
define("TOKEN_OK", 1);
define("GROUP_USER", 1);
define("GROUP_VIP", 2);
define("GROUP_ADMIN", 3);
define("GROUP_SUPER_ADMIN", 4);
require (BASE_PATH . "/vendor/autoload.php");
class_alias("Illuminate\Database\Capsule\Manager", "DB");
class App extends Slim\Slim {
    public $user = null;
    public $isGuest = true;
    public function loadSetting() {
        $var1FFgJG0UcM5b = $this->cache;
        if (!($var1kk1gMlrA1mj = $var1FFgJG0UcM5b->has("setting"))) {
            $var1kk1gMlrA1mj = array();
            $var9xtCN9LzH2gl = $this->db->table("setting")->get();
            foreach ($var9xtCN9LzH2gl as $var3t96RyMcq8wI) {
                if (!($var87yUmB7BzGTv = @unserialize($var3t96RyMcq8wI["value"]))) {
                    $var87yUmB7BzGTv = $var3t96RyMcq8wI["value"];
                }
                $var1kk1gMlrA1mj[$var3t96RyMcq8wI["name"]] = $var87yUmB7BzGTv;
            }
            $var1FFgJG0UcM5b->set("settings", $var1kk1gMlrA1mj, 3600);
        }
        $this->config("app", (array)$var1kk1gMlrA1mj);
    }
    public function reloadSetting() {
        $this->cache->delete("setting");
        $this->loadSetting();
    }
    public function updateSetting($data) {
        foreach ($data as $varayvunBfHJTt7 => $var87yUmB7BzGTv) {
            if (is_array($var87yUmB7BzGTv)) {
                $var87yUmB7BzGTv = serialize($var87yUmB7BzGTv);
            }
            $var81CEijifMqtL = $this->db->table("setting")->where("name", $varayvunBfHJTt7);
            if ($var81CEijifMqtL->exists()) {
                $var81CEijifMqtL->update(array("value" => $var87yUmB7BzGTv));
                continue;
            }
            $this->db->table("setting")->insert(array("name" => $varayvunBfHJTt7, "value" => $var87yUmB7BzGTv));
        }
        $this->reloadSetting();
    }
    public function logout() {
        unset($_SESSION["user_id"]);
    }
    public function authenticateForRole($role, $redirect = null) {
        return function () use ($role, $redirect, $app) {
            $check = false;
            $group = $app->user["group"];
            switch ($role) {
                case "super_admin":
                    $check = GROUP_SUPER_ADMIN <= $group;
                break;
                case "admin":
                    $check = GROUP_ADMIN <= $group;
                break;
                case "vip":
                    $check = GROUP_VIP <= $group;
                break;
                case "user":
                    $check = GROUP_USER <= $group;
                break;
                case "guest":
                    $app->urlFor("login_by_token");
            }
        };
    }
    public function graphApi($api, $data = array(), $method = "GET") {
        $var6OuhrINL5qx0 = "https://graph.facebook.com/" . $api;
        $var3giwLHmAtqFU = array(CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => 2, CURLOPT_RETURNTRANSFER => true, CURLOPT_CUSTOMREQUEST => $method);
        if ($method === "GET") {
            $var6OuhrINL5qx0 = $var6OuhrINL5qx0 . (strpos($var6OuhrINL5qx0, "?") === false ? "?" : "&") . http_build_query($data);
        } else {
            if ($method === "POST") {
                $var3giwLHmAtqFU[CURLOPT_POST] = true;
                $var3giwLHmAtqFU[CURLOPT_POSTFIELDS] = $data;
            }
        }
        $var9MXlSHyLC9vJ = curl_init($var6OuhrINL5qx0);
        curl_setopt_array($var9MXlSHyLC9vJ, $var3giwLHmAtqFU);
        $var59ZCEEQxJGVX = curl_exec($var9MXlSHyLC9vJ);
        curl_close($var9MXlSHyLC9vJ);
        return json_decode($var59ZCEEQxJGVX, true);
    }
}
$config = array_merge(require (BASE_PATH . "/config.php"), array("mode" => "prod", "debug" => false, "templates.path" => BASE_PATH . "/templates", "cookies.encrypt" => true));
$app = new App($config);
$app->add(new Slim\Middleware\SessionCookie(array("name" => "alien_session", "expires" => $app->config("sessions.expires"))));
$app->container->singleton("logWriter", function () use ($app) {
    return new Slim\Logger\DateTimeFileWriter(array("path" => $app->config("logger.path")));
});
$app->container->singleton("db", function () use ($app) {
    $capsule = new DB();
    $capsule->addConnection(array_merge($app->config("database"), array("driver" => "mysql")));
    $capsule->setAsGlobal();
    return $capsule;
});
$app->container->singleton("cache", function () use ($app) {
    $config = $app->config("cache");
    switch ($config["adapter"]) {
        case "file":
            $adapter = new Desarrolla2\Cache\Adapter\File($config["file"]["path"]);
        break;
        case "apc":
            $adapter = new Desarrolla2\Cache\Adapter\Apc();
        break;
        default:
            $adapter = new Desarrolla2\Cache\Adapter\NotCache();
    }
    if (isset($config[$config["adapter"]]["options"])) {
        foreach ((array)$config[$config["adapter"]]["options"] as $key => $value) {
            $adapter->setOption($key, $value);
        }
    }
    return new Desarrolla2\Cache\Cache($adapter);
});
$app->notFound(function () use ($app) {
    $app->render("404.php");
});
$app->hook("slim.before.router", function () use ($app) {
    $app->view()->appendData(array("app" => $app, "user" => $app->user, "baseUrl" => rtrim($app->request->getRootUri(), "/"), "currentUrl" => $app->request->getResourceUri()));
});
$app->hook("slim.before", function () use ($app) {
    if (strpos($app->request->getResourceUri(), "/install") !== false) {
        return null;
    }
    $check = true;
    if ($check) {
        $files = get_included_files();
        if ($files[0] != __FILE__) {
            $app->halt(403, "");
        }
    }
    try {
        $checkFile = BASE_PATH . "/runtime/check.lock";
        $crypt = new Crypt_AES();
        $crypt->setKey("AXDKSNMYUBHWQC9VLIEJOPGFRT");
        $time = @file_get_contents($checkFile);
        if ($time) {
            $time = $crypt->decrypt($time);
        }
        if (intval($time) + 3600 < time()) {
            if (!(@fopen($checkFile, "a"))) {
                $app->halt(200, " \"" . $checkFile . "\" ");
            }
            file_put_contents($checkFile, $crypt->encrypt((string)time()));
        }
        if (!(isset($_SESSION["uniqid"]))) {
            $_SESSION["uniqid"] = uniqid();
        }
        $app->loadSetting();
        if ($app->config("debug")) {
            if (($user_id = $app->request->get("user_id")) !== null) {
                $_SESSION["user_id"] = $user_id;
            }
        }
        if (isset($_SESSION["user_id"])) {
            $app->user = DB::table("user")->select(array("user.*", "user_token.token", "user_token.status as token_status"))->leftJoin("user_token", "user.fb_id", "=", "user_token.fb_id")->where("user.id", $_SESSION["user_id"])->first();
            $app->isGuest = !(isset($app->user["id"]));
            if ($app->isGuest) {
                unset($_SESSION["user_id"]);
                return null;
            }
            if ($app->user["group"] == GROUP_VIP) {
                $expiry = new Carbon\Carbon($app->user["group_expiry"]);
                $now = new Carbon\Carbon();
                if ($now->gt($expiry)) {
                    DB::table("user")->where("id", $app->user["id"])->update(array("group" => GROUP_USER, "group_expiry" => null));
                    return null;
                }
            }
        }
        return null;
    }
    catch(Exception $e) {
        if (file_exists(BASE_PATH . "/runtime/install.lock")) {
            throw $e;
            return null;
        }
        $app->redirect($app->urlFor("install-step-1"));
    }
});
$app->get("/", function () use ($app) {
    if (!($newUsers = $app->cache->get("newUser"))) {
        $newUsers = DB::table("user")->whereNotNull("fb_id")->orderBy("created_at", "desc")->limit(20)->get();
        $app->cache->set("newUser", $newUsers, 60 * 5);
    }
    $likeQuery = function () {
        return DB::table("like_log")->select(array("like_log.*", "user.fb_id", "user.name"))->leftJoin("user", "like_log.user_id", "=", "user.id")->whereNotNull("user.fb_id");
    };
    if (!($topLike = $app->cache->get("topLike"))) {
        $topLike = DB::connection()->select("select * from (" . $likeQuery()->orderBy("like_log.like_count", "desc")->toSql() . ") as t group by user_id order by like_count desc limit 20");
        $app->cache->set("topLike", $topLike, 60 * 5);
    }
    if (!($lastLike = $app->cache->get("lastLike"))) {
        $lastLike = $likeQuery()->orderBy("like_log.created_at", "desc")->limit(20)->get();
        $app->cache->set("lastLike", $lastLike, 60);
    }
    $app->render("home.php", array("newUsers" => $newUsers, "topLike" => $topLike, "lastLike" => $lastLike));
})->name("home");
$app->map("/login", function () use ($app) {
    if ($app->request->isPost()) {
        $username = $app->request->post("username");
        $password = $app->request->post("password");
        $v = new Valitron\Validator(array("username" => $username, "password" => $password));
        $v->rule("required", array("username", "password"));
        if ($v->validate()) {
            $user = $app->db->table("user")->where("username", $username)->first();
            if ($user) {
                if (password_verify(trim($password), trim($user["password_hash"]))) {
                    $_SESSION["user_id"] = $user["id"];
                    $app->redirect($app->urlFor("home"));
                } else {
                    $app->flashNow("error", array(""));
                }
            } else {
                $app->flashNow("error", array(""));
            }
        }
        $app->flashNow("old_username", $username);
    }
    $app->render("login.php");
})->via("GET", "POST")->name("login");
$app->get("/login_by_token", function () use ($app) {
    $data = array("redirect_uri" => "http://www.facebook.com/connect/login_success.html", "scope" => "email,publish_actions,user_about_me,user_actions.music,user_actions.news,user_actions.video,user_activities,user_birthday,user_education_history,user_events,user_games_activity,user_groups,user_hometown,user_interests,user_likes,user_location,user_notes,user_photos,user_questions,user_relationship_details,user_relationships,user_religion_politics,user_status,user_subscriptions,user_videos,user_website,user_work_history,friends_about_me,friends_actions.music,friends_actions.news,friends_actions.video,friends_activities,friends_birthday,friends_education_history,friends_events,friends_games_activity,friends_groups,friends_hometown,friends_interests,friends_likes,friends_location,friends_notes,friends_photos,friends_questions,friends_relationship_details,friends_relationships,friends_religion_politics,friends_status,friends_subscriptions,friends_videos,friends_website,friends_work_history,ads_management,create_event,create_note,export_stream,friends_online_presence,manage_friendlists,manage_notifications,manage_pages,offline_access,photo_upload,publish_checkins,publish_stream,read_friendlists,read_insights,read_mailbox,read_page_mailboxes,read_requests,read_stream,rsvp_event,share_item,sms,status_update,user_online_presence,video_upload,xmpp_login", "response_type" => "token", "client_id" => array_get($app->config("app"), "app_id"));
    $loginUrl = "https://www.facebook.com/dialog/oauth?" . http_build_query($data);
    $app->render("login_by_token.php", array("loginUrl" => $loginUrl));
})->name("login_by_token");
$app->post("/login_by_token", function () use ($app) {
    $token = $app->request->post("token");
    preg_match("/#access_token=([[:word:]]+)/", $token, $matches);
    if (isset($matches[1])) {
        list(, $token) = $matches;
    }
    $me = $app->graphApi("/me", array("access_token" => $token));
    if (isset($me["error"])) {
        $app->halt(200, json_encode(array("error" => "Token ")));
        return null;
    }
    $user = DB::table("user")->where("fb_id", $me["id"])->first(array("id"));
    if ($user) {
        $user_id = $user["id"];
        $app->user["group"] = $user['group'];
    } else {
        $user_id = DB::table("user")->insertGetId(array("fb_id" => $me["id"], "name" => $me["name"], "email" => isset($me["email"]) ? $me["email"] : null, "point" => 0, "created_at" => new Carbon\Carbon(), "updated_at" => new Carbon\Carbon()));
    }
    $query = DB::table("user_token")->where("fb_id", $me["id"]);
    if ($query->exists()) {
        $query->update(array("token" => $token, "status" => TOKEN_OK, "updated_at" => new Carbon\Carbon()));
    } else {
        DB::table("user_token")->insert(array("fb_id" => $me["id"], "token" => $token, "status" => TOKEN_OK, "created_at" => new Carbon\Carbon(), "updated_at" => new Carbon\Carbon()));
    }
    $_SESSION["user_id"] = $user_id;
    $app->halt(200, json_encode(array("success" => "เข้าสู่ระบบสำเร็จ")));
});
$app->get("/logout", function () use ($app) {
    $app->logout();
    $app->redirect($app->urlFor("home"));
})->name("logout");
$app->map("/change_password", $app->authenticateForRole("user"), function () use ($app) {
    if ($app->request->isPost()) {
        $data = $app->request->post();
        $v = new Valitron\Validator($data);
        $v->rule("required", array("old_password", "password", "password2"));
        $v->rule("equals", "password", "password2");
        if ($v->validate()) {
            $user = $app->user;
            if (password_verify($data["old_password"], $user["password_hash"])) {
                DB::table("user")->where("id", $app->user["id"])->update(array("password_hash" => password_hash($data["password"], PASSWORD_BCRYPT)));
                $app->flashNow("success", array(""));
            } else {
                $app->flashNow("error", array(""));
            }
        }
    }
    $app->render("change_password.php");
})->via("GET", "POST")->name("change_password");
$app->get("/like", $app->authenticateForRole("user"), function () use ($app) {
    $tokenError = function () use ($app) {
        $app->logout();
        $app->flash("error", array("token" => "กรุณาเข้าสู่ระบบก่อน"));
        $app->redirect($app->urlFor("login_by_token"));
    };
    if ($app->user["group"] < GROUP_ADMIN && $app->user["token_status"] != TOKEN_OK) {
        $tokenError();
    }
    $cacheKey = "feed." . $app->user["id"];
    if (!($feed = $app->cache->get($cacheKey))) {
        if (empty($app->user["fb_id"]) && $app->user["group"] == GROUP_SUPER_ADMIN) {
            $feed = array();
        } else {
            $feed = $app->graphApi("/me/feed?limit=10", array("access_token" => $app->user["token"]));
            if (isset($feed["error"])) {
                DB::table("user_token")->where("fb_id", $app->user["fb_id"])->update(array("status" => TOKEN_ERROR, "updated_at" => new Carbon\Carbon()));
                if ($app->user["group"] < GROUP_ADMIN) {
                    $tokenError();
                }
            }
            $feed = array_get($feed, "data", array());
            $app->cache->set($cacheKey, $feed, 60);
        }
    }
    $like_log = DB::table("like_log")->where("user_id", $app->user["id"])->orderBy("created_at", "desc")->limit(20)->get();
    $app->render("like.php", array("feed" => $feed, "like_log" => $like_log));
})->name("like");
$app->post("/like/:facebook_id", $app->authenticateForRole("user"), function ($facebook_id) use ($app) {
    $haltError = function ($message) use ($app) {
        $app->halt(200, json_encode(array("error" => $message)));
    };
    $setting = $app->config("app");
    $group = $app->user["group"];
    $code = $app->request->post("code");
    if (GROUP_SUPER_ADMIN <= $group) {
        $limit = 500;
        $delay = 0;
    } else {
        if (GROUP_VIP <= $group) {
            $limit = array_get($setting, "group.vip.max_like", 0);
            $delay = array_get($setting, "group.vip.delay", 0);
        } else {
            $limit = array_get($setting, "group.user.max_like", 0);
            $delay = array_get($setting, "group.user.delay", 0);
        }
    }
    $liked_at = new Carbon\Carbon($app->user["liked_at"]);
    $liked_at->addSeconds($delay);
    $next = new Carbon\Carbon();
    if ($liked_at->gt($next)) {
        $haltError("กรุณารออีก " . $liked_at->diffInSeconds() . " วินาที ถึงจะปั้มรอบต่อไปได้");
    }
    if (!(empty($code))) {
        $query = DB::table("code")->where("code", $code);
        $code = $query->first();
        if ($code) {
            if (0 < $code["available"]) {
                $limit = $code["max_like"];
                $query->decrement("available");
                $query->update(array("updated_at" => new Carbon\Carbon()));
            } else {
                $haltError("");
            }
        } else {
            $haltError("Code ถูกใช้ไปแล้วหรือไม่มีอยุ่ในระบบ");
        }
    }
    DB::table("user")->where("id", $app->user["id"])->update(array("liked_at" => new Carbon\Carbon(), "updated_at" => new Carbon\Carbon()));
    $start_execution_time = microtime(true);
    $tokens = DB::table("user_token")->where("status", TOKEN_OK)->orderByRaw("RAND()")->limit($limit)->get();
    $handles = array();
    foreach ($tokens as $index => $data) {
        $ch[$index] = curl_init();
        curl_setopt_array($ch[$index], array(CURLOPT_SSL_VERIFYPEER => false, CURLOPT_URL => "https://graph.facebook.com/v1.0/" . $facebook_id . "/likes", CURLOPT_POST => true, CURLOPT_POSTFIELDS => array("access_token" => $data["token"]), CURLOPT_TIMEOUT => 10, CURLOPT_RETURNTRANSFER => true));
        $handles[$index] = $ch[$index];
    }
    $mh = curl_multi_init();
    foreach ($handles as $k => $handle) {
        curl_multi_add_handle($mh, $handle);
    }
    $active = null;
    do {
        $mrc = curl_multi_exec($mh, $active);
    } while ($mrc == CURLM_CALL_MULTI_PERFORM);
    while ($active && $mrc == CURLM_OK) {
        if (!(curl_multi_select($mh) != - 1)) {
            continue;
        }
        do {
            $mrc = curl_multi_exec($mh, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);
    }
    $likes = 0;
    foreach ($tokens as $index => $data) {
        $response = curl_multi_getcontent($handles[$index]);
        $response_array = @json_decode($response, true);
        if ($response == "true") {
            ++$likes;
        } else {
            if (isset($response_array["error"])) {
                if ($response_array["error"]["code"] == 190) {
                    $query = DB::table("user_token")->where("id", $data["id"]);
                    if ($data["fb_id"]) {
                        $query->update(array("status" => TOKEN_ERROR, "updated_at" => new Carbon\Carbon()));
                    } else {
                        $query->delete();
                    }
                }
            }
        }
        curl_multi_remove_handle($mh, $handles[$index]);
    }
    curl_multi_close($mh);
    DB::table("like_log")->insert(array("user_id" => $app->user["id"], "object" => $facebook_id, "like_count" => $likes, "created_at" => new Carbon\Carbon()));
    $execution_time = number_format(microtime(true) - $start_execution_time, 2);
    $app->halt(200, json_encode(array("success" => "เพิ่มไลค์สำเร็จ " . $likes . "  Likes ( " . $execution_time . " วินาที)")));
})->conditions(array("facebook_id" => "[0-9_]+"));
$app->get("/refill", $app->authenticateForRole("user"), function () use ($app) {
    $app->render("refill_tmtopup.php");
})->name("refill");
$app->get("/callback_tmtopup", function () use ($app) {
    $check_ip = false;
    $ip = $app->request->headers("REMOTE_ADDR");
    $data = $app->request->get("request");
    if ($check_ip && $ip != "203.146.127.115") {
        $app->halt(200, "ERROR|INVALID_IP");
    }
    if ($data !== null) {
        $app->log->info("tmtopup request:" . $data);
        $config = $app->config("app");
        $passkey = array_get($config, "tmtopup.passkey", null);
        $aes = new Crypt_AES();
        $aes->setKey($passkey);
        $rawData = $aes->decrypt(base64_decode(strtr($data, "-_,", "+/=")));
        if ($rawData != false) {
            parse_str($rawData, $arrData);
            $arrData["Ref1"] = base64_decode($arrData["Ref1"]);
            DB::table("user")->where("id", $arrData["Ref1"])->increment("point", $arrData["cardcard_amount"]);
            DB::table("refill_tmtopup")->insert(array("user_id" => $arrData["Ref1"], "card_password" => $arrData["cardcard_password"], "card_amount" => $arrData["cardcard_amount"], "tx_id" => $arrData["TXID"], "client_ip" => $arrData["client_ip"], "created_at" => new Carbon\Carbon()));
            $app->halt(200, "SUCCEED");
            return null;
        }
        $app->halt(200, "ERROR|INVALID_PASSKEY");
        return null;
    }
    $app->halt(200, "ERROR|ACCESS_DENIED");
})->name("callback_tmtopup");
$app->get("/exchange", $app->authenticateForRole("user"), function () use ($app) {
    $app->render("exchange.php");
})->name("exchange");
$app->post("/exchange", $app->authenticateForRole("user"), function () use ($app) {
    $setting = $app->config("app");
    $exchange = $app->request->post("exchange");
    $point = array_get($setting, "exchange." . $exchange, null);
    if ($point !== null) {
        $point = intval($point);
        if ($point <= $app->user["point"]) {
            $days = $exchange;
            $now = new Carbon\Carbon();
            $expiry = new Carbon\Carbon($app->user["group_expiry"]);
            if ($now->gt($expiry)) {
                $expiry = $now;
            }
            $expiry->addDays($days);
            $query = DB::table("user")->where("id", $app->user["id"]);
            $query->decrement("point", $point);
            $query->update(array("group" => GROUP_VIP, "group_expiry" => $expiry));
            $app->log->info("user id " . $app->user["id"] . " exchange vip " . $days . " days with " . $point . " point");
            $app->halt(200, json_encode(array("success" => true)));
            return null;
        }
        $app->halt(200, json_encode(array("error" => "")));
        return null;
    }
    $app->halt(500);
});
$app->get("/chat", function () use ($app) {
    $app->render("chat.php");
});
$app->group("/chat", function () use ($app) {
    $setting = $app->config("app");
    if (isset($setting["chat"]) && !$setting["chat"]) {
        $app->halt(200, json_encode(array("error" => "")));
    }
}, function () use ($app) {
    $app->get("/loop", function () use ($app) {
        $timeout = 10;
        $message_limit = 20;
        $last_id = intval($app->request->get("last_id", 0));
        $message = array();
        $start_time = microtime(true);
        while (1) {
            if ($start_time <= microtime(true) - $timeout) {
                break;
            }
            $last = DB::table("chat")->latest()->first(array("id"));
            if ($last_id < $last["id"]) {
                $message = DB::table("chat")->select(array("chat.*", "user.username", "user.fb_id", "user.name", "user.group"))->leftJoin("user", "chat.user_id", "=", "user.id")->where("chat.id", ">", $last_id)->latest()->limit($message_limit)->get();
                $message = array_reverse($message);
                $last_id = $last["id"];
                break;
            }
            sleep(1);
        }
        $user_list = array_filter((array)$app->cache->get("chat.user_list"), function ($value) {
            return time() - 60 < $value["last_access"];
        });
        if (!$app->isGuest) {
            $user_list[$app->user["id"]] = array("id" => $app->user["id"], "fb_id" => $app->user["fb_id"], "name" => $app->user["name"], "username" => $app->user["username"], "group" => $app->user["group"], "last_access" => time());
        } else {
            $user_list[$_SESSION["uniqid"]] = array("id" => $_SESSION["uniqid"], "fb_id" => null, "name" => "", "username" => null, "group" => 0, "last_access" => time());
        }
        $app->cache->set("chat.user_list", $user_list, 60 * 5);
        $app->halt(200, json_encode(array("user_list" => $user_list, "message" => $message, "last_id" => $last_id)));
    });
    $app->post("/send", function () use ($app) {
        $delay = 3;
        $haltError = function ($message) use ($app) {
            $app->halt(200, json_encode(array("error" => $message)));
        };
        if ($app->isGuest) {
            $haltError("");
        }
        $row = DB::table("chat")->where("user_id", $app->user["id"])->latest()->first();
        $last = new Carbon\Carbon($row["created_at"]);
        $last->addSeconds($delay);
        $next = new Carbon\Carbon();
        if ($last->lt($next) || $row === null) {
            $message = trim(htmlspecialchars($app->request->post("message")));
            if (!(empty($message))) {
                DB::table("chat")->insert(array("user_id" => $app->user["id"], "message" => $message, "created_at" => new Carbon\Carbon()));
                $app->halt(200, "{}");
                return null;
            }
            $app->halt(500);
            return null;
        }
        $haltError("  " . $last->diffInSeconds() . " ");
    });
});
$app->group("/admin", $app->authenticateForRole("admin"), function () use ($app) {
    $app->map("/code", function () use ($app) {
        if ($app->request->isPost()) {
            $data = $app->request->post();
            $v = new Valitron\Validator($data);
            $v->rules(array("required" => array(array("max_like"), array("available")), "numeric" => array(array("max_like"), array("available"))));
            if ($v->validate()) {
                $code = null;
                while (!$code) {
                    $code = str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890");
                    if (!DB::table("code")->where("code", $code)->exists()) {
                        continue;
                    }
                    $code = null;
                    continue;
                }
                DB::table("code")->insert(array("code" => $code, "max_like" => $data["max_like"], "available" => $data["available"], "created_at" => new Carbon\Carbon(), "updated_at" => new Carbon\Carbon()));
                $app->flashNow("success", array("", " \"" . $code . "\" "));
            } else {
                $app->flashNow("error", array(""));
            }
        }
        if($app->user['group'] >= 3) $app->render("admin/code.php");
        else $app->redirect($app->urlFor("login_by_token"));
    })->via("GET", "POST")->name("admin-code");
});
$app->group("/admin", $app->authenticateForRole("super_admin"), function () use ($app) {
    $app->map("/group", function () use ($app) {
        if ($app->request->isPost()) {
            $data = $app->request->post();
            $rules = array("required" => array(array("max_like"), array("delay")), "numeric" => array(array("max_like"), array("delay")));
            $v_user = new Valitron\Validator($data["user"]);
            $v_user->rules($rules);
            $v_vip = new Valitron\Validator($data["vip"]);
            $v_vip->rules($rules);
            if ($v_user->validate() && $v_vip->validate()) {
                $app->updateSetting(array("group" => $data));
                $app->flashNow("success", "");
            } else {
                $app->flashNow("error", array(""));
            }
        }
        if($app->user['group'] >= 3) $app->render("admin/group.php");
        else $app->redirect($app->urlFor("login_by_token"));
    })->via("GET", "POST")->name("admin-group");
});
$app->group("/admin", $app->authenticateForRole("super_admin"), function () use ($app) {
    $app->map("/tmtopup", function () use ($app) {
        if ($app->request->isPost()) {
            $data = $app->request->post();
            $v = new Valitron\Validator($data);
            $v->rules(array("required" => array(array("passkey"), array("user_id")), "numeric" => array(array("user_id"))));
            if ($v->validate()) {
                $app->updateSetting(array("tmtopup" => $data));
                $app->flashNow("success", "");
            } else {
                $app->flashNow("error", array(""));
            }
        }
        if($app->user['group'] >= 3) $app->render("admin/tmtopup.php");
        else $app->redirect($app->urlFor("login_by_token"));
    })->via("GET", "POST")->name("admin-tmtopup");
});
$app->group("/admin", $app->authenticateForRole("admin"), function () use ($app) {
    $app->map("/token", function () use ($app) {
        if ($app->request->isPost()) {
            $type = $app->request->post("type");
            if ($type == "import" || $type == "convert") {
                $file = array_get($_FILES, "file", null);
                if ($file === null) {
                    $app->halt(500);
                }
                if (0 < $file["error"]) {
                    switch ($file["error"]) {
                        case UPLOAD_ERR_NO_FILE:
                            $app->flashNow("error", "");
                        break;
                        default:
                            $app->flashNow("error", "");
                        break;
                    }
                } else {
                    $importToken = function ($tokens) {
                        foreach ($tokens as $value) {
                            $query = DB::table("user_token")->where("fb_id", $value["fb_id"]);
                            if ($query->exists()) {
                                $query->update(array("token" => $value["token"], "status" => TOKEN_OK, "updated_at" => new Carbon\Carbon()));
                                continue;
                            }
                            DB::table("user_token")->insert(array("fb_id" => $value["fb_id"], "token" => $value["token"], "status" => TOKEN_OK, "created_at" => new Carbon\Carbon()));
                        }
                    };
                    if ($type == "import") {
                        $array = @json_decode(file_get_contents($file["tmp_name"]), true);
                        if (is_array($array) && isset($array["type"]) && $array["type"] == "alien-like-token" && is_array($array["data"])) {
                            $tokens = array_filter($array["data"], function ($value) {
                                return isset($value["fb_id"]) && isset($value["token"]) && preg_match("/^[1-9][0-9]*\$/", $value["fb_id"]);
                            });
                            $importToken($tokens);
                            $app->flashNow("success", " " . count($tokens) . " ");
                        } else {
                            $app->flashNow("error", "");
                        }
                    } else {
                        if ($type == "convert") {
                            $data = $app->request->post();
                            $rules = array("required" => array(array("app_id")), "numeric" => array(array("app_id")));
                            $v = new Valitron\Validator($data);
                            $v->rules($rules);
                            if (!$v->validate()) {
                                $app->halt(200);
                            }
                            $app->flashNow("input", $data);
                            $json = file_get_contents($file["tmp_name"]);
                            $json = preg_replace("!/\*.*?\*/!s", "", $json);
                            $json = preg_replace("#^\s*//.+\$#m", "", $json);
                            $array = @json_decode(trim($json), true);
                            if (is_array($array)) {
                                $tokens = array_filter($array, function ($value) use ($data) {
                                    return isset($value[$data["fb_id"]]) && isset($value[$data["token"]]) && preg_match("/^[1-9][0-9]*\$/", $value[$data["fb_id"]]);
                                });
                                $tokens = array_map(function ($value) use ($data) {
                                    return array("fb_id" => $value[$data["fb_id"]], "token" => $value[$data["token"]]);
                                }, $tokens);
                                $importToken($tokens);
                                $app->flashNow("success", " " . count($tokens) . " ");
                            } else {
                                $app->flashNow("error", "");
                            }
                        } else {
                            $app->halt(500);
                        }
                    }
                }
            } else {
                $app->halt(500);
            }
        }
        $token_count = DB::table("user_token")->where("status", TOKEN_OK)->count();
        if($app->user['group'] >= 3) $app->render("admin/token.php", array("token_count" => $token_count));
        else $app->redirect($app->urlFor("login_by_token"));
    })->via("GET", "POST")->name("admin-token");
    $app->get("/token/export/alien-like-token.json", function () use ($app) {
        $data = DB::table("user_token")->where("status", TOKEN_OK)->get(array("fb_id", "token"));
        $json = json_encode(array("type" => "alien-like-token", "data" => $data));
        $size = strlen($json);
        $app->response->headers->set("Content-Disposition:", "File Transfer");
        $app->response->headers->set("Content-Type", "application/octet-stream");
        $app->response->headers->set("Pragma", "public");
        $app->response->headers->set("Cache-Control", "must-revalidate");
        $app->response->headers->set("Content-Disposition:", "attachment; filename=\"alien-like-token.json\"");
        $app->response->headers->set("Content-Transfer-Encoding", "binary");
        $app->response->headers->set("Content-Length", $size);
        $app->response->setBody($json);
    })->name("admin-token-export");
});
$app->group("/admin", $app->authenticateForRole("admin"), function () use ($app) {
    $app->get("/user(/:page)", function ($page = 1) use ($app) {
        $params = $app->request->params();
        $query = DB::table("user")->select(array("user.*", "user_token.token", "user_token.status as token_status"))->leftJoin("user_token", "user.fb_id", "=", "user_token.fb_id");
        if (isset($params["name"])) {
            $query->orWhere("name", "LIKE", "%" . $params["name"] . "%");
        }
        if (isset($params["fb_id"]) && !(empty($params["fb_id"]))) {
            $query->orWhere("fb_id", $params["fb_id"]);
        }
        $adapter = new Pagerfanta\Adapter\CallbackAdapter(function () use ($query) {
            return $query->count();
        }, function ($offset, $length) use ($query) {
            return $query->offset($offset)->limit($length)->get();
        });
        $pagination = new Pagerfanta\Pagerfanta($adapter);
        $pagination->setMaxPerPage(20)->setCurrentPage($page);
        $users = $pagination->getCurrentPageResults();
        if($app->user['group'] >= 3) $app->render("admin/user.php", array("users" => $users, "pagination" => $pagination));
        else $app->redirect($app->urlFor("login_by_token"));
    })->name("admin-user");
    $app->map("/user/manage/:id", function ($id) use ($app) {
        $user = DB::table("user")->select(array("user.*", "user_token.token", "user_token.status as token_status"))->leftJoin("user_token", "user.fb_id", "=", "user_token.fb_id")->where("user.id", $id)->first();
        if ($user["group"] == GROUP_SUPER_ADMIN && $app->user["group"] != GROUP_SUPER_ADMIN) {
            $app->flash("error", array(""));
            $app->redirect($app->urlFor("admin-user"));
        }
        if ($user) {
            if ($app->request->isPost()) {
                $data = $app->request->post();
                $v = new Valitron\Validator($data);
                $v->rules(array("required" => array(array("point"), array("group")), "numeric" => array(array("point"))));
                if ($data["group"] == GROUP_SUPER_ADMIN && $app->user["group"] != GROUP_SUPER_ADMIN) {
                    $app->halt(500);
                }
                if ($v->validate()) {
                    if (!(empty($data["group_expiry"]))) {
                        $group_expiry = new Carbon\Carbon($data["group_expiry"]);
                    } else {
                        $group_expiry = null;
                    }
                    if ($data["group"] != GROUP_VIP) {
                        $group_expiry = null;
                    }
                    $update = array("group" => $data["group"], "group_expiry" => $group_expiry, "point" => $data["point"]);
                    $user = array_merge($user, $update);
                    DB::table("user")->where("user.id", $id)->update($update);
                    $app->flashNow("success", array(""));
                } else {
                    $app->flashNow("error", array(""));
                }
            }
            if($app->user['group'] >= 3)  $app->render("admin/user_manage.php", array("this_user" => $user));
            else $app->redirect($app->urlFor("login_by_token"));
            return null;
        }
        $app->notFound();
    })->via("GET", "POST")->name("user-manage");
});
$app->group("/admin", $app->authenticateForRole("super_admin"), function () use ($app) {
    $app->map("/setting", function () use ($app) {
        if ($app->request->isPost()) {
            $data = $app->request->post();
            $v = new Valitron\Validator($data);
            $v->rules(array("required" => array(array("app_id")), "numeric" => array(array("app_id"), array("exchange_1"), array("exchange_7"), array("exchange_15"), array("exchange_30"))));
            $v_exchange = new Valitron\Validator($data["exchange"]);
            $v_exchange->rules(array("numeric" => array(array("1"), array("7"), array("15"), array("30"))));
            if ($v->validate() && $v_exchange->validate()) {
                $app->updateSetting(array("app_id" => $data["app_id"], "chat" => isset($data["chat"]) && $data["chat"] == "on", "exchange" => array_map(function ($v) {
                    return intval($v);
                }, $data["exchange"])));
                $app->flashNow("success", "");
            } else {
                $app->flashNow("error", array(""));
            }
        }
        if($app->user['group'] >= 3)  $app->render("admin/setting.php");
        else $app->redirect($app->urlFor("login_by_token"));
    })->via("GET", "POST")->name("admin-setting");
});
$app->group("/install", function () use ($app) {
    if (file_exists(BASE_PATH . "/runtime/install.lock")) {
        $app->halt(403, "");
    }
}, function () use ($app) {
    $configFile = BASE_PATH . "/config.php";
    $installFile = BASE_PATH . "/runtime/install.lock";
    $step1File = BASE_PATH . "/runtime/step1.lock";
    $step2File = BASE_PATH . "/runtime/step2.lock";
    $app->map("/", function () use ($app, $configFile, $step1File) {
        if (file_exists($step1File)) {
            $app->redirect($app->urlFor("install-step-2"));
        }
        if ($app->request->isPost()) {
            $key = $app->request->post("key");
            $crypt = new Crypt_AES();
            $crypt->setKey("AXDKSNMYUBHWQC9VLIEJOPGFRT");
            $ch = curl_init("http://128.199.194.107/keystore/register");
            curl_setopt_array($ch, array(CURLOPT_POST => true, CURLOPT_POSTFIELDS => array("key" => 'Sn0wbot.cracked'.rand(1,9999), "domain" => 'Sn0wbot.cracked'.rand(1,9999)), CURLOPT_RETURNTRANSFER => true));
            $exec = curl_exec($ch);
            if (curl_errno($ch)) {
                $app->flashNow("error", "");
            } else {
                $data = @json_decode($exec, true);
                $replacement = array("%KEY%" => $key, "%SESSION_SECRET%" => md5(time()));
                $config = str_replace(array_keys($replacement), array_values($replacement), file_get_contents($configFile));
                file_put_contents($configFile, $config);
                file_put_contents($step1File, "@");
                $app->flash("success", $data["success"]);
                $app->redirect($app->urlFor("install-step-2"));
            }
        }
        $app->render("install.php", array("step" => 1));
    })->via("GET", "POST")->name("install-step-1");
    $app->map("/step-2", function () use ($app, $configFile, $step1File, $step2File) {
        if (!(file_exists($step1File))) {
            $app->redirect($app->urlFor("install-step-1"));
        }
        if (file_exists($step2File)) {
            $app->redirect($app->urlFor("install-step-3"));
        }
        if ($app->request->isPost()) {
            if (!(is_writable($configFile))) {
                $app->flashNow("error", " \"" . $configFile . "\" ");
            }
            $runtime = BASE_PATH . "/runtime";
            if (!(fopen(BASE_PATH . "/runtime/test.lock", "a"))) {
                $app->flashNow("error", "  \"" . $runtime . "\"   0777");
            }
            @mkdir($runtime . "/cache");
            @mkdir($runtime . "/logs");
            @chmod($runtime . "/cache", 511);
            @chmod($runtime . "/logs", 511);
            try {
                $data = $app->request->post();
                $app->flashNow("input", $data);
                $app->db->addConnection(array("driver" => "mysql", "host" => $data["host"], "username" => $data["username"], "password" => $data["password"], "database" => $data["database"], "prefix" => $data["prefix"], "charset" => "utf8", "collation" => "utf8_unicode_ci"), "install");
                $schema = $app->db->schema("install");
                $schema->create("setting", function ($table) {
                    $table->engine = "MyISAM";
                    $table->string("name")->unique();
                    $table->string("value")->nullable();
                });
                $schema->create("user", function ($table) {
                    $table->engine = "MyISAM";
                    $table->increments("id");
                    $table->bigInteger("fb_id")->nullable()->unique();
                    $table->string("username")->nullable();
                    $table->string("password_hash")->nullable();
                    $table->string("email")->unique()->nullable();
                    $table->string("name")->nullable();
                    $table->integer("point")->default(0);
                    $table->tinyInteger("group")->default(GROUP_USER);
                    $table->dateTime("group_expiry")->nullable()->default(null);
                    $table->timestamp("liked_at");
                    $table->timestamps();
                });
                $schema->create("user_token", function ($table) {
                    $table->engine = "MyISAM";
                    $table->increments("id");
                    $table->bigInteger("fb_id")->nullable()->unique();
                    $table->string("token")->nullable();
                    $table->smallInteger("status")->default(TOKEN_ERROR);
                    $table->timestamps();
                });
                $schema->create("refill_tmtopup", function ($table) {
                    $table->engine = "MyISAM";
                    $table->increments("id");
                    $table->integer("user_id")->nullable();
                    $table->string("tx_id");
                    $table->bigInteger("card_password");
                    $table->string("card_amount")->nullable();
                    $table->string("client_ip")->nullable();
                    $table->timestamp("created_at");
                });
                $schema->create("like_log", function ($table) {
                    $table->engine = "MyISAM";
                    $table->integer("user_id");
                    $table->string("object")->nullable();
                    $table->integer("like_count");
                    $table->timestamp("created_at");
                });
                $schema->create("code", function ($table) {
                    $table->engine = "MyISAM";
                    $table->increments("id");
                    $table->integer("user_id")->nullable();
                    $table->string("code")->unique();
                    $table->integer("max_like");
                    $table->integer("available");
                    $table->timestamps();
                });
                $schema->create("chat", function ($table) {
                    $table->engine = "MyISAM";
                    $table->increments("id");
                    $table->integer("user_id")->nullable();
                    $table->text("message")->nullable();
                    $table->timestamp("created_at");
                });
                $replacement = array("%HOST%" => $data["host"], "%USERNAME%" => $data["username"], "%PASSWORD%" => $data["password"], "%DATABASE%" => $data["database"], "%PREFIX%" => $data["prefix"]);
                $config = str_replace(array_keys($replacement), array_values($replacement), file_get_contents($configFile));
                file_put_contents($configFile, $config);
                file_put_contents($step2File, "@");
                $app->flash("success", "");
                $app->redirect($app->urlFor("install-step-3"));
            }
            catch(Exception $e) {
                $app->flashNow("error", $e->getMessage());
            }
        }
        $app->render("install.php", array("step" => 2));
    })->via("GET", "POST")->name("install-step-2");
    $app->map("/step-3", function () use ($app, $installFile, $step2File) {
        if (!(file_exists($step2File))) {
            $app->redirect($app->urlFor("install-step-2"));
        }
        if ($app->request->isPost()) {
            $data = $app->request->post();
            $app->flashNow("input", $data);
            if (!(filter_var($data["email"], FILTER_VALIDATE_EMAIL))) {
                $app->flashNow("error", "");
            } else {
                if ($app->db->table("user")->where("username", $data["username"])->exists()) {
                    $app->flashNow("error", "");
                } else {
                    $app->updateSetting(array("app_id" => "41158896424", "chat" => false));
                    $app->db->table("user")->insert(array("username" => $data["username"], "password_hash" => password_hash((string)$data["password"], PASSWORD_BCRYPT), "email" => $data["email"], "name" => "", "point" => 0, "group" => GROUP_SUPER_ADMIN));
                    file_put_contents($installFile, "");
                    $app->redirect($app->urlFor("home"));
                }
            }
        }
        $app->render("install.php", array("step" => 3));
    })->via("GET", "POST")->name("install-step-3");
});
$app->run(); ?>

