<?php

define("ERR_IMPROPER_ACTION", EMOJI_ALERT." This was not your command ".EMOJI_ALERT);
define("ERR_COMMAND_NOT_FOUND", EMOJI_EMBARASSED." Command [0] not found ".EMOJI_EMBARASSED);
define("ERR_TOO_MANY_COMMANDS", EMOJI_SCREAM." Too many commands ".EMOJI_SCREAM);
define("ERR_TOO_MANY_MENTIONS", EMOJI_SCREAM." Too many mentions ".EMOJI_SCREAM);

//todo good responses
define("TITLE", "<b>Welcome to Plain Telegram Bot!</b>\n\n");
define("HELP", TITLE."Telegram bot Github repository for php webhook.\n<a href='https://instagram.com/gurgamezcla'>Follow my father on Instagram</a>, <a href='https://twitter.com/0xMrln'>Twitter</a> or <a href='https://github.com/g-flex'>Github</a>.");

define("WELCOME", "This is the welcome message.");
define("PRIVATE_RESP", TITLE."<b>This is a private chat message.</b>");

define("TAG_HTML", "<a href='tg://user?id=[0]'>[1]</a>");
define("BUTTON_0", ["Top notification".EMOJI_RACE_FLAG, [CBQ_KEY_0=>1]]);
define("BUTTON_1", ["Only sender allowed".EMOJI_TARGET, [CBQ_KEY_1=>1]]);

