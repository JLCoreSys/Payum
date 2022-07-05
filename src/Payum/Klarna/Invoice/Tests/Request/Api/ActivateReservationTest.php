<?php

namespace Payum\Klarna\Invoice\Tests\Request\Api;

use PHPUnit\Framework\TestCase;

class ActivateReservationTest extends TestCase
{
    public function testShouldBeSubClassOfBaseOrder()
    {
        $rc = new \ReflectionClass(\Payum\Klarna\Invoice\Request\Api\ActivateReservation::class);

        $this->assertTrue($rc->isSubclassOf(\Payum\Core\Request\Generic::class));
    }
}
