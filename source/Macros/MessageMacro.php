<?php

namespace MartyIX\Utils\Mail;

use Nette,
    Nette\Utils\Strings;

class MessageMacro extends \Nette\Latte\Macros\MacroSet
{
        public static function install(\Nette\Latte\Parser $parser)
        {
                $me = new static($parser);
                //$me->addMacro('message', array($me, "message"));
                $me->addMacro(
                        'message',
                        'ob_start();',
                        '$__message->setHtmlBody(ob_get_clean());'
                );
        }

}

