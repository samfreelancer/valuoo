<?php
class Database{
    
    private $database = 'db_valuoo';
    private $host = 'localhost';
    private $user = 'root';
    private $pass = '';
    private $port = '3306';
    private $adapter = '';
    
    public function __construct(){
                
         //mysqli_connect(, $this->database);
        $this->adapter = mysql_connect($this->host, $this->user, $this->pass);
        if(!$this->adapter){
            
            die("Could not connect: ".mysql_error());
        }else{
            mysql_select_db($this->database, $this->adapter);
        }
        return $this->adapter;
    }
    
    public function query($query){
        
      $res = mysql_query($query, $this->adapter);
      
      if(mysql_num_rows($res)){
              $arr = array();
          while($row = mysql_fetch_assoc($res)){
              
              $arr[] = $row;
          }
          return $arr;
      }else{
          return 0;
      }
        
    }
    
    public function insert($query){
        echo $query."<br />";
        if(mysql_query($query, $this->adapter)){
            
            return mysql_insert_id($this->adapter);
        }
        //return 0;
        return mysql_error();
        
    }
    
}