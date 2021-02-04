<?php

class Strings {

	public static function build($phrase, $params = []) {

		if (gettype($phrase) === "string") {
			$stringValue = $phrase;
		} else {
			$stringValue = $phrase[array_rand($phrase)];
		}

		$paramsNum = count($params);
		if ($paramsNum > 0) {
			for ($i = 0; $i < $paramsNum; $i++) {
				$stringValue = str_replace("[" . $i . "]", $params[$i], $stringValue);
			}
		}

		return $stringValue;

	}

}