<?php
    /*
     Payment Name      : CCAvenue MCPG
     Description		  : Extends Payment with  CCAvenue MCPG.
     CCAvenue Version  : MCPG-2.0
     Module Version    : bz-3.0
     Author			  : BlueZeal SoftNet
     Copyright         : © 2009-2017
     */
namespace Magento\CcavenuepayProvider\Test\Unit\Gateway\Http;

use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Ccavenuepay\Gateway\Http\TransferFactory;
use Magento\Ccavenuepay\Gateway\Request\MockDataRequest;

class TransferFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $request = [
            'parameter' => 'value',
            MockDataRequest::FORCE_RESULT => 1
        ];

        $transferBuilder = $this->getMockBuilder(TransferBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $transferObject = $this->getMock(TransferInterface::class);

        $transferBuilder->expects(static::once())
            ->method('setBody')
            ->with($request)
            ->willReturnSelf();
        $transferBuilder->expects(static::once())
            ->method('setMethod')
            ->with('POST')
            ->willReturnSelf();
        $transferBuilder->expects(static::once())
            ->method('setHeaders')
            ->with(
                [
                    'force_result' => 1
                ]
            )
            ->willReturnSelf();

        $transferBuilder->expects(static::once())
            ->method('build')
            ->willReturn($transferObject);

        $transferFactory = new TransferFactory($transferBuilder);

        static::assertSame(
            $transferObject,
            $transferFactory->create($request)
        );
    }
}
