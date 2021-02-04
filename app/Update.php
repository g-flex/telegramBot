<?php

class Update {

	public $TYPE_CALLBACK_QUERY = 'callback_query';
	public $TYPE_MESSAGE = 'message';
	public $TYPE_EDITED_MESSAGE = 'edited_message';
	public $TYPE_CHANNEL = 'channel_post';
	public $TYPE_POLL = 'poll';
	public $TYPE_POLL_ANSWER = 'poll_answer';
	public $TYPE_EDITED_CH_POST = 'edited_channel_post';
	public $TYPE_INLINE_QUERY = 'inline_query';
	public $TYPE_SHIPPING_QUERY = 'shipping_query';
	public $TYPE_PRE_CHECKOUT = 'pre_checkout_query';

	public $TYPES = [];

	public $update;

	function __construct() {

		$content = file_get_contents("php://input");
		$this->update = json_decode($content, true);

		//DEBUG
		if(PRINT_DEBUG === true || PRINT_REQUEST_DEBUG === true) {
			$fi = new FilesystemIterator(PATH_TEST."request/", FilesystemIterator::SKIP_DOTS);
			file_put_contents(PATH_TEST."request/request".iterator_count($fi).".json", $content);
		}

		$this->initSession();

		//EACH OF THESE FNS CHECKS THE EXISTENCE OF THE MAIN PARAMS AND
		//REDIRECTS TO THE PROPER OBJECT
		if(isset($this->update[$this->TYPE_CALLBACK_QUERY])) {
			$this->handleCallbackQuery();
		} else if(isset($this->update[$this->TYPE_CHANNEL])) {
			$this->handleChannelPost();
		} else if(isset($this->update[$this->TYPE_MESSAGE])) {
			$this->handleMesage();
		} else {
			throw new Exception(ERR_MISSING_PARAMS."2");
		}

	}

	public function handleCallbackQuery() {

		$cbQuery = $this->update['callback_query'];
		$checkParams = isset($cbQuery['data']);
		$checkParams = ($checkParams && isset($cbQuery['from']['id']) && is_numeric($cbQuery['from']['id']));
		$checkParams = ($checkParams && isset($cbQuery['message']['message_id']) && is_numeric($cbQuery['message']['message_id']));
		$checkParams = ($checkParams && isset($cbQuery['message']['chat']['id']) && is_numeric($cbQuery['message']['chat']['id']));
		$checkParams = ($checkParams && isset($cbQuery['message']['reply_to_message']['from']['id']) && is_numeric($cbQuery['message']['reply_to_message']['from']['id']));
		if(!$checkParams) {
			throw new Exception(ERR_MISSING_PARAMS."3");
		} else {
			new CbQuery($cbQuery);
		}

	}

	public function handleChannelPost() {

		$checkParams = (isset($this->update['channel_post']['chat']['id']));
		$checkParams = ($checkParams && isset($this->update['update_id']) && is_numeric($this->update['update_id']));
		$checkParams = ($checkParams && isset($this->update['channel_post']['chat']['id']) && is_numeric($this->update['channel_post']['chat']['id']));
		if($checkParams) {

			//IN MY USE CASE, I DIDNT WANT MY BOT TO BE ADDED TO CHANNELS
			curlPost(URL_LEAVE_CHAT, ['chat_id'=>$this->update['channel_post']['chat']['id']]);
			http_response_code(204);
			exit;

		} else {
			throw new Exception(ERR_MISSING_PARAMS."4");
		}

	}

	public function handleMesage() {

		$checkParams = (isset($this->update['message']) && isset($this->update['message']['from']['id']) && isset($this->update['message']['chat']['id']));
		$checkParams = ($checkParams && is_numeric($this->update['message']['from']['id']) && is_numeric($this->update['message']['chat']['id']));

		if($checkParams) {

			new Message($this->update);

		} else {
			throw new Exception(ERR_MISSING_PARAMS."1");
		}

	}

	public function initSession() {

		//SET ACCEPTED TYPES, IGNORE OTHER TYPES GENTLY
		$this->TYPES = [$this->TYPE_EDITED_MESSAGE, $this->TYPE_MESSAGE, $this->TYPE_CALLBACK_QUERY];
		if(empty($this->update) || !is_array($this->update) || empty(array_intersect(array_keys($this->update), $this->TYPES))) {
			http_response_code(204);
			exit;
		}

	}

}