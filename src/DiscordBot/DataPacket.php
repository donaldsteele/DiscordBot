<?php

namespace DiscordBot;

use CharlotteDunois\Yasmin\Client;
use CharlotteDunois\Yasmin\Models\Message;

class DataPacket
{
    public $command;
    public $message;
    public $guild_id;
    public $author_id;
    public $channel_id;

    function __construct(Client $client, Message $message)
    {


        $bits = preg_split('/ /', $message, -1, PREG_SPLIT_NO_EMPTY);
        //print_r($bits);
        /* if (count($bits) < 2) {
             return;
         }
 */

        $command = array_shift($bits);
        $this->message = implode(" ", $bits);
        print_r($this->message);

        if ($command[0] == '!') {
            $command = substr($command, 1);
            if (Commands::hasMethod($command)) {
                $this->command = $command;
            }
        }

        $this->guild_id = $message->guild;
        $this->channel_id = $message->channel;
        $this->author_id = $message->author;
    }


}
