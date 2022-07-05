<?php

namespace Payum\Core\Tests\Bridge\Zend\Storage;

use Laminas\Db\TableGateway\TableGateway;
use Payum\Core\Bridge\Zend\Storage\TableGatewayStorage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TableGatewayStorageTest extends TestCase
{
    public function testShouldBeSubClassOfAbstractStorage()
    {
        $rc = new \ReflectionClass(\Payum\Core\Bridge\Zend\Storage\TableGatewayStorage::class);

        $this->assertTrue($rc->isSubclassOf(\Payum\Core\Storage\AbstractStorage::class));
    }

    public function testThrowIfTryToUseNotSupportedFindByMethod()
    {
        $this->expectException(\Payum\Core\Exception\LogicException::class);
        $this->expectExceptionMessage('Method is not supported by the storage.');
        $storage = new TableGatewayStorage($this->createTableGatewayMock(), \stdClass::class);

        $storage->findBy([]);
    }

    /**
     * @return MockObject|TableGateway
     */
    protected function createTableGatewayMock()
    {
        return $this->createMock(TableGateway::class);
    }
}
