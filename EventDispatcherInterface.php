<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\EventDispatcher;

/**
 * The EventDispatcherInterface is the central point of Doctrine's event listener system.
 * Listeners are registered on the manager and events are dispatched through the
 * manager.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
interface EventDispatcherInterface
{
    /**
     * Adds an event listener that listens on the specified events.
     *
     * @param string|array $eventNames The event(s) to listen on.
     * @param object $listener The listener object.
     * @param integer $priority The higher this value, the earlier an event
     *                          listener will be triggered in the chain.
     *                          Defaults to 0.
     */
    function addEventListener($eventNames, $listener, $priority = 0);

    /**
     * Adds an event subscriber. The subscriber is asked for all the events he is
     * interested in and added as a listener for these events.
     *
     * @param EventSubscriberInterface $subscriber The subscriber.
     * @param integer $priority The higher this value, the earlier an event
     *                          listener will be triggered in the chain.
     *                          Defaults to 0.
     */
    function addEventSubscriber(EventSubscriberInterface $subscriber, $priority = 0);

    /**
     * Removes an event listener from the specified events.
     *
     * @param string|array $eventNames The event(s) to remove a listener from.
     * @param object $listener The listener object to remove.
     */
    function removeEventListener($eventNames, $listener);

    /**
     * Dispatches an event to all registered listeners.
     *
     * @param string $eventName The name of the event to dispatch. The name of
     *                          the event is the name of the method that is
     *                          invoked on listeners.
     * @param Event $event The event to pass to the event handlers/listeners.
     *                     If not supplied, an empty Event instance is created.
     */
    function dispatchEvent($eventName, Event $event = null);

    /**
     * Gets the listeners of a specific event or all listeners.
     *
     * @param string $eventName The name of the event.
     *
     * @return array The event listeners for the specified event, or all event
     *               listeners by event name.
     */
    function getListeners($eventName = null);

    /**
     * Checks whether an event has any registered listeners.
     *
     * @param string $eventName The name of the event.
     *
     * @return Boolean TRUE if the specified event has any listeners, FALSE
     *                 otherwise.
     */
    function hasListeners($eventName);
}