<?php
	//Creato da: Valentino Vivone
	//il giorno: 02/11/15
	//Modificato da: Valentino Vivone
	//il giorno: 09/11/2015
	
	//Creazione della classe che verrà utilizzata per l'importazione dei dati all'interno del database.
	class Mapping{
		//Inizializzazione delle variabili 
		private $triple;
		private $duple;
		private $_TB_ART;
		private $_TB_ART_DET;
		private $_TB_ART_DIBA;
		private $_TB_ART_DESCR;
		private $_TB_ART_DET_FOTO;
		private $_TB_ART_DET_DISP;
		private $_TB_DESCR_LIN;
		private $keys;
		
		function Mapping($path){
			//Costruttore: in input deve essere passato il path del file semaforo al fine di recuperare gli altri path
			//i cui elementi devono essere presi e trattati secondo le specifiche.
			
			//Ogni file avrà un array associato contenente gli elementi dei singoli.
			$tmpArray = $this->createArray($path);
			if(sizeof($tmpArray) === 1){
				throw new Exception("Il file semaforo è vuoto");
			}else{
				$tmpPath = "File/";
				
				$this->_TB_ART = $this->createArray($tmpPath.$tmpArray[1]);
				if(!$this->launchException($this->_TB_ART,17)){
					throw new Exception("Il file ".$tmpPath.$tmpArray[1]."non rispetta la corretta sintassi oppure è vuoto");
				}
				
				//$this->_TB_ART_DESCR = $this->createArray($tmpPath.$tmpArray[2]); DA VALUTARE SE CONSIDERARLI
				
				$this->_TB_ART_DET = $this->createArray($tmpPath.$tmpArray[3]);
				if(!$this->launchException($this->_TB_ART_DET,8)){
					throw new Exception("Il file ".$tmpPath.$tmpArray[3]."non rispetta la corretta sintassi oppure è vuoto");
				}
				
				$this->_TB_ART_DET_DISP = $this->createArray($tmpPath.$tmpArray[4]);
				if(!$this->launchException($this->_TB_ART_DET_DISP,5)){
					throw new Exception("Il file ".$tmpPath.$tmpArray[4]."non rispetta la corretta sintassi oppure è vuoto");
				}
				
				$this->_TB_ART_DET_FOTO = $this->createArray($tmpPath.$tmpArray[5]);
				if(!$this->launchException($this->_TB_ART_DET_FOTO,5)){
					throw new Exception("Il file ".$tmpPath.$tmpArray[5]."non rispetta la corretta sintassi oppure è vuoto");
				}
				
				//$this->_TB_ART_DIBA = $this->createArray($tmpPath.$tmpArray[6]); DA VALUTARE SE CONSIDERARLI
				
				$this->_TB_DESCR_LIN = $this->createArray($tmpPath.$tmpArray[7]);
				if(!$this->launchException($this->_TB_DESCR_LIN,4)){
					throw new Exception("Il file ".$tmpPath.$tmpArray[7]."non rispetta la corretta sintassi oppure è vuoto");
				}
				
				//Creazione di array avente la chiave (CODICE_ARTICOLO) e la tupla (CODICE_ARTICOLO;COLORE;TAGLIA)
				$this->keys = $this->keys();
				$this->triple = $this->triple();
			}
		}
		
		private function launchException($array, $exprReg){
			$flag = true;
			if(sizeof($array) === 1){
				$flag = false;
			}else{
				$size = sizeof($array);
				for($i = 0; $i < $size; $i++){
					$tmp  = explode(";",$array[$i]);
					$sizeTmp = sizeof($tmp); 
					if($sizeTmp === 1) break;
					if($sizeTmp != $exprReg){
						$flag = false;
						break;
					}					
				}
			}
			return $flag;
		}
		
		private function sortFile($path){
			//Dal momento che il file dato in input 'potrebbe' non contenere elementi ordinati, tale funzion avrà il compito di ordinarli
			//al fine di 'ridurre' il tempo computazionale delle funzioni successive.
			
			//Si accede al file in modalità lettura
			$pathFile = array();
			$_TB = fopen(trim($path), "r") or die("Unable to open file!"); //apertura del file in 'lettura'
			while(!feof($_TB)) {
				$tmp = fgets($_TB);
				array_push($pathFile, $tmp);
			}
			
			//Dopo aver creato l'array associato, il medesimo viene ordinato
			sort($pathFile);
			
			//Si accede al file in modalità scrittura
			$_TB = fopen(trim($path), "w") or die("Unable to open file!"); //apertura del file in 'scrittura'
			$size = sizeof($pathFile);
			for($i = 0; $i < $size; $i++){
				if($pathFile[$i] !== "\n")
					fwrite($_TB,$pathFile[$i]);
			}
			
			//Dopo aver scritto gli elementi ordinati sul file, si procede alla chiusura.
			fclose($_TB);
		}
		
		private function extractInFile($path){
			//Estrazione degli elementi contenuti nel file dato il path $path in input.
			//Nello specifico, si crea una variabile array $pathFile che, successivamente, verrà
			//data in output avente gli elementi suddetti.
			//Verrà utilizzata la funzione fopen per accedere al file, la funzione trim per evitare eventuali
			//spazi contenuti nel path dato in input, lo si scorre fino alla fine ed ogni riga contenuta nel file
			//verrà aggiunta all'array suddetto.
			
			$pathFile = array();
			$_TB = fopen(trim($path), "r") or die("Unable to open file!"); //apertura del file in 'lettura'
			
			while(!feof($_TB)) {
				$tmp = fgets($_TB);
				array_push($pathFile, $tmp);
			}
			//print_r($pathFile);
			return $pathFile;
			
			//Esempio di Output:
			//[0] => xxxxx;xxxxx;xxxxx;xxxx
			//[1] => xxxx;xx;xxxxxxxxx;x
		}
	
		private function createArray($path){
			//Tale funzione crea un array, dato il path del fine in input, contenente gli elementi dei file suddetto.
			//Gli elementi del file vengono ordinati. L'output sarà l'array menzionato.
			$this->sortFile($path);
			return $this->extractInFile($path);
		}
		
		public function keys(){
			//Semplice funzione utilizzata per l'estrapolazione dei codici materiali
			//dato in input un array contenente gli articoli contenuti nei file.
			
			$arrayTMP = array();
			$size = sizeof($this->_TB_ART);
			for($i = 0; $i < $size; $i++){
				$tmp = explode(";",$this->_TB_ART[$i]); //split sul valore ';'. Mi restituisce un array
				if(sizeof($tmp) === 1) // per evitare si inserire un elemento vuotonell'array
					break;
				array_push($arrayTMP,$tmp[0]);
			}
			
			return $arrayTMP;
			
			//Esempio di Output:
			//[0] = > xxxxxxxxx
			//[1] => xxccxx
		}
		
		private function getArtAndAttribute(){
			//getArtAndAttribute: è una funzione che, dato l'array corrispondente al file _TB_ART,
			//genera un array associativo ($KEY: la chiave sarà il CODICE_ARTICOLO) avente gli attributi del prodotto
			//(LARGHEZZA;ALTEZZA;LUNGHEZZA).
			
			$return = array();
			$sizeArrayTBART = sizeof($this->_TB_ART);
			
			for($i = 0; $i < $sizeArrayTBART; $i++){
				$tmp = explode(";",$this->_TB_ART[$i]);
				if(sizeof($tmp) === 1) 
					break;
				$element = array();
				$element["Larghezza"] = str_replace(",",".",trim($tmp[12]));
				$element["Altezza"] = str_replace(",",".",trim($tmp[13]));
				$element["Lunghezza"] = str_replace(",",".",trim($tmp[11]));
				
				$return[$tmp[0]] = $element;
			}
			
			return $return;
			
			//Esempio di Output:
			//[$KEY1] = >  [Larghezza]:14,000,[Altezza]:35,000,[Lunghezza]:36,000
			//[$KEY2] = >  [Larghezza]:24,000,[Altezza]:15,000,[Lunghezza]:76,000
		}
		
		private function triple(){
			//Questa funzione genera la chiave univoca degli articoli.
			//Tale chiave sarà la combinazione di CODICE_ARTICOLO;COLORE;TAGLIA.
			//Tale chiave sarà contenuta in un array associativo avente come KEY il codice
			//dell'articolo e VALUE la tripla che sarà creata.
			
			$combinations = array();
			
			$sizeArrayTBARTDET = sizeof($this->_TB_ART_DET);
			$sizeArrayKeys = sizeof($this->keys);
			
			$count = 0;
			for($j = 0; $j < $sizeArrayKeys; $j++){
				$element = array();
				for ($i = $count; $i < $sizeArrayTBARTDET; $i++){
					$tmp = explode(";",$this->_TB_ART_DET[$i]);
					if(sizeof($tmp) === 1) 
						break;
					if($this->keys[$j] === $tmp[0])
						$element[$tmp[0].";".$tmp[1].";".$tmp[2]] = $tmp[0].";".$tmp[1].";".$tmp[2];
					else{
						$count = $i;
						break;
					}
				}
				$combinations[$this->keys[$j]] = $element;
			}
			
			return $combinations;
			
			//Esempio di Output:
			//[CODICE_ARTICOLO1] => [0] => CODICE_ARTICOLO1;COLORE;TAGLIA [1] => CODICE_ARTICOLO1;COLORE;TAGLIA
			//[CODICE_ARTICOLO2] => [0] => CODICE_ARTICOLO2;COLORE;TAGLIA [1] => CODICE_ARTICOLO2;COLORE;TAGLIA
		}
		
		
		public function getCategory(){
			$return = array();
			$sizeArrayTBDESCRLIN = sizeof($this->_TB_DESCR_LIN);
			
			//Inizializziamo le variabili di cui necessitiamo
			$griffe = null;
			
			for($j = 0; $j < $sizeArrayTBDESCRLIN; $j++){
				$tmp1 = explode(";",$this->_TB_DESCR_LIN[$j]);
				if(sizeof($tmp1) === 1) 
					break;
	
				//Ovviamente ricerchiamo tutte queste caratteristiche andando a prelevare solo i dati in lingua ITALIANA
				if($tmp1[0] === "GRIFFE" and $tmp1[2] === "IT"){
					$griffe = trim($tmp1[3]);
					$griffe = str_replace("\"","",$griffe);
					array_push($return,$griffe);
				}		
				if($tmp1[0] === "LP"){
					break;
				}	
			}
			
			return $return;
		}
		
		public function getSubCategory(){
			$return = array();
			$sizeArrayTBDESCRLIN = sizeof($this->_TB_DESCR_LIN);
			
			//Inizializziamo le variabili di cui necessitiamo
			$model = null;
			
			for($j = 0; $j < $sizeArrayTBDESCRLIN; $j++){
				$tmp1 = explode(";",$this->_TB_DESCR_LIN[$j]);
				if(sizeof($tmp1) === 1) 
					break;
	
				//Ovviamente ricerchiamo tutte queste caratteristiche andando a prelevare solo i dati in lingua ITALIANA
				if($tmp1[0] === "MODELLO" and $tmp1[2] === "IT"){
					$model = trim($tmp1[3]);
					$model = str_replace("\"","",$model);
					array_push($return,$model);
				}		
				if($tmp1[0] === "SESSO"){
					break;
				}	
			}
			
			return $return;
		}
		
		private function getReferenceNameModelAndSupplier(){
			//Con questa funzione avremo la referenza (Quindi il CODICE), il nome, il modello e il fornitore del singolo 
			//articolo contenuto nel file _TB_ART. Tali caratteristiche le possiamo ricavare andando ad effettuare un merge con il 
			//file _TB_DESCR_LIN.
			
			$return = array();
			$sizeArrayTBART = sizeof($this->_TB_ART);
			$sizeArrayTBDESCRLIN = sizeof($this->_TB_DESCR_LIN);
			
			for($i = 0; $i < $sizeArrayTBART; $i++){
				//Scorriamo tutto l'array associato al file _TB_ART
				$tmp = explode(";",$this->_TB_ART[$i]);
				if(sizeof($tmp) === 1) 
					break;
				
				//Inizializziamo le variabili di cui necessitiamo
				$reference = $tmp[0]; $name = null; $model = null; $griffe = null; //<- DOBBIAMO VEDERE SE SUPPLIER = MANUFACTURE
				
				for($j = 0; $j < $sizeArrayTBDESCRLIN; $j++){
					//Da ogni elemento contenuto nel file di cui sopra, ricaviamo i codici necessari 
					//al rilevamento del modello e della griffe associati.
					$tmp1 = explode(";",$this->_TB_DESCR_LIN[$j]);
					if(sizeof($tmp1) === 1) 
						break;
					
					//Quindi ricerchiamo il CODICE_ARTICOLO 
					if($tmp1[0] === "CDMAT" and $tmp1[2] === "IT"){
						if($tmp1[1] === $tmp[0]){
							$name = trim($tmp1[3]);
						}
					}
					
					//Ricerchiamo il modello
					if($tmp1[0] === "MODELLO" and $tmp1[2] === "IT"){
						if($tmp1[1] === $tmp[6]){
							$model = trim($tmp1[3]);
						}
					}
					
					//Ed infine ricerchiamo la griffe.
					//Ovviamente ricerchiamo tutte queste caratteristiche andando a prelevare solo i dati in lingua ITALIANA
					if($tmp1[0] === "GRIFFE" and $tmp1[2] === "IT"){
						if($tmp1[1] === $tmp[2]){
							$griffe = trim($tmp1[3]);
						}
					}					
				}
				
				$name = str_replace("\"","",$name);
				$model = str_replace("\"","",$model);
				$griffe = str_replace("\"","",$griffe);
				
				//Ogni caratteristica ricavata verrà associata in un array con chiave il codice del prodotto
				$return[$reference] = $reference.";".$name.";".$model.";".$griffe;
			}
			
			return $return;
			
			//Esempio di Output:
			//[KEY1] => CODICE_ARTICOLO1;NOME;MODELLO;GRIFFE 
			//[KEY2] => CODICE_ARTICOLO2;NOME;MODELLO;GRIFFE
		}
		
		private function getPriceAndSalable(){
			//Con questa funzione avremo il prezzo e il flag di vendibilità del singolo 
			//articolo contenuto nel file _TB_ART_DET. 
			//Tali caratteristiche saranno ricavate andando a cercare il codice dell'articolo.
			
			$return = array();
			$sizeKey = sizeof($this->keys);
			$sizeArrayTBARTDET =  sizeof($this->_TB_ART_DET);
			
			//$count viene utilizzato come indice di scorrimento. (poichè gli elementi nel file sono stati ordinati, è inutile andare
			//a scorrere tutto il file; bensì si salva l'ultimo indice e si riparte da questo).
			$count = 0;
			for($i = 0; $i < $sizeKey; $i++){
				$flag = 0;
				$price = "";
				for($j = $count; $j < $sizeArrayTBARTDET; $j++){
					$tmp = explode(";",$this->_TB_ART_DET[$j]);
					if(sizeof($tmp) === 1) 
						break;
					if($this->keys[$i] === $tmp[0]){
						$price = str_replace(",",".",trim($tmp[4]));
						if($tmp[6] === "S"){
							$flag = 1;
						}
					}else{
						$count = $j;
						break;
					}
				}
				
				//DA TENERE IN CONTO: il flag di vendibilità viene riportato per tutte le combinazioni di CODICE_ARTICOLO;COLORE;TAGLIA.
				//Pertanto, per esempio, se lo stesso articolo ha due colori diversi (ROSSO E BLUE), allora si va a ricercare il flag di ognuno:
				//	1) Se tutte e due presentano 'S', allora l'articolo è vendibile;
				//	2) Se almeno uno dei due presenta 'S', allora l'articolo è vendibile;
				//	3) Se entrambe presentano 'N, allora non è vendibile;
				//BISOGNA DOMANDARE
				$return[$this->keys[$i]] = $price.";".$flag;
			}
			
			return $return;
			
			//Esempio di Output:
			//[KEY1] => PREZZO;0|1 <- Se il flag è 'S' allora sarà 1, altrimenti 0
			//[KEY2] => PREZZO;0|1 <- Se il flag è 'S' allora sarà 1, altrimenti 0
		}
		
		//DA DOMANDARE se si deve considerare la combinazione o i singoli per quanto riguarda la quantità
		/*private function getQuantity(){
			//Con questa funzione avremo la quantità e la quantità minima del singolo 
			//articolo contenuto nel file _TB_ART_DET_DISP. 
			//Tali caratteristiche saranno ricavate andando a cercare il codice dell'articolo.
			
			$return = array();
			$sizeKey = sizeof($this->keys);
			$sizeArrayTBARTDETDISP =  sizeof($this->_TB_ART_DET_DISP);
			
			//$count viene utilizzato come indice di scorrimento. (poichè gli elementi nel file sono stati ordinati, è inutile andare
			//a scorrere tutto il file; bensì si salva l'ultimo indice e si riparte da questo).
			$count = 0;
			for($i = 0; $i < $sizeKey; $i++){
				$element = array();
				for($j = $count; $j < $sizeArrayTBARTDETDISP; $j++){
					$tmp = explode(";",$this->_TB_ART_DET_DISP[$j]);
					if(sizeof($tmp) === 1) 
						break;
					$tripleTBARTDET = $tmp[0].";".$tmp[1].";".$tmp[2];
					if($this->keys[$i] === $tmp[0]){
						$element[$tripleTBARTDET] = $tmp[4].";1";//Qtà;1 => Qtà: quantità, 1: quantità minima dell'articolo (DA DOMANDARE)
					}else{
						$count = $j;
						break;
					}
				}
				$return[$this->keys[$i]] = $element;
			}
			
			return $return;
			
			//Esempio di Output:
			//[KEY1] => QTA';1
			//[KEY2] => QTA';1
		}*/
		
		private function getQuantity(){
			//Con questa funzione avremo la quantità e la quantità minima del singolo 
			//articolo contenuto nel file _TB_ART_DET_DISP. 
			//Tali caratteristiche saranno ricavate andando a cercare il codice dell'articolo.
			
			$return = array();
			$sizeKey = sizeof($this->keys);
			$sizeArrayTBARTDETDISP =  sizeof($this->_TB_ART_DET_DISP);
			
			//$count viene utilizzato come indice di scorrimento. (poichè gli elementi nel file sono stati ordinati, è inutile andare
			//a scorrere tutto il file; bensì si salva l'ultimo indice e si riparte da questo).
			$count = 0;
			for($i = 0; $i < $sizeKey; $i++){
				$totQta = 0;
				for($j = $count; $j < $sizeArrayTBARTDETDISP; $j++){
					$tmp = explode(";",$this->_TB_ART_DET_DISP[$j]);
					if(sizeof($tmp) === 1) 
						break;
					$tripleTBARTDET = $tmp[0].";".$tmp[1].";".$tmp[2];
					if($this->keys[$i] === $tmp[0]){
						$totQta = $totQta + $tmp[4];
						//$element[$tripleTBARTDET] = $tmp[4].";1";//Qtà;1 => Qtà: quantità, 1: quantità minima dell'articolo (DA DOMANDARE)
					}else{
						$count = $j;
						break;
					}
				}
				$return[$this->keys[$i]] = $totQta.";1";
			}
			
			return $return;
			
			//Esempio di Output:
			//[KEY1] => QTA';1
			//[KEY2] => QTA';1
		}
		
		private function duple($arrayTBARTDETFOTO){
			//Questa funzione genera la chiave univoca degli articoli.
			//Tale chiave sarà la combinazione di CODICE_ARTICOLO;COLORE.
			//Tale chiave sarà contenuta in un array associativo avente come KEY il codice
			//dell'articolo e VALUE la la dupla suddetta. Tale funzione sarà utile per ricercare le foto
			//associate all'articolo.			
			$combinations = array();
			
			$sizeArrayTBARTDETFOTO = sizeof($this->_TB_ART_DET_FOTO);
			for ($i = 0; $i < $sizeArrayTBARTDETFOTO; $i++){
				$tmp = explode(";",$this->_TB_ART_DET_FOTO[$i]);
				if(sizeof($tmp) === 1) 
					break;
				
				$combinations[$tmp[0]] = $tmp[0].";".$tmp[1];
			}
			
			return $combinations;
			
			//Esempio di Output:
			//[CODICE_ARTICOLO1] => [0] => CODICE_ARTICOLO1;COLORE
			//[CODICE_ARTICOLO2] => [0] => CODICE_ARTICOLO2;COLORE
		}
		
		private function getUrlFoto(){
			
			$return = array();
			$sizeKey = sizeof($this->keys);
			$sizeArrayTBARTDETFOTO =  sizeof($this->_TB_ART_DET_FOTO);
			
			$count = 0;
			for($i = 0; $i < $sizeKey; $i++){
				$element = array();
				for($j = $count; $j < $sizeArrayTBARTDETFOTO; $j++){
					$tmp = explode(";",$this->_TB_ART_DET_FOTO[$j]);
					if(sizeof($tmp) === 1) 
						break;
					$dupleTBARTDET = $tmp[0].";".$tmp[1];
					if($this->keys[$i] === $tmp[0]){
						$element[$dupleTBARTDET] = trim($tmp[4]);
					}else{
						$count = $j;
						break;
					}
				}
				$return[$this->keys[$i]] = $element;
			}
			return $return;
		}
		
		private function getColors($codColor){
			//La funzione crea una combinazione di colori associata all articolo preso in oggetto.
			//La stringa $return viene inizializzata a ':' poichè successivamente andremo ad effettuare
			//l' EXPLODE su tale carattere al fine di prelevare i valori.
			$return = ":";
			$sizeArrayTBDESCRLIN = sizeof($this->_TB_DESCR_LIN);
			
			if($codColor != ""){
				for($i = 0; $i < $sizeArrayTBDESCRLIN; $i++){
					$tmp = explode(";",$this->_TB_DESCR_LIN[$i]);
					if(sizeof($tmp) === 1) 
						break;
					if($tmp[0] === "COLORE" && $tmp[1] === $codColor && $tmp[2] === "IT"){
						$return = $codColor.":".str_replace("\"","",trim($tmp[3]));
						break;
					}
				}
			}
			
			return $return;
		}
		
		private function getSizes($codSize){
			//La funzione crea una combinazione di taglie associata all articolo preso in oggetto.
			//La stringa $return viene inizializzata a ':' poichè successivamente andremo ad effettuare
			//l' EXPLODE su tale carattere al fine di prelevare i valori.
			$return = ":";
			$sizeArrayTBDESCRLIN = sizeof($this->_TB_DESCR_LIN);
			if($codSize != ""){
				for($i = 0; $i < $sizeArrayTBDESCRLIN; $i++){
					$tmp = explode(";",$this->_TB_DESCR_LIN[$i]);
					if(sizeof($tmp) === 1) 
						break;
					if($tmp[0] === "TAGLIA" && $tmp[1] === $codSize && $tmp[2] === "IT"){
						$return = $codSize.":".str_replace("\"","",$tmp[3]);
						break;
					}
				}
			}
			
			return $return;
		}
		
		private function getImages($codArt,$codColor){
			//La funzione crea una combinazione di immagini associata all articolo preso in oggetto.
			$return = "";
			$sizeArrayTBFOTO = sizeof($this->_TB_ART_DET_FOTO);
			
			if($codColor != "" && $codArt != ""){
				for($i = 0; $i < $sizeArrayTBFOTO; $i++){
					$tmp = explode(";",$this->_TB_ART_DET_FOTO[$i]);
					if(sizeof($tmp) === 1) 
						break;
					if($tmp[0] === $codArt && $tmp[1] === $codColor){
						$return = trim($tmp[4]);
						break;
					}
				}
			}
			
			return $return;
		}
		
		private function getAttributesValuesAndImagesForCombinations(){
			//La funzione restituisce la combinazione di COLORE, TAGLIA e IMMAGINE associata alla dupla CODICE_ARTICOLO;COLORE
			$return = array();
			$sizeArrayKeys = sizeof($this->keys);
			
			for($i = 0; $i < $sizeArrayKeys; $i++){
				$keysArrayTriple = $this->triple[$this->keys[$i]];
				$sizeArrayKeysTriple = sizeof($keysArrayTriple);
				$element = array();
				
				foreach($keysArrayTriple as $value){
					$tmp  = explode(";",$value);
					if(sizeof($tmp) === 1) 
						break;
					$color = $this->getColors($tmp[1]);
					$size = $this->getSizes($tmp[2]);
					$image = $this->getImages($tmp[0],$tmp[1]);
					
					$tmpColor = explode(":",$color);
					$tmpSize = explode(":",$size);
					
					$attributes = "(COLORE:".$tmpColor[0]."),(TAGLIA:".$tmpSize[0].")";
					$values = "(".$tmpColor[0].":".$tmpColor[1]."),(".$tmpSize[0].":".$tmpSize[1].")";
					
					$element[$value] = array("Attribute" => $attributes, "Value" => $values, "Image" => $image);
				}
				$return[$this->keys[$i]] = $element;
			}
			
			return $return;
			
			//Esempio di Output:
			//[KEY1] => ( [TRIPLA1] => ( [Attribute] => (COLORE:001),(TAGLIA:) [Value] => (001:"NERO" ),(:) [Image] => CODICE,2,COLORE.jpg ) 
			//[KEY2] => ( [TRIPLA2] => ( [Attribute] => (COLORE:001),(TAGLIA:) [Value] => (001:"NERO" ),(:) [Image] => CODICE,2,COLORE.jpg ) 
		}
		
		public function getItemMaster(){
			//Funzione che rilascia un array contenente ogni singolo campo rilevato nella tabella Mapping
			//associato ad una chiave (IL CODICE_ARTICOLO).
			$return = array();
			
			//Creazione delle combinazioni necessarie.
			$priceAndSalable = $this->getPriceAndSalable();
			$referenceNameModelAndSupplier = $this->getReferenceNameModelAndSupplier();
			$quantity = $this->getQuantity();
			$urlFoto = $this->getUrlFoto();
			$feature = $this->getArtAndAttribute();
			
			//Prendo i codici associati agli articoli. 
			foreach($this->keys as $keys){
				//Creo l'associazione chiave (il nome del campo della tabella) - valore (risultato delle creazioni degli array).
				$element = array();
				$tmp = explode(";",$priceAndSalable[$keys]);
				$element["Prezzo"] = $tmp[0];
				$element["Attivo"] = $tmp[1];
				
				$tmp = explode(";",$referenceNameModelAndSupplier[$keys]);
				$element["Reference"] = $tmp[0];
				$element["Nome"] = $tmp[1];
				$element["Categorie"] = $tmp[2].",".$tmp[3];
				$element["Supplier"] = $tmp[3];
				$element["Manufacture"] = $tmp[3];
				
				$tmp = explode(";",$quantity[$keys]);
				$element["Qta"] = $tmp[0];
				$element["Qta_min"] = $tmp[1];
				
				$element["Feature"] = $feature[$keys];
				
				$stringTmp = "";
				foreach($urlFoto[$keys] as $singleFoto){
						$stringTmp = $stringTmp.$singleFoto.",";
				}
				$element["URL"] = $stringTmp;
				
				$return[$keys] = $element;
			}
			
			return $return;
		}
		
		public function getCombinations(){
			//Crea un array relativo alla tabella Combinazioni.
			$return = array();
			$combinations = $this->getAttributesValuesAndImagesForCombinations();
			
			//Scorro tutte le chiavi associate agli articoli.
			foreach($this->keys as $keys){
				$element = array();
				$element["Product_reference"] = $keys;
				$tmp = $combinations[$keys];
				foreach($this->triple[$keys] as $combo){
					$elementTMP = array();
					$sigleAttributeValueAndImage = $tmp[$combo];
					
					$element[$combo] = array("Attributi" => $sigleAttributeValueAndImage["Attribute"],"Valori" => $sigleAttributeValueAndImage["Value"],"Immagine" => $sigleAttributeValueAndImage["Image"]);
					
				}
				$return[$keys] = $element;
			}
			
			return $return;
		}
		
	}
	
	
	
    
	
?>