<?php

namespace App\Tests\Service;

use App\Service\MailService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Mailer\MailerInterface;

class MailServiceTest extends KernelTestCase
{
    public function testSend(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects($this->once())->method('send');

        $logger = $container->get('monolog.logger.mail');

        $service = new MailService($mailer, $logger);

        $service->send(
            'test@mailhog.local',
            'Test',
            'Content'
        );

        $this->assertTrue(true);
    }
}
