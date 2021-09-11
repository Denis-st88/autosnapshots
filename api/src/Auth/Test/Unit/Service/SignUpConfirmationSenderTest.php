<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Service;

use Swift_Mailer;
use Swift_Message;
use Ramsey\Uuid\Uuid;
use RuntimeException;
use Twig\Environment;
use DateTimeImmutable;
use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;
use PHPUnit\Framework\TestCase;
use App\Auth\Service\SignUpConfirmationSender;

/**
 * @covers SignUpConfirmationSender
 */
class SignUpConfirmationSenderTest extends TestCase
{
    public function testSuccess(): void
    {
        $to = new Email('user@app.test');
        $token = new Token(Uuid::uuid4()->toString(), new \DateTimeImmutable());
        $confirmUrl = 'http://sign-up/confirm?token=' . $token->getValue();

        $twig = $this->createMock(Environment::class);
        $twig->expects($this->once())->method('render')->with(
            $this->equalTo('auth/signUp/confirm.html.twig'),
            $this->equalTo(['token' => $token])
        )->willReturn($body = '<a href="' . $confirmUrl .'">'. $confirmUrl .'</a>');

        $mailer = $this->createMock(Swift_Mailer::class);
        $mailer->expects($this->once())->method('send')
            ->willReturnCallback(static function (Swift_Message $message) use ($to, $body): int {
                self::assertEquals([$to->getValue() => null], $message->getTo());
                self::assertEquals('Sign up Confirmation', $message->getSubject());
                self::assertEquals($body, $message->getBody());
                self::assertEquals('text/html', $message->getBodyContentType());
                return 1;
            });

        $sender = new SignUpConfirmationSender($mailer, $twig);

        $sender->send($to, $token);
    }

    public function testError(): void
    {
        $to = new Email('user@app.test');
        $token = new Token(Uuid::uuid4()->toString(), new DateTimeImmutable());
        $confirmUrl = 'http://sign-up/confirm?token=' . $token->getValue();

        $twig = $this->createStub(Environment::class);
        $twig->method('render')->willReturn('<a href="' . $confirmUrl . '">' . $confirmUrl . '</a>');

        $mailer = $this->createStub(Swift_Mailer::class);
        $mailer->method('send')->willReturn(0);

        $sender = new SignUpConfirmationSender($mailer, $twig);

        $this->expectException(RuntimeException::class);
        $sender->send($to, $token);
    }
}
