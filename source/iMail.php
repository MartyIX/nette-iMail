<?php

namespace MartyIX\Utils\Mail;

use Nette;
use MartyIX;

/**
 * Implementation of more advanced template based email sending
 *
 * @author Martin Vseticka
 * @link http://forum.nette.org/cs/7881-kompletni-sablona-pro-email
 * @link http://forum.nette.org/cs/7881-kompletni-sablona-pro-email#p59885
 */
class iMail
{

        private $template;
        private $from;
        private $to;
        private $subject;
        private $attachments = array();
        private $templateParams;
        private $templateFile;

        /**
         * Get & Set helper loader class
         *
         * @link http://api.nette.org/2.0/source-Templating.Template.php.html#209  registerHelperLoader method
         * @var string
         */
        public static $helperClass = '\MartyIX\Utils\Helpers';

        public function getFrom()
        {
                return $this->from;
        }

        public function setFrom($from)
        {
                $this->from = $from;
                return $this;
        }

        public function getTo()
        {
                return $this->to;
        }

        public function setTo($to)
        {
                $this->to = $to;
                return $this;
        }

        public function getSubject()
        {
                return $this->subject;
        }

        public function setSubject($subject)
        {
                $this->subject = $subject;
                return $this;
        }

        public function getTemplateFile()
        {
                return $this->templateFile;
        }

        public function setTemplateFile($templateFile)
        {
                $this->templateFile = $templateFile;
        }

        public function addAttachment($file, $content = NULL, $contentType = NULL)
        {
                $this->attachments[] = array($file, $content, $contentType);
        }

        /**
         * Get email attachment by order of insertion.
         *
         * @param int $index  zero-based
         * @return boolean
         */
        public function getAttachment($index)
        {
                return isset($this->attachments[$index]) ? $this->attachments[$index] : null;
        }

        private function helperLoader($helper)
        {
                $callback = callback(self::$helperClass, $helper);
                if ($callback->isCallable()) {
                        return $callback;
                }
        }

        /**
         * Set the email template and parameters to be passed to the template
         *
         * @param string $templateFile
         * @param array $templateParams
         * @return iMail
         */
        public function setTemplate($templateFile, $templateParams)
        {
                $this->templateFile = $templateFile;
                $this->templateParams = $templateParams;
                return $this;
        }

        private function prepareTemplate()
        {
                $engine = new \Nette\Latte\Engine();
                MartyIX\Utils\Mail\HeadersMacro::install($engine->parser);
                MartyIX\Utils\Mail\MessageMacro::install($engine->parser);
                MartyIX\Utils\Mail\RequireMacro::install($engine->parser);
                MartyIX\Utils\Mail\AddHelperMacro::install($engine->parser);

                $this->template = new Nette\Templating\FileTemplate($this->templateFile);
                $this->template->registerFilter($engine);

                if (self::$helperClass) {
                        $helperClass = self::$helperClass;
                        $this->template->registerHelperLoader(function ($helper) use ($helperClass) {
                                $callback = callback($helperClass, $helper);
                                if ($callback->isCallable()) {
                                        return $callback;
                                }
                        });
                }

                $arr = array(
                    'from' => $this->from,
                    "to" => $this->to,
                    "subject" => $this->subject,
                );

                $this->template->setParams(array_merge($this->templateParams, $arr));
                $this->template->__message = new Nette\Mail\Message;
                $this->template->__template = $this->template;

                return $this->template;
        }


        /**
         * Get final instance of Nette\Mail\Message. Preferred method for sending is method Send().
         *
         * @return Nette\Mail\Message
         */
        public function getGeneratedMessage()
        {
                # Force the template to render
                (string) $this->prepareTemplate();

                $m = $this->template->__message;

                if (!$m->getFrom()) {
                        trigger_error("You should add 'from' information.", E_USER_WARNING);
                }

                if (!$m->getSubject()) {
                        trigger_error("You should add 'subject'.", E_USER_WARNING);
                }

                if (!$m->getHeader('To')) {
                        trigger_error("You should add recipients.", E_USER_WARNING);
                }

                foreach ($this->attachments as $attachment) {
                        list($filename, $content, $type) = $attachment;
                        $m->addAttachment($filename, $content, $type);
                }

                return $m;
        }

        /**
         * Send email.
         *
         * @return iMail
         */
        public function send()
        {
                $this->getGeneratedMessage()->send();
                return $this;
        }

}