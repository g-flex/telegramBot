<?php

class Command {

	public $TYPE_MENTION = "mention";
	public $TYPE_TEXT_MENTION = "text_mention";
	public $TYPE_COMMAND = "bot_command";

	public $message;
	public $entities = [];
	public $text;
	public $command;
	public $mention;
	public $targetUser;

	public function __construct($message) {

		$this->message = $message;
		if(isset($message['entities'])) {
			$this->entities = $message['entities'];
			if(isset($message['text'])) {
				$this->text = $message['text'];
				$this->init();
			} else {
				throw new Exception(ERR_MISSING_PARAMS."6");
			}
		}

	}

	public function init() {

		//returns false if has more than 1 command
		if($this->canFulfill()) {

			if (in_array($this->command, AVAILABLE_COMMANDS)) {

				if(in_array($this->command, TARGET_COMMANDS)) {

					$this->checkForTarget();
					if(!empty($this->mention)) {
						$this->responseText = "Received targeted command.";
					} else if($this->mention === false) {
						$this->responseText = "Received generic command.";
					} else {
						$this->responseText = ERR_TOO_MANY_MENTIONS;
						$this->responseKeyboard = Keyboard::remove();
					}

				} else {
					$this->handleStraightCommand();
				}

			} else {
				$this->responseText = Strings::build(ERR_COMMAND_NOT_FOUND, [$this->command]);
				$this->responseKeyboard = Keyboard::remove();
			}
		} else {
			$this->responseText = ERR_TOO_MANY_COMMANDS;
			$this->responseKeyboard = Keyboard::remove();
		}

	}
	public function handleStraightCommand() {

		if($this->command === COMMAND_HELP || $this->command === COMMAND_START) {
			$this->responseText = HELP;
			$this->parseMode = PARSE_MODE;
			$this->disableWebPagePreview = 1;
		} else if ($this->command === COMMAND_QUERY) {
			$this->responseText = "Command received: ".$this->command;
			$this->responseKeyboard = Keyboard::setInline([BUTTON_0, BUTTON_1]);
		}

	}

	public function canFulfill() {

		$hasCommand = false;
		foreach ($this->entities as $entity) {

			if($entity['type'] === $this->TYPE_COMMAND) {
				if(!$hasCommand) {
					$command = $this->parseEntity($entity);
					if (mbSubstr1($command, -BOT_USERNAME_LENGTH) === BOT_USERNAME) {
						$command = mbSubstr1($command, 0, $entity['length'] - BOT_USERNAME_LENGTH);
					}
					$this->command = strtolower($command);
					$hasCommand = true;
				} else {
					return false;
				}

			}

		}
		return true;

	}
	public function checkForTarget() {

		$setMention = false;
		$mention = false;
		foreach ($this->entities as $entity) {

			if($entity['type'] === $this->TYPE_MENTION) {
				//first char is @
				$mention = substr($this->parseEntity($entity), 1);
				$setMention = $setMention === false ? true : null;
			} else if($entity['type'] === $this->TYPE_TEXT_MENTION) {
				$mention = $this->parseEntity($entity);
				$setMention = $setMention === false ? true : null;
				if(isset($entity['user'])) {
					$this->targetUser = $entity['user'];
				} else {
					throw new Exception(ERR_MISSING_PARAMS."7");
				}
			}

		}

		if(!is_null($setMention)) { $this->mention = $mention; }

	}
	public function parseEntity($entity) {

		if(isset($entity['length']) && isset($entity['offset']) && isset($entity['type'])) {
			return mbSubstr1($this->text, $entity['offset'], $entity['length']);
		} else {
			throw new Exception(ERR_MISSING_PARAMS."5");
		}


	}
	public function getResponse() {

		$response = [];
		if(isset($this->responseText)) {
			$response['responseText'] = $this->responseText;
		}
		if(isset($this->parseMode)) {
			$response['parseMode'] = $this->parseMode;
		}
		if(isset($this->replyTo)) {
			$response['replyTo'] = $this->replyTo;
		}
		if(isset($this->responseKeyboard)) {
			$response['responseKeyboard'] = $this->responseKeyboard;
		}
		if(isset($this->disableWebPagePreview)) {
			$response["disableWebPagePreview"] = 1;
		}
		return $response;

	}

}