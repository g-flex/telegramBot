<?php

class Run {

	public $entities = [];
	public $parseMode = false;
	public $responseKeyboard = false;
	public $replyTo = false;

	function __construct($message) {

		$this->importProps($message);

		if(!$this->skipHandling) {
			$this->handleSession();
		}
		$this->endSession();

	}

	public function handleSession() {

		if($this->hasCommands()) {
			$command = new Command($this->message);
			$this->handleCommandResponse($command->getResponse());
		}

	}
	public function handleCommandResponse($command) {

		if(isset($command['responseText'])) {
			$this->responseText = $command['responseText'];
		}
		if(isset($command['parseMode'])) {
			$this->parseMode = $command['parseMode'];
		}
		if(isset($command['replyTo'])) {
			$this->replyTo = $command['replyTo'];
		}
		if(isset($command['responseKeyboard'])) {
			$this->responseKeyboard = $command['responseKeyboard'];
		}
		if(isset($command['disableWebPagePreview'])) {
			$this->disableWebPagePreview = 1;
		}

	}
	public function endSession() {

		$payload = $this->buildResponse();
		if(!empty($this->responseText)) {
			http_response_code(200);
			header('Content-Type: application/json');
			echo $payload;
		} else {
			http_response_code(204);
		}

		if(LIVE_DEBUG) {
			curlPost(URL_SEND, ['chat_id' => CREATOR_TELEGRAM_ID, 'parse_mode' => PARSE_MODE, 'text' => "<pre><code>".json_encode($this->update, JSON_PRETTY_PRINT)."</code></pre>"]);
		}
		if(PRINT_DEBUG === true || PRINT_RESPONSE_DEBUG === true) {
			$fi = new FilesystemIterator(PATH_TEST."response/", FilesystemIterator::SKIP_DOTS);
			file_put_contents(PATH_TEST."response/response".iterator_count($fi).".json", $payload);
		}
		exit;

	}
	public function buildResponse() {

		$response['text'] = $this->responseText;
		$response['chat_id'] = $this->chatId;
		$response['method'] = METHOD_SEND;
		if($this->parseMode) {
			$response["parse_mode"] = $this->parseMode;
		}
		if($this->replyTo) {
			$response["reply_to_message_id"] = $this->message['message_id'];
		}
		if(isset($this->disableWebPagePreview)) {
			$response["disable_web_page_preview"] = 1;
		}
		if($this->responseKeyboard) {
			$response["reply_markup"] = $this->responseKeyboard;
		}

		return json_encode($response);

	}
	public function importProps(Message $message) {

		foreach(get_object_vars($message) as $key => $value) {
			$this->$key = $value;
		}

	}
	public function hasCommands() {

		if($this->hasEntities()) {
			foreach($this->entities as $entity) {
				if($entity['type'] === 'bot_command') {
					return true;
				}
			}
		}
		return false;

	}
	public function hasEntities() {

		if(isset($this->message['text']) && isset($this->message['entities'])) {
			$this->entities = $this->message['entities'];
			return true;
		}
		return false;

	}

}
