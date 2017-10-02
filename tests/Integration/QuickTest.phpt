<?php
declare(strict_types=1);

namespace FreezyBee\SmokeTester\Tests\Integration;

require __DIR__ . '/../bootstrap.php';

use FreezyBee\SmokeTester\SmokeTester;
use Nette\Application\BadRequestException;
use Tester\Assert;
use Tester\TestCase;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 * @testCase
 */
class QuickTest extends TestCase
{
    public function testDefault()
    {
        $client = SmokeTester::createClient();
        $response = $client->netteRequest('GET', 'Default:default');
        Assert::true($response->isSuccessful());
        Assert::same(200, $response->getCode());
        Assert::same('{"status":"ok"}', $response->getContent());
    }

    public function testRedirect()
    {
        $client = SmokeTester::createClient();
        $response = $client->netteRequest('GET', 'Default:redirect');
        Assert::same(302, $response->getCode());
        Assert::same('http://localhost/', $response->getRedirectUrl());
    }

    public function testError()
    {
        $client = SmokeTester::createClient();
        $response = $client->netteRequest('GET', 'Default:error');
        Assert::same(400, $response->getCode());
        Assert::same('{"status":"error"}', $response->getContent());
    }

    public function testException()
    {
        $client = SmokeTester::createClient();

        Assert::exception(function () use ($client) {
            $client->netteRequest('GET', 'Default:exception');
        }, BadRequestException::class);
    }
}

(new QuickTest)->run();
