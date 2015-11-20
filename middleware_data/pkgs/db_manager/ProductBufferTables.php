<?php

require_once ("connection.php");
require_once ("HandleOperationsException.php");
require_once ("Logger.php");

class ProductBufferTables{
     private $logger=null;
        
    public function __construct(){
        $this->logger = new Logger();
    }
    
    
    public function insertProduct( $reference, $attivo, $categoria, $prezzo, $supplier, $manufacture, $qta, $qta_min, $lunghezza, $altezza, $larghezza, $colore, $quantita, $taglia, $nome, $modello, $linea, $codice_colore, $codice_taglia, $url, $immagine){
         $connection = connectionServer();
            $sql ="INSERT INTO ps_buffer_product (reference, attivo, categoria, prezzo, supplier,                manufacture, qta, qta_min, lunghezza, altezza,larghezza,colore,quantita,taglia, nome, modello, linea, codice_colore, codice_taglia, url, immagine )VALUES('".$reference."','".$attivo."',            '".$categoria."','".$prezzo."','".$supplier."','".$manufacture."','".$qta."','".$qta_min."',
 '".$lunghezza."','".$altezza."','".$larghezza."','".$colore."','".$quantita."','".$taglia."','".$nome."','".$modello."','".$linea."','".$codice_colore."','".$codice_taglia."','".$url."','".$immagine."')";
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
    
      public function freeBufferTable(){
         $connection = connectionServer();
            $sql = "TRUNCATE TABLE ps_buffer_product";
            try{
                $res = mysql_query($sql,$connection);
            }catch(Exception $e){
                echo $e."";
            }
            if($res){
                $this->logger->postMessage("La tabella buffer è stata svuotata");
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