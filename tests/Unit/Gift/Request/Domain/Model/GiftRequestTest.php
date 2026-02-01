<?php

declare(strict_types=1);

namespace App\Tests\Unit\Gift\Request\Domain\Model;

use App\Gift\Request\Domain\Model\GiftRequest;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for GiftRequest Entity - MINIMUM REQUIRED.
 */
final class GiftRequestTest extends TestCase
{
    #[Test]
    public function it_creates_request_in_pending_state(): void
    {
        $request = GiftRequest::create(
            'req-1',
            'John',
            'john@example.com',
            '0612345678',
            'Vélo',
            'Motivation'
        );

        $this->assertTrue($request->isPending());
    }

    #[Test]
    public function it_approves_request(): void
    {
        $request = GiftRequest::create(
            'req-1',
            'John',
            'john@example.com',
            '0612345678',
            'Vélo',
            'Motivation'
        );

        $request->approve();

        $this->assertFalse($request->isPending());
    }

    #[Test]
    public function it_rejects_request(): void
    {
        $request = GiftRequest::create(
            'req-1',
            'John',
            'john@example.com',
            '0612345678',
            'Vélo',
            'Motivation'
        );

        $request->reject();

        $this->assertFalse($request->isPending());
    }

    #[Test]
    public function it_rejects_double_approval(): void
    {
        $request = GiftRequest::create(
            'req-1',
            'John',
            'john@example.com',
            '0612345678',
            'Vélo',
            'Motivation'
        );

        $request->approve();

        $this->expectException(\DomainException::class);
        $request->approve();
    }
}
