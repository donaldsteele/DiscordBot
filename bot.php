#!/usr/bin/env php
<?php

$include_dir = implode(DIRECTORY_SEPARATOR, [__DIR__, 'vendor', 'autoload.php']);

require_once $include_dir;

/* Load our environment configuration */
$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

use shanemcc\discord\DiscordClient;

// Client ID of our bot.
// (Create a new Application at https://discordapp.com/developers/applications/me)
$clientID = $_ENV['client_id'];
$clientSecret = $_ENV['client_secret'];

// Token for our Bot user for the above application.
$token = $_ENV['token'];

// Testing Server and channel. We will send a message here when we start up.
$testServer = '444978993905795073';
$testChannel = '606677830712950796';

// Create the DiscordClient and let the user know how to invite the bot
$client = new DiscordClient($clientID, $clientSecret, $token);
echo 'Discord invite link: https://discordapp.com/oauth2/authorize?client_id=' . $clientID . '&scope=bot&permissions=536931328', "\n";

// If we want to manage our own event loop we can, if we call connect()
// without having given the bot a LoopInterface it will create and start
// one itself.
$loop = React\EventLoop\Factory::create();
try {
    $client->setLoopInterface($loop);
} catch (Exception $e) {
    print_r($e);
}

// Servers are not immediately available when the bot starts up, so wait
// until we get the GUILD_CREATE message for the server we want to send the
// test message to.
//
// This will also mean we will send the message to the channel as soon as we
// are first invited to the server (if we are online) rather than failing to
// send it if we were just trying to send it blind.
$client->on('event.GUILD_CREATE',
    function (DiscordClient $client, int $shard, String $event, Array $data) use ($testServer, $testChannel) {
        // Check if this is the server we want.
        if ($data['id'] == $testServer) {
            // Check if the channel is known on the server.
            if ($client->validChannel($testServer, $testChannel)) {
                echo 'Sending test message.', "\n";

                // Send the message
                $client->sendChannelMessage($testServer, $testChannel, 'Bot Started.');
            }
        }
    });

// We can also respond to things, so lets add an !echo command.
//
// We react whenever a message is created and respond to the same channel
// with a reply.
$client->on('event.MESSAGE_CREATE', function (DiscordClient $client, int $shard, String $event, Array $data) {
    // Don't respond to our own messages.
    if ($data['author']['id'] == $client->getMyInfo()['id']) {
        return;
    }
    $datapacket = new DataPacket($data);

    if ($datapacket->command) {
        $message = Commands::{$datapacket->command}($datapacket);

        if (isset($datapacket->guild_id)) {
            $client->sendChannelMessage($datapacket->guild_id, $datapacket->channel_id, $message);
        } else {
            $client->sendPersonMessage($datapacket->author_id, $message);
        }
    }
});

// Start a connection.
// If we didn't pass a LoopInterface then one will be created and started
// automatically when we do this.
try {
    $client->connect();
} catch (Exception $e) {
    print_r($e);
}

// We manage our own LoopInterface, so start it here.
$loop->run();

class DataPacket
{
    public $command;
    public $message;
    public $guild_id;
    public $author_id;
    public $channel_id;

    function __construct(array $data)
    {
        $bits = explode(' ', $data['content'], 2);
        if (count($bits) < 2) {
            return;
        }

        list($command, $this->message) = $bits;

        if ($command[0] == '!') {
            $command = substr($command, 1);
            if (Commands::hasMethod($command)) {
                $this->command = $command;
            }
        }

        $this->guild_id = $data['guild_id'];
        $this->channel_id = $data['channel_id'];
        $this->author_id = $data['author']['id'];
    }
}

class Commands
{
    static function echo(\DataPacket $dataPacket)
    {
        return self::createReply($dataPacket->author_id, 'said: ' . $dataPacket->message);
    }

    static function createReply($person, $message)
    {
        return sprintf('<@%s> %s', $person, $message);
    }

    static function eightball(\DataPacket $dataPacket)
    {
        $choices = [
            'It is certain',
            'It is decidedly so',
            'Without a doubt',
            'Yes, definitely',
            'You may rely on it',
            'As I see it, yes',
            'Most likely',
            'Outlook good',
            'Yes',
            'Signs point to yes',
            'Reply hazy try again',
            'Ask again later',
            'Better not tell you now',
            'Cannot predict now',
            'Concentrate and ask again',
            'Don\'t count on it',
            'My reply is no',
            'My sources say no',
            'Outlook not so good',
            'Very doubtful',
        ];

        return $choices[array_rand($choices)];
    }

    static function hasMethod($methodName)
    {

        return method_exists(__CLASS__, $methodName);

    }
}