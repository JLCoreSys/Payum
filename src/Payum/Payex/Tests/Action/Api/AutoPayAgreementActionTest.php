<?php

namespace Payum\Payex\Tests\Action\Api;

use Payum\Payex\Action\Api\AutoPayAgreementAction;
use Payum\Payex\Api\AgreementApi;
use Payum\Payex\Request\Api\AutoPayAgreement;

class AutoPayAgreementActionTest extends \PHPUnit\Framework\TestCase
{
    protected $requiredFields = [
        'agreementRef' => 'aRef',
        'price' => 1000,
        'productNumber' => 'aNum',
        'description' => 'aDesc',
        'orderId' => 'anId',
        'purchaseOperation' => AgreementApi::PURCHASEOPERATION_SALE,
        'currency' => 'NOK',
    ];

    public function provideRequiredFields()
    {
        $fields = [];

        foreach ($this->requiredFields as $name => $value) {
            $fields[] = [$name];
        }

        return $fields;
    }

    public function testShouldImplementActionInterface()
    {
        $rc = new \ReflectionClass(\Payum\Payex\Action\Api\AutoPayAgreementAction::class);

        $this->assertTrue($rc->isSubclassOf(\Payum\Core\Action\ActionInterface::class));
    }

    public function testShouldImplementApiAwareInterface()
    {
        $rc = new \ReflectionClass(\Payum\Payex\Action\Api\AutoPayAgreementAction::class);

        $this->assertTrue($rc->isSubclassOf(\Payum\Core\ApiAwareInterface::class));
    }

    public function testThrowOnTryingSetNotAgreementApiAsApi()
    {
        $this->expectException(\Payum\Core\Exception\UnsupportedApiException::class);
        $this->expectExceptionMessage('Not supported api given. It must be an instance of Payum\Payex\Api\AgreementApi');
        $action = new AutoPayAgreementAction();

        $action->setApi(new \stdClass());
    }

    public function testShouldSupportAutoPayAgreementRequestWithArrayAccessAsModel()
    {
        $action = new AutoPayAgreementAction();

        $this->assertTrue($action->supports(new AutoPayAgreement($this->createMock(\ArrayAccess::class))));
    }

    public function testShouldNotSupportAnythingNotAutoPayAgreementRequest()
    {
        $action = new AutoPayAgreementAction();

        $this->assertFalse($action->supports(new \stdClass()));
    }

    public function testShouldNotSupportAutoPayAgreementRequestWithNotArrayAccessModel()
    {
        $action = new AutoPayAgreementAction();

        $this->assertFalse($action->supports(new AutoPayAgreement(new \stdClass())));
    }

    public function testThrowIfNotSupportedRequestGivenAsArgumentForExecute()
    {
        $this->expectException(\Payum\Core\Exception\RequestNotSupportedException::class);
        $action = new AutoPayAgreementAction($this->createApiMock());

        $action->execute(new \stdClass());
    }

    /**
     * @dataProvider provideRequiredFields
     */
    public function testThrowIfTryInitializeWithRequiredFieldNotPresent($requiredField)
    {
        $this->expectException(\Payum\Core\Exception\LogicException::class);
        unset($this->requiredFields[$requiredField]);

        $action = new AutoPayAgreementAction();

        $action->execute(new AutoPayAgreement($this->requiredFields));
    }

    public function testShouldAutoPayAgreementPayment()
    {
        $apiMock = $this->createApiMock();
        $apiMock
            ->expects($this->once())
            ->method('autoPay')
            ->with($this->requiredFields)
            ->willReturn([
                'transactionStatus' => 'theStatus',
            ]);

        $action = new AutoPayAgreementAction();
        $action->setApi($apiMock);

        $request = new AutoPayAgreement($this->requiredFields);

        $action->execute($request);

        $model = $request->getModel();
        $this->assertSame('theStatus', $model['transactionStatus']);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Payum\Payex\Api\AgreementApi
     */
    protected function createApiMock()
    {
        return $this->createMock(\Payum\Payex\Api\AgreementApi::class, [], [], '', false);
    }
}
