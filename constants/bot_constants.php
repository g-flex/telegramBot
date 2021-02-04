<?php

define("BOT_API", "https://api.telegram.org/bot" . BOT_TOKEN.'/');

define("METHOD_SEND", "sendMessage");
define("METHOD_LEAVE", "leaveChat");
define("METHOD_GET_MEMBERS", "getChatMembersCount");
define("METHOD_EDIT_MESSAGE_KEYBOARD", "editMessageReplyMarkup");
define("METHOD_EDIT_MESSAGE_TEXT", "editMessageText");
define("METHOD_ANSWER_QUERY", "answerCallbackQuery");

define("URL_SEND", BOT_API.METHOD_SEND);
define("URL_LEAVE_CHAT", BOT_API.METHOD_LEAVE);
define("URL_MEMBERS_COUNT", BOT_API.METHOD_GET_MEMBERS);
define("URL_EDIT_MESSAGE_KEYBOARD", BOT_API.METHOD_EDIT_MESSAGE_KEYBOARD);
define("URL_EDIT_MESSAGE", BOT_API.METHOD_EDIT_MESSAGE_TEXT);
define("URL_ANSWER_QUERY", BOT_API.METHOD_ANSWER_QUERY);

//COMMANDS
define("COMMAND_HELP", '/help');
define("COMMAND_START", '/start');
define("COMMAND_TARGET", '/target');
define("COMMAND_QUERY", '/query');

define("AVAILABLE_COMMANDS", [COMMAND_HELP, COMMAND_START, COMMAND_TARGET, COMMAND_QUERY]);
define("TARGET_COMMANDS", [COMMAND_TARGET]);