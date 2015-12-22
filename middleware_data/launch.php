<?php
/**
 * Created by PhpStorm.
 * User: johneisenheim
 * Date: 06/12/15
 * Time: 14:55
 */

require("/Applications/MAMP/htdocs/prestashop/webservice/middleware_data/MiriadeOperations.php");
$miriade = new MiriadeOperations();
$miriade->start();