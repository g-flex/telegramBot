<?php

class Keyboard {

	public static function setInline($buttonsArray, $rowSize = 1, $isCurlRequest = false) {

		$keyboard = ['inline_keyboard' => array(array(array()))];
		if(!empty($buttonsArray)) {
			$rowCount = 0;
			$currentRow = 0;
			foreach ($buttonsArray as $buttonArray) {

				if(count($buttonArray) < 2) {
					$buttonArray[1] = $buttonArray[0];
				}
				$button['text'] = $buttonArray[0];
				$button['callback_data'] = http_build_query($buttonArray[1]);
				$keyboard['inline_keyboard'][$currentRow][$rowCount] = $button;
				$rowCount += 1;
				if($rowCount === $rowSize) {
					$rowCount = 0;
					$currentRow += 1;
				}

			}
		} else {
			//response text cant be empty!!
			$keyboard = ['remove_keyboard' => true];
		}

		return $isCurlRequest ? json_encode($keyboard) : $keyboard;

	}

	public static function setBlock($buttonsArray, $oneTime = true) {

		$keyboard['resize_keyboard'] = true;
		$keyboard['one_time_keyboard'] = $oneTime;
		$keyboard['keyboard'] = $buttonsArray;

		return $keyboard;

	}

	public static function remove() {

		$keyboard['remove_keyboard'] = true;
		return $keyboard;

	}


}