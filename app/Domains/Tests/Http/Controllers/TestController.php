<?php

namespace App\Domains\Tests\Http\Controllers;

use App\Domains\Tests\Services\TestEventBuilderService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class TestController extends Controller
{
    public function __construct(
        private TestEventBuilderService $testEventBuilderService
    ) {}

    public function testEventBuilder(): JsonResponse
    {
        $result = $this->testEventBuilderService->execute();

        return $this->success($result);
    }
}
