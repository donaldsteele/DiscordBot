<?php

namespace DiscordBot;

use DOMXPath;

class Commands
{
    static function echo(DataPacket $dataPacket)
    {
        return 'said: ' . $dataPacket->message;
    }


    static function meme(DataPacket $dataPacket)
    {
        $meme = self::randomMeme();
        print_r($meme);
        $embed = new \CharlotteDunois\Yasmin\Models\MessageEmbed();
        $embed
            ->setTitle('Fresh Meme')// Set a title
            ->setColor(random_int(0, 16777215))// Set a color (the thing on the left side)
            ->setImage('https:' . $meme[0])// Set an image (below everything except footer)
            ->setTimestamp()// Set a timestamp (gets shown next to footer)
            ->setFooter('yaa yeet!');                               // Set a footer without icon
        return $embed;
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


    static function compcal(DataPacket $dataPacket)
    {

        $embed = new \CharlotteDunois\Yasmin\Models\MessageEmbed();
        $embed
            ->setTitle('Official Cyber Patriot Calendar')// Set a title
            ->setColor(random_int(0, 16777215))// Set a color (the thing on the left side)
            ->setImage('https://www.uscyberpatriot.org/SiteCollectionImages/Competition/Schedule_CP12_190707.png')// Set an image (below everything except footer)
            ->setTimestamp()// Set a timestamp (gets shown next to footer)
            ->setFooter('yaa yeet!');                               // Set a footer without icon
        return $embed;

    }

    static function nextcomp(DataPacket $dataPacket)
    {

        $compdates = array
        (
            '0' => "2019-10-25",
            '1' => "2019-11-15",
            '2' => "2019-12-06",
            '3' => "2020-01-24",
            '4' => "2020-09-27",
        );
        //$count = 0;
        foreach ($compdates as $compday) {
            //$interval[$count] = abs(strtotime($date) - strtotime($day));
            $interval[] = abs(strtotime($compday) - strtotime("now"));
            print abs(strtotime($compday) - strtotime("now")) . "\n";
            //$count++;
        }

        asort($interval);
        $closest = $compdates[key($interval)];
        return 'the next closest competition date is ' . $closest;
    }


    static function hasMethod($methodName)
    {

        if (gettype($methodName) === 'string') {
            return method_exists(__CLASS__, $methodName);
        }

    }


    static private function randomMeme()
    {
        $url = "https://imgflip.com/";
        $html = file_get_contents($url);
        $dom = new \DOMDocument();
        $dom->loadHTML($html);
        $xpath = new DOMXPath($dom);
        $tds = $xpath->query('//*[@id="base-left"]/div[position()>0]/div[1]/div/a');
        $out = array();
        foreach ($tds as $td) {
            $img = self::walkNode($td->firstChild);
            if ($img) {
                $out[] = $img;
            }
        }
        return ($out[array_rand($out)]);
    }

    static private function walkNode($node)
    {
        $str = array();
        /*    if($node->nodeType==XML_TEXT_NODE)
            {
                if (trim($node->nodeValue) != '') {
                $str[]=$node->nodeValue;
                }
            }
          */
        /*else*/
        if (strtolower($node->nodeName) == "img") {
            /* This is just a demonstration;
             * You'll have to extract the info in the way you want
             * */
            $str[] = $node->attributes->getNamedItem("src")->nodeValue;
        }
        if ($node->firstChild) $str = array_merge(self::walkNode($node->firstChild), $str);
        if ($node->nextSibling) $str = array_merge(self::walkNode($node->nextSibling), $str);

        return $str;

    }

}