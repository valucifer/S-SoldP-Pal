<?php

echo "This is path before running set:".get_include_path()."<br/>";

set_include_path(get_include_path() . PATH_SEPARATOR . 'pkgs');
set_include_path(get_include_path() . PATH_SEPARATOR . 'libs');
set_include_path(get_include_path() . PATH_SEPARATOR . 'files');

?>