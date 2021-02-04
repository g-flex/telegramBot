# Telegram Bot
##### Basic php webhook setting for Telegram bot
###### This is a quick installation guide. The purpose is to lauch a working Telegram bot in a matter of minutes.
To learn how I set up my server environment, you can head at [this guide](https://github.com/g-flex/linux-ami-setup).
For any doubt take a look at [Telegram API docs](https://core.telegram.org/bots/api).
***

## Quick launch guide
- First of all, [create a new bot](https://core.telegram.org/bots/api).
- Fill `constants/environment_constants.php` with your newly created bot infos.
Don't worry about CREATOR_TELEGRAM_ID now, we will get there later.
- Insert project absolute path in `html/API/hook.php` and move `API/` folder into your web-reachable folder (mine was html/).
- Call `https://api.telegram.org/bot(mytoken)/setWebhook?url=https://(mywebpage)/API/hook.php?api_key=(API_KEY)` to enable webhook. *Returns true on success.*

You are now ready to interact with the bot via Telegram!

***

## Code guide
### Debug
###### Project has 3 different and useful types of debug:
- `DEBUG`: enables basic php error debugging.
- `PRINT_DEBUG`: enables local creation of files with request or response body. You can enable all or choose one. Files are saved in `test/` folder. Remember to give write permission for this folder.
- `LIVE_DEBUG`: sends a message to your `CREATOR_TELEGRAM_ID`.
*If you do not know your Telegram Id, you can enable print debug and send a message to your bot. You will find your Id under **Update** -> **Message** -> **From** -> **Id***.

### Data flow
- Updates from Telegram are firstly handled in `Update()`, then parsed in `Message()`, elaborated and outputted in `Run()`.
- If a command is found, data will pass from `Run()` through ```Command()``` before being outputted. Insert it in `constants/bot_constants.php`>`AVAILABLE_COMMANDS` or it will be rejected as *Command not found*.

If the update is from a callback query, data flow becomes as follows: 
`Update()` -> `CbQuery()` -> Output.
Here, remember that *Output* is (and has to be) a callback query response and not a message. You should then use either `editMessageText` or `sendMessage` to send/edit chat message if triggered by callback query.

### Other useful infos
- **In general, just set `$this->responseText` to respond with a text message** and `$this->responseQueryText` to respond with an [answerCallbackQuery](https://core.telegram.org/bots/api#answercallbackquery).
- **Use `Keyboard()` methods to edit keyboard.** Remember to send a text message along with the keyboard, otherwise your new keyboard will be ignored.
- Since php functions *strlen, substr and strpos* do not count letters as Telegram does (i.e. for *length and offset* specified in entities) for encoding reasons, I found on github some helpers: `mbStrlen1` and `mbSubstr1`. I then obtained `mbStrpos1` from them. You can find them under `/functions/api_helpers.php`.
[Many thanks to danog for these helpers](https://github.com/danog/MadelineProto/blob/master/src/danog/MadelineProto/TL/Conversion/BotAPI.php)
- **You can find a list of emojis** in `constants/emoji_constants.php` which I previously used for a bot. You can either use them or just take a look to search for the same encoding when selecting emojis. I suggest to pick them from [iemoji.com](http://www.iemoji.com/) -> locate your desired emoji -> find UTF8 bytes in table *Current* under the column *php source*.

### Test it
Try sending your bot a private message: it should answer as follows

![New bot launched](https://i.postimg.cc/wxwjzXSB/Screenshot-2021-02-04-at-19-00-40.png)

If the bot does not correctly respond, is time to set DEBUG = true 😆


ENJOY
