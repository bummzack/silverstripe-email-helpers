<?php

/**
 * PHPUnit Tests for Silverstripe Email Helpers
 *
 * @package Silverstripe Email Helpers
 * @subpackage tests
 */
class EmailHelpersTest extends SapphireTest
{

    public function testSmtpMailerSetup()
    {
        // PHPMailer setup
        $mailer = new SmtpMailer('yourserver.com:587', 'username', 'password', true, 'UTF-8');
        Injector::inst()->registerService($mailer, 'Mailer');

        $smtpmailer = Email::mailer();
        $this->assertEquals('SmtpMailer', get_class($smtpmailer), "SmtpMailer class is used for sending emails");
        $this->assertTrue($smtpmailer->getTls(), "tls is set to true as set in Injector");
        $this->assertContains('UTF-8', $smtpmailer->getCharset(), "Charset set to UTF-8 as set in Injector");
    }

    public function testEmogrifiedSmtpMailerSetup()
    {
        // PHPMailer setup
        $mailer = new EmogrifiedSmtpMailer('yourserver.com:587', 'username', 'password', true, 'UTF-8', 'silvershop/css/order.css', 1, true);
        Injector::inst()->registerService($mailer, 'Mailer');

        $emogrifiedsmtpmailer = Email::mailer();
        $this->assertEquals('EmogrifiedSmtpMailer', get_class($emogrifiedsmtpmailer), "EmogrifiedSmtpMailer class is used for sending emails");
        $this->assertTrue($emogrifiedsmtpmailer->getTls(), "tls is set to true as set in Injector");
        $this->assertSame('UTF-8', $emogrifiedsmtpmailer->getCharset(), "Charset set to UTF-8 as set in Injector");
        $this->assertSame('silvershop/css/order.css', $emogrifiedsmtpmailer->getCSSfile(), 'The CSS file is set to silvershop/css/order.css');
        $this->assertSame(1, $emogrifiedsmtpmailer->getSMTPDebug(), 'SMTPDebug is set to 1');
        $this->assertTrue($emogrifiedsmtpmailer->getLogfailedemail(), "Failed emails to be logged");

    }

    public function testInlineCSS()
    {
        // Get HTML file from Fixtures
        $fileLocation = join(DIRECTORY_SEPARATOR, array(__DIR__, 'fixtures/testhtml.html'));
        $fileHandler = fopen($fileLocation, 'r');
        $htmlContent = fread($fileHandler, filesize($fileLocation));
        fclose($fileHandler);

        // Note reference to external css file
        $inlinedCSS = InlineCSS::convert($htmlContent, 'email-helpers/tests/fixtures/externalcssfile.css');

        $this->assertContains('<body style="font-family: Helvetica,Arial,sans-serif; font-size: 14px; line-height: 1.6em; margin: 0; width: 100% !important; height: 100%;">', $inlinedCSS, 'Body element contains inline styling');
        $this->assertContains('<table id="Content" cellspacing="0" cellpadding="0" summary="Order Information" style="text-align: left; margin: auto; padding-left: 20px;">', $inlinedCSS, 'Table element contains inline styling');
        $this->assertContains('<tr class="itemRow" style="background-color: red;">', $inlinedCSS, 'Table row contains inline styling');
        $this->assertContains('<td class="image" style="border: 1px;"></td>', $inlinedCSS, 'Table cell contains inline styling');
    }
}