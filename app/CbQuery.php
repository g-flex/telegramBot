<?php

class CbQuery {

	public $cbQuery;
	public $message;
	public $from;
	public $chat;
	public $fromId;
	public $fromUsername;
	public $fromName;
	public $fromFirstName;
	public $groupName;
	public $messageFromId;
	public $chatId;
	public $data = [];
	public $messageId;
	public $date;

	public $action;

	public $responseQueryText = null;
	public $responseText = null;
	public $showAlert = false;

	public function __construct($cbQuery) {

		$this->init($cbQuery);
		$this->handleCallBackQuery();

	}

	private function init($cbQuery) {

		$this->cbQuery = $cbQuery;
		$this->from = $cbQuery['from'];
		$this->fromId = $cbQuery['from']['id'];
		$this->fromFirstName = $cbQuery['from']['first_name'];
		$this->fromUsername = isset($cbQuery['from']['username']) ? $cbQuery['from']['username'] : null;
		$this->fromName = isset($cbQuery['from']['username']) ? '@' . $cbQuery['from']['username'] : $cbQuery['from']['first_name'];
		$this->message = $cbQuery['message'];
		$this->messageId = $cbQuery['message']['message_id'];
		$this->date = $cbQuery['message']['date'];
		$this->chat = $cbQuery['message']['chat'];
		$this->chatId = $cbQuery['message']['chat']['id'];
		$this->groupName = isset($this->chat['title']) ? $this->chat['title'] : null;
		$this->messageFromId = $cbQuery['message']['reply_to_message']['from']['id'];

		parse_str($cbQuery['data'], $this->data);
		foreach ($this->data as $dataKey => $value) {

			$this->action = strval($dataKey);
			if ($dataKey !== CBQ_KEY_0 && $dataKey !== CBQ_KEY_1) {
				throw new Exception(ERR_WRONG_CALLBACK_QUERY);
			}

		}
	}
	public function handleCallBackQuery() {

		if ($this->action === CBQ_KEY_0) {
			$this->responseQueryText = CBQ_KEY_0." query received.";
		} else {

			if ($this->fromOriginalSender()) {

				$this->responseText = "Sender query received.";

			} else {
				$this->responseQueryText = ERR_IMPROPER_ACTION;
				$this->showAlert = true;
			}

		}
		$this->endCallbackSession();
		exit;
	}

	private function rebuildMentionedText() {
		$mentions = [];
		foreach ($this->message['entities'] as $entity) {
			if ($entity['type'] === "text_mention") {
				array_push($mentions, $entity);
			}
		}
		$text = $this->message['text'];
		$offsetIncrement = 0;
		foreach ($mentions as $entity) {

			$mention = Strings::build(TAG_HTML, [$entity['user']['id'], $entity['user']['first_name']]);
			$offset = intval($entity['offset']) + $offsetIncrement;
			$length = intval($entity['length']);
			$textLength = mbStrlen1($text);
			$firstPart = mbSubstr1($text, 0, $offset);
			$secondPart = mbSubstr1($text, $offset + $length, $textLength - ($offset + $length));
			$text = $firstPart . $mention . $secondPart;
			$offsetIncrement += mbStrlen1($mention) - $length;

		}
		return $text;
	}
	public function fromOriginalSender() {
		return $this->messageFromId === $this->fromId;
	}

	public function endCallbackSession() {

		$payload = $this->buildResponseCallbackQuery();
		http_response_code(200);
		header('Content-Type: application/json');

		//REMEMBER TO ALWAYS RESPOND TO THE CBQUERY TO
		//AVOID INFINITE LOADER LOOP IN TELEGRAM GUI
		echo $payload;

		if (!empty($this->responseText)) {
			$this->editMessageText($this->chatId, $this->messageId, $this->responseText);
		}
		if (LIVE_DEBUG || PRINT_DEBUG || PRINT_RESPONSE_DEBUG) {
			$newPayload = json_encode(json_decode($payload), JSON_PRETTY_PRINT);
		}
		if (LIVE_DEBUG) {
			curlPost(URL_SEND, ['chat_id' => CREATOR_TELEGRAM_ID, 'parse_mode' => PARSE_MODE, 'text' => "<pre><code>".$newPayload."</code></pre>"]);
		}
		if (PRINT_DEBUG || PRINT_RESPONSE_DEBUG) {
			$fi = new FilesystemIterator(PATH_TEST."response/", FilesystemIterator::SKIP_DOTS);
			file_put_contents(PATH_TEST."response/response" . iterator_count($fi) . ".json", $newPayload);
		}
		exit;
	}
	public function buildResponseCallbackQuery() {
		$response['method'] = METHOD_ANSWER_QUERY;
		$response['callback_query_id'] = $this->cbQuery['id'];
		if ($this->showAlert) {
			$response['show_alert'] = $this->showAlert;
		}
		if ($this->responseQueryText) {
			$response['text'] = $this->responseQueryText;
		}
		return json_encode($response);
	}
	public function editMessageText($chatId, $messageId, $text, $keyboard = false, $parseMode = false) {

		$response['chat_id'] = $chatId;
		$response['message_id'] = $messageId;
		$response['text'] = $text;
		if ($keyboard !== false) {
			$response['reply_markup'] = $keyboard;
		}
		if ($parseMode !== false) {
			$response['parse_mode'] = PARSE_MODE;
		}

		return $response;

	}

}
