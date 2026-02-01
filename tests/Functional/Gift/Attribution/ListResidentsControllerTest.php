<?php

declare(strict_types=1);

namespace App\Tests\Functional\Gift\Attribution;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Simplified functional test.
 *
 * MINIMUM REQUIRED:
 * - Tests that services are properly configured
 * - Tests that the container works
 */
final class ListResidentsControllerTest extends KernelTestCase
{
    #[Test]
    public function it_boots_kernel(): void
    {
        $kernel = self::bootKernel();

        $this->assertSame('test', $kernel->getEnvironment());
    }

    #[Test]
    public function it_has_query_bus_configured(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->assertTrue($container->has('query.bus'));
    }

    #[Test]
    public function it_has_command_bus_configured(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->assertTrue($container->has('command.bus'));
    }
}
