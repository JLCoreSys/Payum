<?php

namespace Payum\Klarna\Invoice\Tests\Action\Api;

use Payum\Core\Tests\GenericApiAwareActionTest;
use Payum\Klarna\Invoice\Action\Api\SendInvoiceAction;
use Payum\Klarna\Invoice\Config;
use Payum\Klarna\Invoice\Request\Api\SendInvoice;
use PhpXmlRpc\Client;

class SendInvoiceActionTest extends GenericApiAwareActionTest
{
    public function testShouldBeSubClassOfBaseApiAwareAction()
    {
        $rc = new \ReflectionClass(\Payum\Klarna\Invoice\Action\Api\SendInvoiceAction::class);

        $this->assertTrue($rc->isSubclassOf(\Payum\Klarna\Invoice\Action\Api\BaseApiAwareAction::class));
    }

    public function testThrowApiNotSupportedIfNotConfigGivenAsApi()
    {
        $this->expectException(\Payum\Core\Exception\UnsupportedApiException::class);
        $this->expectExceptionMessage('Not supported api given. It must be an instance of Payum\Klarna\Invoice\Config');
        $action = new SendInvoiceAction($this->createKlarnaMock());

        $action->setApi(new \stdClass());
    }

    public function testShouldSupportSendInvoiceWithArrayAsModel()
    {
        $action = new SendInvoiceAction();

        $this->assertTrue($action->supports(new SendInvoice([])));
    }

    public function testShouldNotSupportAnythingNotSendInvoice()
    {
        $action = new SendInvoiceAction();

        $this->assertFalse($action->supports(new \stdClass()));
    }

    public function testShouldNotSupportSendInvoiceWithNotArrayAccessModel()
    {
        $action = new SendInvoiceAction();

        $this->assertFalse($action->supports(new SendInvoice(new \stdClass())));
    }

    public function testThrowIfNotSupportedRequestGivenAsArgumentOnExecute()
    {
        $this->expectException(\Payum\Core\Exception\RequestNotSupportedException::class);
        $action = new SendInvoiceAction();

        $action->execute(new \stdClass());
    }

    public function testShouldCallKlarnaSendInvoice()
    {
        $details = [
            'invoice_number' => 'invoice number',
        ];

        $klarnaMock = $this->createKlarnaMock();
        $klarnaMock
            ->expects($this->once())
            ->method('sendInvoice')
            ->with(
                $details['invoice_number']
            )
        ;

        $action = new SendInvoiceAction($klarnaMock);
        $action->setApi(new Config());

        $action->execute(new SendInvoice($details));
    }

    public function testShouldCatchKlarnaExceptionAndSetErrorInfoToDetails()
    {
        $details = [
            'invoice_number' => 'invoice number',
        ];

        $klarnaMock = $this->createKlarnaMock();
        $klarnaMock
            ->expects($this->once())
            ->method('sendInvoice')
            ->with(
                $details['invoice_number']
            )
            ->willThrowException(new \KlarnaException('theMessage', 123))
        ;

        $action = new SendInvoiceAction($klarnaMock);
        $action->setApi(new Config());

        $action->execute($reserve = new SendInvoice($details));

        $postDetails = $reserve->getModel();
        $this->assertSame(123, $postDetails['error_code']);
        $this->assertSame('theMessage', $postDetails['error_message']);
    }

    protected function getActionClass(): string
    {
        return SendInvoiceAction::class;
    }

    protected function getApiClass()
    {
        return new Config();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Klarna
     */
    protected function createKlarnaMock()
    {
        $klarnaMock = $this->createMock(\Klarna::class, ['config', 'sendInvoice']);

        $rp = new \ReflectionProperty($klarnaMock, 'xmlrpc');
        $rp->setAccessible(true);
        $rp->setValue($klarnaMock, $this->createMock(class_exists('xmlrpc_client') ? 'xmlrpc_client' : Client::class));
        $rp->setAccessible(false);

        return $klarnaMock;
    }
}
