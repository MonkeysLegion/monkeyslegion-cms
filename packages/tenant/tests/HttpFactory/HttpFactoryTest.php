<?php

declare(strict_types=1);

namespace MonkeysLegion\TenantTests\HttpFactory;

use Interop\Http\Factory\ResponseFactoryTestCase;
use MonkeysLegion\Http\Factory\HttpFactory;
use Psr\Http\Message\ResponseFactoryInterface;

final class HttpFactoryTest extends ResponseFactoryTestCase
{
    protected function createResponseFactory(): ResponseFactoryInterface
    {
        return new HttpFactory();
    }
}
