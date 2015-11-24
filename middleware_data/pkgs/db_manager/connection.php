<?php
require_once("HandleOperationsException.php");
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
		// Create connection
		$connection = mysql_connect(MD_DB_SERVER_NAME, MD_DB_USERNAME, MD_DB_PASSWORD);

		// Check connection
		if (!$connection) {
			throw new HandleOperationsException("Connection failed: ".mysql_error());
		}
		
		//Create connection database
		$database = mysql_select_db (MD_DB_NAME, $connection);
		
		//Check connection
		if (!$database) {
			throw new HandleOperationsException("Connection database failed: ". mysql_error());
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
			throw new HandleOperationsException("Close connection failed: ".mysql_error());
		}
		return $close_connection;
	}

    ?>