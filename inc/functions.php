<?php
class Functions{
    
    static public function escape_string($str = ''){
        
        if(!empty($str)){
           return htmlspecialchars(addslashes($str));
        }
        return;
    }
    
    static public function decode_string($str = ''){
        
        if(!empty($str)){
            return stripslashes(htmlspecialchars_decode($str));
        }
    }
    
    static public function escape_mysql($str = ''){
        if(!empty($str)){
            return mysql_real_escape_string(($str));
        } 
    }
}