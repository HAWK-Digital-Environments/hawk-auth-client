<?php
declare(strict_types=1);


namespace Hawk\AuthClient\Tests\Keycloak\Value;


use Hawk\AuthClient\Exception\InvalidUuidException;
use Hawk\AuthClient\Util\Uuid;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Uuid::class)]
#[CoversClass(InvalidUuidException::class)]
class ClientUuidTest extends TestCase
{
    public function testItConstructs(): void
    {
        $sut = new Uuid('f47ac10b-58cc-4372-a567-0e02b2c3d001');
        $this->assertInstanceOf(Uuid::class, $sut);
    }

    public function testItFailsToConstructWithInvalidUuid(): void
    {
        $this->expectException(InvalidUuidException::class);
        new Uuid('asdf');
    }

    public function testItCanBeStringified(): void
    {
        $sut = new Uuid('f47ac10b-58cc-4372-a567-0e02b2c3d001');
        $this->assertEquals('f47ac10b-58cc-4372-a567-0e02b2c3d001', (string)$sut);
    }
}
