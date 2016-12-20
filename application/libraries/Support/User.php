<?php

namespace Lib\Support;


class User
{
    public static function checkEmail($email)
    {
        return (bool) preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $email);
    }

    public static function checkPwd($passStr)
    {
        return (bool) preg_match("/^[a-zA-Z]\w{5,17}$/", $passStr);
    }


} 