<?php

namespace DiscordBot;

class Commands
{
    static function echo(DataPacket $dataPacket)
    {
        return 'said: ' . $dataPacket->message;
    }

    static function eightball(DataPacket $dataPacket)
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