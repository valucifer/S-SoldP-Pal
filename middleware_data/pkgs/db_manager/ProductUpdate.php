<?php
require_once("settings.php");
require_once ("connection.php");
require_once (MD_LIBS_DIR."/HandleOperationsException.php");
require_once (MD_LIBS_DIR."/Logger.php");

/**
* PHP class update the tmp custom table used to verify the changes between
* the last import and the new product import 
* @package    UpdateProduct
* @author     Carlos Borges (carboma89@gmail.com)
**/


class ProductUpdate{
     private $logger=null;        
    public function __construct(){
        $this->logger = new Logger();
    }
    
    /** 
    *Checks if product exists 
    *@params int $reference
    *@return string prestashop id if exists, false if not exits
    */
    public function productExists($reference){
            $connection = connectionServer();
            $sql = "SELECT * FROM  ps_tmp_product WHERE ( reference = '".$reference."')";
            $result = mysql_query($sql,$connection);
            if (mysql_num_rows($result) > 0){
                $toReturn = null;
                while($row = mysql_fetch_array( $result )){
                    $toReturn = (int) $row[0]; 
                }
                closeConnectionServer($connection);
                return $toReturn;    
            } else {
                closeConnectionServer($connection);
                return "false";
            }
    }
    
    
    public function colorSizeExists($reference, $ps_id, $code_color, $code_size){
         $connection = connectionServer();
            $sql = "SELECT * FROM  ps_tmp_product WHERE ( reference = '".$reference."' AND ps_id ='".$ps_id."' AND (codice_colore = '".$code_color."' AND codice_taglia ='".$code_size."'))";
            $result = mysql_query($sql,$connection);
            $nrow = mysql_num_rows($result);
            if (mysql_num_rows($result) > 0){
                closeConnectionServer($connection);
                return 0;    
            } else {
                closeConnectionServer($connection);
                return 1;
            }
    }
    
    
    /** 
    *Inserts product informations
    *@params int $psIdImage, int $psIdImage, string $coloranalysis, string $md5Digest,
    *string $imgPath
    */
    public function insertProduct($ps_id, $reference, $attivo, $categoria, $prezzo, $supplier, $manufacture, $qta, $qta_min, $lunghezza, $altezza, $larghezza, $colore, $quantita, $taglia, $nome, $modello, $linea, $codice_colore, $codice_taglia, $url, $immagine ){
         $connection = connectionServer();
            $sql = "INSERT INTO ps_tmp_product (ps_id, reference, attivo, categoria, prezzo, supplier,                manufacture, qta, qta_min, lunghezza, altezza,larghezza,colore,quantita,taglia, nome, modello, linea, codice_colore, codice_taglia, url, immagine )VALUES('".$ps_id."','".$reference."','".$attivo."',            '".$categoria."','".$prezzo."','".$supplier."','".$manufacture."','".$qta."','".$qta_min."',
 '".$lunghezza."','".$altezza."','".$larghezza."','".$colore."','".$quantita."','".$taglia."','".$nome."','".$modello."','".$linea."','".$codice_colore."','".$codice_taglia."','".$url."','".$immagine."')";
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
    
    /** 
    *Updates product informations
    *@params int $ps_id, string $attivo,string $prezzo,string $qta,string $qta_min,string $lunghezza,string
    *$altezza,string $larghezza,string $nome,string $modello,string $linea 
    */
    public function updateProduct($ps_id, $attivo, $prezzo, $qta, $qta_min, $lunghezza, $altezza, $larghezza, $nome, $modello, $linea,$colore, $taglia ){
         $connection = connectionServer();
            $sql = "UPDATE  ps_tmp_product SET attivo = '".$attivo."', prezzo = '".$prezzo."', qta = '".$qta."', qta_min = '".$qta_min."', lunghezza = '".$lunghezza."', altezza = '".$altezza."',larghezza = '".$larghezza."', nome = '".$nome."', modello = '".$modello."' , linea = '".$linea."' WHERE ps_id = '".$ps_id."' AND colore = '".$colore."' AND taglia = '".$taglia."'";
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