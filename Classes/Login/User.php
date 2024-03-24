<?php
namespace Login;
class User extends \Database\Base {
    public static function TableName() { return "users"; }

    public static function isLoggedIn($level = 1) {
        return self::validateCookie($level);
    }
    
    public static function login($user,$pass) {
        try {
            $userClass = self::LoadBy(array("username"=>$user,"pass"=>md5($pass),"active"=>1))[0];
            self::setCookie(base64_encode($user.':'.md5($pass)));
            return true;
        } catch (\Exceptions\ItemNotFound $e) {
            return false;
        }
    }
    
    public static function setCookie($data) {
        \setcookie("ScumsCyborg", $data);
        self::validateCookie();
    }
    
    public static function validateCookie() {
        $cookie = explode(':',base64_decode($_COOKIE['ScumsCyborg']));
        try {
            $userClass = self::LoadBy(array("username"=>$cookie[0],"pass"=>$cookie[1],"active"=>1))[0];
            return true;
        } catch (\Exceptions\ItemNotFound $e) {
            self::logout();
            return false;
        }  

    }
    
    public static function logout() {
        if (isset($_COOKIE['ScumsCyborg'])) {
            unset($_COOKIE['ScumsCyborg']);
            setcookie('ScumsCyborg', '', time() - 3600, '/'); // empty value and old timestamp
        }
    }
    
    public function active() {
        if ($this->active == 1) return '<img class="icon" src="/img/yes.png" />';
        else return '<img class="icon" src="/img/none.png" />';
    }

    public static function getCurrent() {
        if (!self::validateCookie()) {
            return false;
        }
        $cookie = explode(":",base64_decode($_COOKIE['ScumsCyborg']));
        try {
            return self::LoadBy(array("username"=>$cookie[0]))[0];
        } catch (\Exceptions\ItemNotFound $e) {
            return false;
        }
    }
}
