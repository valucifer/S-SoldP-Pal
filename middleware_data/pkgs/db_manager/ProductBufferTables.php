<?php
require_once("settings.php");
require_once ("connection.php");
require_once (MD_LIBS_DIR."/HandleOperationsException.php");
require_once (MD_LIBS_DIR."/Logger.php");

    /**
    * PHP class update the buffer custom table use to verify the changes between
    * the last import and the new import of product informations
    * @package    UpdateProduct
    * @author     Carlos Borges (carboma89@gmail.com)
    **/

class ProductBufferTables{
     private $logger=null;
        
    public function __construct(){
        $this->logger = new Logger();
    }
    
        /** 
        *Inserts new product into buffer table
        *@params  string $reference, string $attivo, string $categoria, string $prezzo, string $supplier, string $manufacture, string $qta, string $qta_min, string $lunghezza, string $altezza, string $larghezza, string $colore, string $quantita, string $taglia, string $nome, string $modello, string $linea, string $codice_colore, string $codice_taglia, string $url, string $immagine
        */
    public function insertProduct( $reference, $attivo, $categoria, $prezzo, $supplier, $manufacture, $qta, $qta_min, $lunghezza, $altezza, $larghezza, $colore, $quantita, $taglia, $nome, $modello, $linea, $codice_colore, $codice_taglia, $url, $immagine){
         $connection = connectionServer();
            $sql ="INSERT INTO ps_buffer_product (reference, attivo, categoria, prezzo, supplier,                manufacture, qta, qta_min, lunghezza, altezza,larghezza,colore,quantita,taglia, nome, modello, linea, codice_colore, codice_taglia, url, immagine )VALUES('".$reference."','".$attivo."',            '".$categoria."','".$prezzo."','".$supplier."','".$manufacture."','".$qta."','".$qta_min."',
 '".$lunghezza."','".$altezza."','".$larghezza."','".$colore."','".$quantita."','".$taglia."','".$nome."','".$modello."','".$linea."','".$codice_colore."','".$codice_taglia."','".$url."','".$immagine."')";
                $res = mysql_query($sql,$connection);
            if($res){
                $this->logger->postMessage("Product with reference $reference has been added successfully.");
            }else{
                 $errno = mysql_errno($connection);
                 $error = mysql_error($connection);
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
    
    /**
    *Truncate buffer table
    **/
      public function freeBufferTable(){
         $connection = connectionServer();
            $sql = "TRUNCATE TABLE ps_buffer_product";
                $res = mysql_query($sql,$connection);
            if($res){
            }else{
                 $errno = mysql_errno($connection);
                 $error = mysql_error($connection);
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