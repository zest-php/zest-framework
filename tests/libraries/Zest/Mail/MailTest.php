<?php

/**
 * @category Zest
 * @package Zest_Mail
 * @subpackage UnitTests
 */
class Zest_Mail_MailTest extends PHPUnit_Framework_TestCase{
	
	public function testConstructCharset(){
		$mail = new Zest_Mail('iso-8859-1');
		$this->assertEquals('iso-8859-1', $mail->getCharset());
	}
	
	public function testConstructOptions(){
		$mail = new Zest_Mail(array('charset' => 'iso-8859-1'));
		$this->assertEquals('iso-8859-1', $mail->getCharset());
	}
	
	public function testBodyHtmlScript(){
		$view = Zest_View::getStaticView();
		$view->addScriptPath(Zest_AllTests::getDataDir().'/mail');
		
		$mail = new Zest_Mail();
		$mail->setBodyHtmlScript('test-body-html-script.phtml');
		
		$this->assertRegExp('/<body>\s*testBodyHtmlScript\s*<\/body>/', $this->_getBodyHtml($mail));
	}
	
	public function testBodyHtmlLayoutBeforeException(){
		$this->setExpectedException('Zest_Mail_Exception');
		$mail = new Zest_Mail();
		$mail->setBodyHtml('testBodyHtmlLayoutException');
		$mail->setBodyHtmlLayout('test-body-html-layout');
	}
	
	public function testBodyHtmlLayoutMvcException(){
		$this->setExpectedException('Zest_Mail_Exception');
		Zest_Layout::resetMvcInstance();
		$mail = new Zest_Mail();
		$mail->setBodyHtmlLayout('test-body-html-layout');
	}
	
	public function testBodyHtmlLayout(){
		Zest_Layout::resetMvcInstance();
		Zest_Layout::startMvc(array('layoutPath' => Zest_AllTests::getDataDir().'/mail'));
		
		$mail = new Zest_Mail();
		$mail->setBodyHtmlLayout('test-body-html-layout');
		$mail->setBodyHtml('testBodyHtmlLayout');
		
		$this->assertRegExp('/<body>\s*testBodyHtmlLayout\s*<\/body>/', $this->_getBodyHtml($mail));
	}
	
	public function testAddToEmail(){
		$mail = new Zest_Mail();
		$mail->addTo('email@yopmail.com');
		
		$recipients = $mail->getRecipients();
		$to = $mail->getHeaders();
		$to = $to['To'];
		
		$this->assertEquals('email@yopmail.com', $recipients[0]);
		$this->assertEquals($to[0], $recipients[0]);
	}
	
	public function testAddToEmailName(){
		$mail = new Zest_Mail();
		$mail->addTo('email@yopmail.com', 'a name');
		
		$recipients = $mail->getRecipients();
		$to = $mail->getHeaders();
		$to = $to['To'];
		
		$this->assertEquals('email@yopmail.com', $recipients[0]);
		$this->assertEquals('a name <email@yopmail.com>', $to[0]);
	}
	
	public function testAddToEmails(){
		$mail = new Zest_Mail();
		$mail->addTo(array(
			array('email-1@yopmail.com'),
			array('email-2@yopmail.com', 'a name'),
			'email-3@yopmail.com'
		));
		
		$recipients = $mail->getRecipients();
		$to = $mail->getHeaders();
		$to = $to['To'];
		
		$this->assertEquals(array('email-1@yopmail.com', 'email-2@yopmail.com', 'email-3@yopmail.com'), $recipients);
		
		unset($to['append']);
		$this->assertEquals(array('email-1@yopmail.com', 'a name <email-2@yopmail.com>', 'email-3@yopmail.com'), $to);
	}
	
	public function testAddAttachmentFile(){
		$mail = new Zest_Mail();
		$mail->addAttachmentFile($this->_getAttachment());
		$parts = $mail->getParts();
		$this->assertEquals('image/png', $parts[0]->type);
		$this->assertEquals(Zend_Mime::DISPOSITION_ATTACHMENT, $parts[0]->disposition);
	}
	
	public function testAddAttachmentInlineImage(){
		$mail = new Zest_Mail();
		$cid = $mail->addAttachmentInlineImage($this->_getAttachment());
		$parts = $mail->getParts();
		
		$this->assertStringStartsWith('cid_', $cid);
		$this->assertEquals('image/png', $parts[0]->type);
		$this->assertEquals(Zend_Mime::DISPOSITION_INLINE, $parts[0]->disposition);
	}
	
	public function testValidEmailAddress(){
		$this->assertTrue(Zest_Mail::isValidEmailAddress('email@yopmail.com'));
	}
	
	public function testObfuscate(){
		$email = Zest_Mail::obfuscate('email@yopmail.com');
		$this->assertEquals('&#0101;&#0109;&#0097;&#0105;&#0108;&#0064;&#0121;&#0111;&#0112;&#0109;&#0097;&#0105;&#0108;&#0046;&#0099;&#0111;&#0109;', $email);
	}
	
	protected function _getAttachment(){
		return Zest_AllTests::getDataDir().'/mail/attachment.png';
	}
	
	protected function _getBodyHtml(Zest_Mail $mail){
		$content = $mail->getBodyHtml()->getContent();
		$content = str_replace('=0D=0A', PHP_EOL, $content);
		$content = str_replace('=0A', "\n", $content);
		$content = str_replace('=09', "\t", $content);
		$content = str_replace('=3D', '=', $content);
		$content = str_replace("=\n", '', $content);
		return $content;
	}
	
}