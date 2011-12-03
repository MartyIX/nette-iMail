<?php

namespace MartyIX\Utils\Mail;

use Nette,
        Nette\Utils\Strings;

class HeadersMacro extends \Nette\Latte\Macros\MacroSet {

	public static function header(\Nette\Latte\MacroNode $node, $writer)
	{
                if (!is_string($node->args)) {
                        throw new \Nette\InvalidArgumentException("Argument should be a string!");
                }

                $addresses = explode(',', $node->args);
                $return = '';
                $nodeName = strtolower($node->name);

                switch ($nodeName) {
                        case 'subject':
                                $method = 'setSubject';
                                break;
                        case 'from':
                                $method = 'setFrom';
                                break;
                        case 'to':
                        case 'bcc':
                        case 'cc':
                                $method = 'add' . Strings::Capitalize($nodeName);
                                break;
                        default:
                                throw new Nette\InvalidArgumentException("Unknown mail parameter `{$node->name}'");
                                break;
                }

                foreach ($addresses as $address) {
                        $return .= '$__message->'.$method.'('.$address.'); ';
                }

		return $return;
	}


	public static function install(\Nette\Latte\Parser $parser)
	{
                $me = new static($parser);
		$me->addMacro('to', array($me, "header"));
                $me->addMacro('cc', array($me, "header"));
                $me->addMacro('bcc', array($me, "header"));
                $me->addMacro('from', array($me, "header"));
                $me->addMacro('subject', array($me, "header"));
	}

}

