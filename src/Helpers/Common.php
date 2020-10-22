<?php

if (!function_exists("remove_double_slash")) {
    function remove_double_slash($path){
        return preg_replace('/([^:])(\/{2,})/', '$1/', $path);
    }
}