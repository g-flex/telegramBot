<?php

class Message {

	public $message;
	public $update;
	public $from;
	public $chat;
	public $type;
	public $chatId;
	public $telegramId;
	public $username;
	public $firstName;
	public $groupName;
	public $date;

	public $skipHandling = false;
	public $responseText = '';
	public $checkMembersCount = false;

	function __construct($update) {

		$this->initSession($update);
		$this->checkMessageAction();
		new Run($this);

	}

	public function initSession($update) {

		$this->update = $update;

		$this->message = $this->update['message'];
		$this->from = $this->message['from'];
		$this->chat = $this->message['chat'];
		$this->date = $this->message['date'];
		$this->chatId = $this->chat['id'];
		$this->type = $this->chat['type'];
		$this->telegramId = $this->from['id'];
		$this->firstName = $this->from['first_name'];
		$this->username = isset($this->from['username']) ? $this->from['username'] : null;
		$this->groupName = isset($this->chat['title']) ? $this->chat['title'] : null;

	}

	public function checkMessageAction() {

		if($this->type === "private") {
			$this->responseText = PRIVATE_RESP;
			$this->parseMode = PARSE_MODE;
			$this->skipHandling = true;
		}
		if(isset($this->message['left_chat_member']['id'])) {
			$this->responseText = "Someone just left the chat.";
			$this->skipHandling = true;
		}
		if(isset($this->message['group_chat_created']) || (isset($this->message['new_chat_member']) && $this->message['new_chat_member']['id'] === BOT_ID) || (isset($this->message['new_chat_participant']) && $this->message['new_chat_participant']['id'] === BOT_ID)) {
			$this->responseText = WELCOME;
			$this->skipHandling = true;
		}

	}

}