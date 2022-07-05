<?php

namespace Payum\Klarna\Invoice\Tests\Request\Api;

use PHPUnit\Framework\TestCase;

class ActivateTest extends TestCase
{
    public function testShouldBeSubClassOfBaseOrder()
    {
        $rc = new \ReflectionClass(\Payum\Klarna\Invoice\Request\Api\Activate::class);

        $this->assertTrue($rc->isSubclassOf(\Payum\Core\Request\Generic::class));
    }
}
