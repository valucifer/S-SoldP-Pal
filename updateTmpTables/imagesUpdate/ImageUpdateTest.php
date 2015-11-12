<?php

    /**
    * PHP test class gor ImageUpdate
    * @package    UpdateProduct
    * @author     Carlos Borges (carboma89@gmail.com)
    **/

    require ("ImageUpdate.php");
    
    $update = new ImageUpdate();
    echo "Faccio l'inserimento di una nuova imagine </br>";
    $update->updateImageInformation(1,1,"Prova color analysis 1", "Prova per dm5 1","prova path 1");
    echo "Faccio la ricerca del prodotto aggiornato </br>";
    $returValue = $update-> getImageInformation(1);
    echo "Il  prodotto 1  ha color analysis: ".$returValue["colorAnalysis"]." </br> md5 digest: ".$returValue["md5Digest"]." </br> old paht: ".$returValue["oldPath"]." </br> new paht:".$returValue["newPath"]." </br>";
    echo"******************************************************* </br>";
    echo "Faccio l'inserimento di una nuova immagine </br>";
    $update->updateImageInformation(1,2,"Prova color analysis 2", "Prova per dm5 2","prova path 2");
    $returValue=$update->getImageInformation(2);
    echo "Il  prodotto 1 ha color analysis: ".$returValue["colorAnalysis"]." </br> md5 digest: " .$returValue["md5Digest"]." </br> old paht: ".$returValue["oldPath"]." </br> new paht:".$returValue["newPath"]." </br>";
    echo"******************************************************* </br>";
    echo "Faccio update dell'immagine in position 2 </br>";
    $update->updateImageInformation(1,2,"Nuova prova color analysis 3", "Nuova prova per dm5 3","nuova prova path 3");
   $returValue=$update-> getImageInformation(2);
    echo "Il  prodotto 1 ha color analysis: ".$returValue["colorAnalysis"]." </br> md5 digest: " .$returValue["md5Digest"]." </br> old paht: ".$returValue["oldPath"]." </br> new paht:".$returValue["newPath"]." </br>";
    
?>