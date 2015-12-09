<?php

define("MD_FTP_SERVER","ftp14.ilbello.com");
define("MD_FTP_USERNAME", "pjgroup");
define("MD_FTP_PORT",2114);
define("MD_FTP_PASSWORD", "3nRvgCMDYv");
define("MD_DB_SERVER_NAME","localhost:3306");
define("MD_DB_USERNAME", "root");
define("MD_DB_PASSWORD","root");
define("MD_DB_NAME","miriadecommerce");

define("MD_FTP_ROOT_DIR", "/");

define("MD_LOG_FILE_DIR",  realpath(dirname(__FILE__) . '/log_files'));
define("MD_LOGGER_LEVEL", "DEBUG"); //i valori possono essere DEBUG o INFO, in maiuscolo
define("MD_LOG_FILE_SIZE",5242880); //5MB

define("MD_PKGS_DIR", realpath(dirname(__FILE__) . '/pkgs'));
define("MD_COLORCOMPARISON_DIR", realpath(MD_PKGS_DIR . '/color_comparison'));
define("MD_DBMANAGER_DIR", realpath(MD_PKGS_DIR . '/db_manager'));
define("MD_PSMANAGER_DIR", realpath(MD_PKGS_DIR . '/ps_manager'));
define("MD_IMAGECHECKER_DIR", realpath(MD_PKGS_DIR . '/image_checker'));
define("MD_FTPCONNECTION_DIR", realpath(MD_PKGS_DIR . '/ftp_connection'));
define("MD_LIBS_DIR", realpath(dirname(__FILE__) . '/libs'));
define("MD_COLORLIB_DIR", realpath(MD_LIBS_DIR . '/color_lib'));
define("MD_FILES_DIR", realpath(dirname(__FILE__) . '/files'));

define("MD_ROOT", realpath(dirname(__FILE__)));

?>