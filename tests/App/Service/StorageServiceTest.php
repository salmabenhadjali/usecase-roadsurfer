<?php

namespace App\Tests\App\Service;

use App\Service\StorageService;
use PHPUnit\Framework\TestCase;

class StorageServiceTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        fwrite(STDOUT, "\nStarting StorageServiceTest Tests...\n");
    }

    public function testReceivingRequest(): void
    {
        $request = file_get_contents('request.json');

        $storageService = new StorageService($request);

        $this->assertNotEmpty($storageService->getRequest());
        $this->assertIsString($storageService->getRequest());
    }
}
