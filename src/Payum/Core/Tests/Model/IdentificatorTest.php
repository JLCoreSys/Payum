<?php

namespace Payum\Core\Tests\Model;

use Payum\Core\Model\Identificator;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Serializable;
use stdClass;

class IdentificatorTest extends TestCase
{
    public function testShouldImplementSerializableInterface(): void
    {
        $rc = new ReflectionClass(Identificator::class);

        $this->assertTrue($rc->implementsInterface(Serializable::class));
    }

    public function testShouldAllowGetIdSetInConstructor(): void
    {
        $id = new Identificator('theId', new stdClass());

        $this->assertSame('theId', $id->getId());
    }

    public function testShouldAllowGetClassSetInConstructor(): void
    {
        $id = new Identificator('theId', new stdClass());

        $this->assertSame(stdClass::class, $id->getClass());
    }

    public function testShouldBeCorrectlySerializedAndUnserialized(): void
    {
        $id = new Identificator('theId', new stdClass());

        $serializedId = serialize($id);

        $unserializedId = unserialize($serializedId);

        $this->assertEquals($id, $unserializedId);
    }
}
