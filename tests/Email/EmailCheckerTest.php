<?php

namespace Bboxlab\Tests\Email;

use Bboxlab\Moselle\Client\MoselleClient;
use Bboxlab\Moselle\Configuration\Configuration;
use Bboxlab\Moselle\Email\EmailInput;
use Bboxlab\Moselle\Validation\Validator;
use Bboxlab\Tests\Utils\AbstractMoselleTestCase;
use Bboxlab\Moselle\Email\EmailChecker;

class EmailCheckerTest extends AbstractMoselleTestCase
{
    public function testCheckEmail()
    {
        // create a mock for Moselle Client
        $mockedClient = $this->createMock(MoselleClient::class);
        $mockedClient->method('requestBtOpenApi')
            ->willReturnOnConsecutiveCalls(
                [
                    'access_token' => '123456',
                    'expires_in' => 3600
                ],
                [
                    'contactEmailAddress' => false,
                    'validEmailAddress' => true
                ],
            );

        $checker = new EmailChecker(new Validator(), $mockedClient);

        $btConfig = new Configuration();
        $btConfig->setOauthAppCredentialsUrl('http://oauth-fake-url.fr');
        $btConfig->setEmailAddressUrl('http://emailcheck-fake-url.fr');

        $input = new EmailInput();
        $input->setEmailAddress('eugenie.grandet@balzac.fr');

        $result = $checker($input, $btConfig, $this->createCredentials());

        // test token
        $this->assertEquals(123456, $result->getToken()->getAccessToken());
        $this->assertEquals(3600, $result->getToken()->getExpiresIn());
        $this->assertEquals(true, $result->getToken()->isNew());
        $this->assertIsString($result->getToken()->getCreatedAt());

        // test content
        $this->assertEquals(false,$result->getContent()['contactEmailAddress']);
        $this->assertEquals(true, $result->getContent()['validEmailAddress']);
    }
}
