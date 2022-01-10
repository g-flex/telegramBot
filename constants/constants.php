<?php

include_once "environment_constants.php";
include_once "bot_constants.php";
include_once "emoji_constants.php";
include_once "strings_constants.php";

//set print to true to save input and output in a file
//set live to true to send output via telegram message to CREATOR_TELEGRAM_ID
define("DEBUG", false);
define("PRINT_DEBUG", false);
define("LIVE_DEBUG", false);
define("PRINT_REQUEST_DEBUG", false);
define("PRINT_RESPONSE_DEBUG", false);


if(DEBUG) {
	ini_set('display_errors', 'On');
	error_reporting(E_ALL);
}