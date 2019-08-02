<?php

use DiscordBot\Commands;
use DiscordBot\DataPacket;
use React\EventLoop\Factory;

$include_dir = implode(DIRECTORY_SEPARATOR, [__DIR__, 'vendor', 'autoload.php']);

require_once $include_dir;


/* Load our environment configuration */
$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

$loop = Factory::create();
$client = new \CharlotteDunois\Yasmin\Client(array(
    'ws.disabledEvents' => array(
        /* We disable the TYPING_START event to save CPU cycles, we don't need it here in this example. */
        'TYPING_START'
    )
), $loop);


$client->on('message', function ($message) use ($client) {
    try {
        /**
         * @var $message CharlotteDunois\Yasmin\Models\Message
         */
        $datapacket = new DataPacket($client, $message);

        if ($datapacket->command) {
            $response = Commands::{$datapacket->command}($datapacket);
            print ' responding to ' . $datapacket->command . "\n";
            if (gettype($response) == 'object') {
                print "its an object \n";

                $message->reply('', array('embed' => $response))
                    ->otherwise(function ($error) {
                        echo $error . PHP_EOL;
                    });

            } else {
                print "its a string \n";
                $message->reply($response)->otherwise(function ($error) {
                    echo $error . PHP_EOL;
                });
            }
        }
    } catch (\Exception $error) {
        // Handle exception
    }
});

$client->login($_ENV['token']);
$loop->run();
