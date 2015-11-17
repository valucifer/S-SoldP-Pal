<?php

require("ColorComparison.php");
ini_set('max_execution_time', 600);

$color_comparison = new ColorComparison();

$files = scandir("imgs");

$index = 1;
$start = microtime(true);
foreach($files as $file){
    if($file === '.DS_Store' || $file === "." || $file === ".."){
        continue;
    }
    echo "<div>Image ".$index." called ".$file.":</div><br/>";  
    $color_comparison->getColorsTable("imgs/".$file);
    $index++;
}
$end = microtime(true)-$start;

echo "<br/>time: ".$end;

?>