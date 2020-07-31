<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\EventDispatcher\Tests;

use Amp\Deferred;
use Amp\Promise;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\ImmutableEventDispatcher;
use Symfony\Contracts\EventDispatcher\Event;
use function Amp\call;

/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class ImmutableEventDispatcherTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $innerDispatcher;

    /**
     * @var ImmutableEventDispatcher
     */
    private $dispatcher;

    protected function setUp(): void
    {
        $this->innerDispatcher = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcherInterface')->getMock();
        $this->dispatcher = new ImmutableEventDispatcher($this->innerDispatcher);
    }

    public function testDispatchDelegates()
    {
        $event = new Event();
        $eventName = 'event';

        $return = call(function () use ($event, $eventName) {
            $defer = new Deferred();
            $defer->resolve($event);
            return $defer->promise();
        });

        $this->innerDispatcher->expects($this->once())
            ->method('dispatch')
            ->with($event, $eventName)
            ->willReturn($return);

        Promise\wait(call(function () use ($event, $eventName) {
            $this->assertSame($event, yield $this->dispatcher->dispatch($event, $eventName));
        }));
    }

    public function testGetListenersDelegates()
    {
        $this->innerDispatcher->expects($this->once())
            ->method('getListeners')
            ->with('event')
            ->willReturn(['result']);

        $this->assertSame(['result'], $this->dispatcher->getListeners('event'));
    }

    public function testHasListenersDelegates()
    {
        $this->innerDispatcher->expects($this->once())
            ->method('hasListeners')
            ->with('event')
            ->willReturn(true);

        $this->assertTrue($this->dispatcher->hasListeners('event'));
    }

    public function testAddListenerDisallowed()
    {
        $this->expectException('\BadMethodCallException');
        $this->dispatcher->addListener('event', function () { return 'foo'; });
    }

    public function testAddSubscriberDisallowed()
    {
        $this->expectException('\BadMethodCallException');
        $subscriber = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventSubscriberInterface')->getMock();

        $this->dispatcher->addSubscriber($subscriber);
    }

    public function testRemoveListenerDisallowed()
    {
        $this->expectException('\BadMethodCallException');
        $this->dispatcher->removeListener('event', function () { return 'foo'; });
    }

    public function testRemoveSubscriberDisallowed()
    {
        $this->expectException('\BadMethodCallException');
        $subscriber = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventSubscriberInterface')->getMock();

        $this->dispatcher->removeSubscriber($subscriber);
    }
}
