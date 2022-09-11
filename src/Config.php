<?php

namespace Lib;

class Config
{
    public $config;

    public function __construct(){
        $this->config = parse_ini_file('../config/config.ini',true);
    }

    public function get($key){
        list($section,$key) = explode('.',$key);
        return $this->config[$section][$key] ?? $this->config[$section][$key]  ;
    }
}