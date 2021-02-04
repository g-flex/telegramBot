<?php

function curlPost($url, $postParamsArray) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postParamsArray));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$serverResponse = curl_exec($ch);
	curl_close($ch);
	$serverResponse = json_decode($serverResponse, true);
	if (isset($serverResponse['ok']) && $serverResponse['ok'] === true) {
		return true;
	} else if(DEBUG) {
		echo json_encode($serverResponse);
	}
	return false;

}

function countMembersAPI($chatId) {
	$membersCountUrl = URL_MEMBERS_COUNT . '?chat_id=' . $chatId;
	$membersCountResp = json_decode(file_get_contents($membersCountUrl), true);
	if (isset($membersCountResp["result"]) && intval($membersCountResp["result"]) > 0) {
		return intval($membersCountResp["result"]);
	} else {
		throw new Exception(ERR_API);
	}
}

function mbStrlen1(string $text) {
	$length = 0;
	$textlength = \strlen($text);
	for ($x = 0; $x < $textlength; $x++) {
		$char = \ord($text[$x]);
		if (($char & 0xc0) != 0x80) {
			$length += 1 + ($char >= 0xf0);
		}
	}
	return $length;
}
/**
 * Telegram UTF-8 multibyte substring.
 **/
function mbSubstr1(string $text, int $offset, $length = null): string {
	$mb_text_length = mbStrlen1($text);
	if ($offset < 0) {
		$offset = $mb_text_length + $offset;
	}
	if ($length < 0) {
		$length = $mb_text_length - $offset + $length;
	} elseif ($length === null) {
		$length = $mb_text_length - $offset;
	}
	$new_text = '';
	$current_offset = 0;
	$current_length = 0;
	$text_length = \strlen($text);
	for ($x = 0; $x < $text_length; $x++) {
		$char = \ord($text[$x]);
		if (($char & 0xc0) != 0x80) {
			$current_offset += 1 + ($char >= 0xf0);
			if ($current_offset > $offset) {
				$current_length += 1 + ($char >= 0xf0);
			}
		}
		if ($current_offset > $offset) {
			if ($current_length <= $length) {
				$new_text .= $text[$x];
			}
		}
	}
	return $new_text;
}

function mbStrPos1(string $text, string $needle) {
	$mbPos = strpos($text,$needle);
	if($mbPos !== false) {
		$textTrimmed = substr($text,0,$mbPos);
		$mbPos = mbStrlen1($textTrimmed);
	}
	return $mbPos;
}
