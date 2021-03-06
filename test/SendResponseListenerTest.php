<?php
/**
 * @link      http://github.com/zendframework/zend-mvc for the canonical source repository
 * @copyright Copyright (c) 2005-2018 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-mvc/blob/master/LICENSE.md New BSD License
 */

namespace ZendTest\Mvc;

use PHPUnit\Framework\TestCase;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\SendResponseListener;
use Zend\Mvc\ResponseSender\SendResponseEvent;
use Zend\Stdlib\ResponseInterface;

class SendResponseListenerTest extends TestCase
{
    public function testEventManagerIdentifiers()
    {
        $listener = new SendResponseListener();
        $identifiers = $listener->getEventManager()->getIdentifiers();
        $expected    = [SendResponseListener::class];
        $this->assertEquals($expected, array_values($identifiers));
    }

    public function testSendResponseTriggersSendResponseEvent()
    {
        $listener = new SendResponseListener();
        $result = [];
        $listener->getEventManager()->attach(SendResponseEvent::EVENT_SEND_RESPONSE, function ($e) use (&$result) {
            $result['target'] = $e->getTarget();
            $result['response'] = $e->getResponse();
        }, 10000);
        $mockResponse = $this->getMockForAbstractClass(ResponseInterface::class);
        $mockMvcEvent = $this->getMockBuilder(MvcEvent::class)
            ->setMethods(['getResponse'])
            ->getMock();
        $mockMvcEvent->expects($this->any())->method('getResponse')->will($this->returnValue($mockResponse));
        $listener->sendResponse($mockMvcEvent);
        $expected = [
            'target' => $listener,
            'response' => $mockResponse
        ];
        $this->assertEquals($expected, $result);
    }
}
