<?php

namespace MartyIX\Utils\Mail;

use Nette,
        Nette\Utils\Strings;

class AddHelperMacro extends \Nette\Latte\Macros\MacroSet {

	public static function filter(\Nette\Latte\MacroNode $node, $writer)
	{
                $return = '$__template->registerHelper('.$node->args.');';
		return $return;
	}


	public static function install(\Nette\Latte\Parser $parser)
	{
                $me = new static($parser);
		$me->addMacro('addHelper', array($me, "filter"));
	}

}

