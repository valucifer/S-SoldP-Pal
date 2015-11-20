<?php
    /**
    * PHP class update the tmp custom table use to verify the change between
    * the last import and the new import of images of a product 
    * @package    UpdateProduct
    * @author     Carlos Borges (carboma89@gmail.com)
    **/

    /**
    *Connection to database
    *@return string
    **/
	function connectionServer(){
		$SERVERNAME = "localhost:3306";
		$USERNAME = "root";
		$PASSWORD = "root";

		// Create connection
		$connection = mysql_connect($SERVERNAME, $USERNAME, $PASSWORD);

		// Check connection
		if (!$connection) {
			die("Connection failed: ".mysql_error());
		}
		
		//Create connection database
		$database = mysql_select_db ("prestashop", $connection);
		
		//Check connection
		if (!$database) {
			die("Connection database failed: ". mysql_error());
		}
		
		return $connection;
	}
	
    /**
    *Closes database Connection
    *@return string
    **/
	function closeConnectionServer($server){
		//close the connection
		$close_connection = mysql_close($server);
		if(!$close_connection) {
			die("Close connection failed: ".mysql_error());
		}
		return $close_connection;
	}

    ?>