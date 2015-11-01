<?php
	//prova con la libreria PHP-diff
	//Tale libreria sta al seguente indirizzo http://code.stephenmorley.org/php/diff-implementation/
	require_once './class.Diff.php';

	//Se nel compareFiles |che viene usato per comparare i file|
	//viene messo true, avviene una comparazione carattere per carattere. Altrimenti
	//solo per righe.
	//diff:compareFIles -> ('','',true/false) -> true compare char by char
	$differenceBetweenTwoFiles = Diff::compareFiles('proof1.txt','proof2.txt');

	//L'output è un array contenente le righe dei due file. Ad ogni
	//elemento dell'array corrisponde un array di due elementi:
	//alla posizione [0] c'è la riga del file, alla posizione [1]
	//c'è il risultato della funzione (0: le righe sono uguali, 1: la riga appartiene solo al file 1, 2:
	//la riga appartiene solo al file 2).
	$lengthArray = sizeof($differenceBetweenTwoFiles);

	$bool = false;
	for ( $i = 0; $i < $lengthArray; $i++ ){
		$array = $differenceBetweenTwoFiles[$i];
		$compareFiles = $array[1];
		
		if($compareFiles != 0){
			$bool = true;
			break;
		}
	}

	if(!$bool)
		echo "i due file sono uguali.";
	else
		echo "i due file sono diversi.";
	
?>