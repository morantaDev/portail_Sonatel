<?php 
if (!function_exists('flash')) {
    function flash($name = '', $msg = '', $cate = 'green') {
        if (!empty($name)) {
            if (!empty($msg) && empty($_SESSION[$name])) {
                $_SESSION[$name] = $name;
                $_SESSION[$name . "_msg"] = $msg;
                $_SESSION[$name . "_cate"] = $cate;
            } elseif (empty($msg) && !empty($_SESSION[$name])) {
                echo "<h5 id='flash-message' style='padding:30px;color:white;background-color:{$_SESSION[$name."_cate"]}' >{$_SESSION[$name."_msg"]}</h5>";
                unset($_SESSION[$name]);
                unset($_SESSION[$name . "_msg"]);
                unset($_SESSION[$name . "_cate"]);
            }
        }
    }
}

?>

