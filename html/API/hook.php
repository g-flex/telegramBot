<?php

include_once "/your/project/path/constants/constants.php";
include_once PATH_FUNCTIONS."api_helpers.php";
include_once PATH_APP."Strings.php";
include_once PATH_APP."CbQuery.php";
include_once PATH_APP."Keyboard.php";
include_once PATH_APP."Update.php";
include_once PATH_APP."Message.php";
include_once PATH_APP."Command.php";
include_once PATH_APP."Run.php";

if(isset($_GET['api_key']) && $_GET['api_key'] === API_KEY) {
        try {
		
		new Update();
		
	} catch (Exception $e) {

		http_response_code(400);
		if($e->getMessage()) { echo json_encode(['Error' => $e->getMessage()]); }
		exit;

	}
} else {
        http_response_code(401);
}
