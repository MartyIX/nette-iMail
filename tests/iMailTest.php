<?php

require __DIR__ . '/Mail.inc';

class iMailTest extends \MartyIX\Tests\TestCase
{

        public static function dummyParenthesisHelper($s)
        {
                return "($s)";
        }

        protected function getMailInstance($templateFile, $params)
        {
                $iMail = new \MartyIX\Utils\Mail\iMail();
                $m = $iMail->setTemplate(__DIR__ . '/' . $templateFile, $params)
                        ->setFrom('no-reply@fluid.com')
                        ->setTo('fluid@fluid.com')
                        ->setSubject('News from ...')
                        ->getGeneratedMessage();

                return $m;
        }

        public function testSimpleMail()
        {
                $iMail = new \MartyIX\Utils\Mail\iMail();
                $iMail->setTemplate(__DIR__ . '/Mail001.latte', array());

                $m = $iMail->getGeneratedMessage();
                $this->assertTrue(is_array($m->getFrom()) && count($m->getFrom()) == 1);
                $this->assertEquals($m->getFrom(), array('no-reply@company.com' => null));
                $this->assertEquals($m->getHeader('To'), array('customerA@companyA.com' => NULL, 'customerB@companyB.com' => NULL));
                $this->assertEquals($m->getSubject(), "News letter");
        }

        public function testIncludedContent()
        {
                $m = $this->getMailInstance('Mail002.latte', array());
                $this->assertEquals($m->getHtmlBody(), 'nested template');
        }

//        public function testRequire01()
//        {
//                $iMail = new \MartyIX\Utils\Mail\iMail();
//
//
//                try {
//                        $m = $iMail->setTemplate(__DIR__ . '/Mail004.latte', array())
//                                ->setFrom('no-reply@fluid.com')
//                                ->setTo('fluid@fluid.com')
//                                ->setSubject('News from ...')
//                                ->getGeneratedMessage();
//
//                        $this->fail();
//                } catch (\Nette\Templating\FilterException $e) {
//
//                }
//
//                $this->log($m);
//                $this->assertEquals($m->getHtmlBody(), 'nested template');
//        }


        public function testRequire02()
        {
                $arr = array(
                    'first_name' => 'John',
                    'surname' => 'Doe',
                    'myArr' => array()
                );

                $this->getMailInstance('Mail003.latte', $arr);
        }

        public function testRegisterHelper()
        {
                $arr = array(
                    "name" => "John"
                );

                $m = $this->getMailInstance('Mail004.latte', $arr);
                $this->assertEquals($m->getHtmlBody(), '(John)');
        }

        public static function mailto($mail)
        {
                return '<a href="mailto:' . $mail . '">' . $mail . '</a>';
        }

        public function testAutoregisterHelper()
        {
                \MartyIX\Utils\Mail\iMail::$helperClass = '\iMailTest';

                $arr = array(
                    "email" => "marty@serious.ly"
                );

                $m = $this->getMailInstance('Mail005.latte', $arr);
                $this->assertEquals('<a href="mailto:' . $arr['email'] . '">' . $arr['email'] . '</a>', $m->getHtmlBody());
        }

}
