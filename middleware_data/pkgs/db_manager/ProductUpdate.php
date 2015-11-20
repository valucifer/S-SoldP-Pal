<?php

/**
* PHP class update the tmp custom table use to verify the change between
* the last import import of a product 
* @package    UpdateProduct
* @author     Carlos Borges (carboma89@gmail.com)
**/

require_once ("connection.php");
require_once ("HandleOperationsException.php");
require_once ("Logger.php");

class ProductUpdate{
     private $logger=null;
        
    public function __construct(){
        $this->logger = new Logger();
    }
    
    /** 
    *Function that updates checks if product exists informations
    *@params int $reference
    *@return string prestashop id if exists, false if not exits
    */
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
    
    /** 
    *Function that inserts product informations
    *@params int $psIdImage, int $psIdImage, string $coloranalysis, string $md5Digest,
    *string $imgPath
    */
    public function insertProduct($ps_id, $reference, $attivo, $categoria, $prezzo, $supplier, $manufacture, $qta, $qta_min, $lunghezza, $altezza, $larghezza, $colore, $quantita, $taglia, $nome, $modello, $linea, $codice_colore, $codice_taglia, $url, $immagine ){
         $connection = connectionServer();
            $sql = "INSERT INTO ps_tmp_product (ps_id, reference, attivo, categoria, prezzo, supplier,                manufacture, qta, qta_min, lunghezza, altezza,larghezza,colore,quantita,taglia, nome, modello, linea, codice_colore, codice_taglia, url, immagine )VALUES('".$ps_id."','".$reference."','".$attivo."',            '".$categoria."','".$prezzo."','".$supplier."','".$manufacture."','".$qta."','".$qta_min."',
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
    *Function that updates product informations
    *@params int $ps_od, string $attivo,string $prezzo,string $qta,string $qta_min,string $lunghezza,string
    *$altezza,string $larghezza,string $nome,string $modello,string $linea 
    */
    public function updateProduct($ps_id, $attivo, $prezzo, $qta, $qta_min, $lunghezza, $altezza, $larghezza, $nome, $modello, $linea,$colore, $taglia ){
         $connection = connectionServer();
            $sql = "UPDATE  ps_tmp_product SET attivo = '".$attivo."', prezzo = '".$prezzo."', qta = '".$qta."', qta_min = '".$qta_min."', lunghezza = '".$lunghezza."', altezza = '".$altezza."',larghezza = '".$larghezza."', nome = '".$nome."', modello = '".$modello."' , linea = '".$linea."' WHERE ps_id = '".$ps_id."' AND colore = '".$colore."' AND taglia = '".$taglia."'";
        echo "</br> $sql";
            try{
                $res = mysql_query($sql,$connection);
            }catch(Exception $e){
                echo $e."";
            }
            if($res){
                $this->logger->postMessage("Il prodotto $ps_id e' stato modificato correttamente ");
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