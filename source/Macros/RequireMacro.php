<?php

namespace MartyIX\Utils\Mail;

use Nette,
        Nette\Utils\Strings;

class RequireMacro extends \Nette\Latte\Macros\MacroSet {

	public static function filter(\Nette\Latte\MacroNode $node, $writer)
	{
                if (!is_string($node->args)) {
                        throw new \Nette\InvalidArgumentException("Argument should be a string!");
                }

                $vars = explode(',', $node->args);
                $return = '$__templateParams = $__template->getParams();';

                foreach ($vars as $var) {

                        $var = trim($var, "\n\r ");
                        $typeCheck = '';
                        $specs = explode(" ", $var);

                        if (count($specs) == 2) {
                                list($type, $var) = $specs;

                                $typeCheck = 'if (!is_'.$type.'($__templateParams["'.$var.'"])) {
                                        throw new \Nette\Templating\FilterException("Type of template parameter `'.$var.'\' is not: '.$type.'!");
                                }';
                        }

                        $return .= 'if (!isset($__templateParams["'.$var.'"])) {
                                        throw new \Nette\Templating\FilterException("Template parameter `'.$var.'\' is not set!");
                                        //trigger_error("Template parameter '.$var.' is not set!", E_USER_ERROR);
                                    } else {
                                        '.$typeCheck.'
                                    }; ';


                }

		return $return;
	}


	public static function install(\Nette\Latte\Parser $parser)
	{
                $me = new static($parser);
		$me->addMacro('require', array($me, "filter"));
	}

}

