<?php

require_once ("connection.php");
require_once ("HandleOperationsException.php");
require_once ("Logger.php");

class ProductUpdate{
     private $logger=null;
        
    public function __construct(){
        $this->logger = new Logger();
    }
    
    public function productExists($reference){
            $connection = connectionServer();
            $sql = "SELECT ps_id FROM  ps_tmp_products WHERE ( reference = '".$reference."')";
            $result = mysql_query($sql,$connection);
            if (mysql_num_rows($result) > 0){
                $toReturn = null;
                while($row = mysql_fetch_array( $result )){
                    $toReturn =$row[0]; 
                }
                closeConnectionServer($connection);
                return $toReturn;    
            } else {
                closeConnectionServer($connection);
                return FALSE;
            }
    }
    
    public function insertProduct($ps_id,$reference){
         $connection = connectionServer();
            $sql = "INSERT INTO ps_tmp_products ( ps_id, reference)
                VALUES('".$ps_id."','".$reference."')";
            try{
                $res = mysql_query($sql,$connection);
            }catch(Exception $e){
                echo $e."";
            }
            if($res){
                $this->logger->postMessage("Il prodotto $ps_id e' stato inserito correttamente ");
            }else{
                 $errno = mysql_errno($connection);
                 $error = mysql_error($connection);
                 echo "sono qua  $error ; $errno<br/>";
                 switch ($errno) {
                     case 1062:
                        throw new HandleOperationsException($error);
                     break;
                     default:
                     throw new HandleOperationsException($error);
                     break;
                 }
             }
            closeConnectionServer($connection);
    }
}

?>