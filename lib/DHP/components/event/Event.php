<?php
namespace DHP\components\event;

/**
 * Class Event
 *
 * Handles events for the system.
 *
 * @package DHP
 */
class Event
{

    const EVENT_ABORT = null;
    public $delegates = array();
    public $events = array(
      '*' => array()
    );

    /**
     * This triggers an event. All registered events are looped through in the order
     * they were registered. All parameters are called by reference so the registered
     * event methods can change the values, if necessary.
     *
     * If a registered method returns FALSE, the loop will break and further events
     * will not be processed.
     *
     *
     * @param String $eventName
     * @param null   $one
     * @param null   $two
     * @param null   $three
     * @param null   $four
     * @param null   $five
     * @param null   $six
     * @param null   $seven
     *
     * @return mixed
     */
    public function trigger(
        $eventName,
        &$one = null,
        &$two = null,
        &$three = null,
        &$four = null,
        &$five = null,
        &$six = null,
        &$seven = null
    ) {
        $args   = func_get_args();
        $return = null;
        switch (count($args)) {
            case 1:
                $return = $this->call($eventName);
                break;
            case 2:
                $return = $this->call($eventName, $one);
                break;
            case 3:
                $return = $this->call($eventName, $one, $two);
                break;
            case 4:
                $return = $this->call($eventName, $one, $two, $three);
                break;
            case 5:
                $return = $this->call($eventName, $one, $two, $three, $four);
                break;
            case 6:
                $return = $this->call($eventName, $one, $two, $three, $four, $five);
                break;
            case 7:
                $return = $this->call($eventName, $one, $two, $three, $four, $five, $six);
                break;
            case 8:
                $return = $this->call($eventName, $one, $two, $three, $four, $five, $six, $seven);
                break;
        }

        return $return;
    }

    /**
     * This is used to register a callable with a certain event.
     *
     * @param String   $eventName
     * @param Callable $callable
     *
     * @return mixed
     */
    public function register(
        $eventName,
        callable $callable
    ) {
        if (!isset( $this->events[$eventName] )) {
            $this->events[$eventName] = array();
        }
        $this->events[$eventName][] = $callable;
        return true;
    }

    /**
     * This is used when there are events that should not be publicly called but only
     * called on a observer, sort of.
     *
     * This way an object can tell it's observer when a certain event happened and
     * delegate some of its functionality to the observer.
     *
     * @param mixed $objectToSubscribeTo object to subscribe to
     * @param mixed $subscriber          observer
     *
     * @return mixed
     */
    public function subscribe($objectToSubscribeTo, &$subscriber)
    {
        $objectToSubscribeTo = spl_object_hash($objectToSubscribeTo);
        if (!isset( $this->delegates[$objectToSubscribeTo] )) {
            $this->delegates[$objectToSubscribeTo] = array();
        }
        $this->delegates[$objectToSubscribeTo][spl_object_hash($subscriber)] = & $subscriber;
    }

    /**
     * This will call $method on all the observers to the delegate, usually an object
     * calls this with $this :
     *
     * triggerSubscriber($this, 'observerNeedsToReactToThis')
     *
     * @param Object $delegate
     * @param String $method
     * @param null   $one
     * @param null   $two
     * @param null   $three
     * @param null   $four
     *
     * @return mixed
     */
    public function triggerSubscribe($delegate, $method, &$one = null, &$two = null, &$three = null, &$four = null)
    {
        $objectHash = spl_object_hash($delegate);
        $return     = null;
        if (isset( $this->delegates[$objectHash] )) {
            foreach ($this->delegates[$objectHash] as $target) {
                $return = $target->$method($one, $two, $three, $four);
                if ($return === false) {
                    break;
                }
            }
        }
        return $return;
    }

    /**
     * Internal function to actually trigger the event
     *
     * @param      $eventName
     * @param null $one
     * @param null $two
     * @param null $three
     * @param null $four
     * @param null $five
     * @param null $six
     * @param null $seven
     *
     * @return mixed|null
     *
     *
     * We know this is a very complicated function, due to the large amount of switch-statements
     * bue we choose to suppress this warning, since it actually isn't such a complicated method
     * after all
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function call(
        $eventName,
        &$one = null,
        &$two = null,
        &$three = null,
        &$four = null,
        &$five = null,
        &$six = null,
        &$seven = null
    ) {
        $return   = null;
        $numArgs  = ( func_num_args() - 1 );
        $callArgs = null;
        foreach ($this->mergeEventToCall($eventName) as $event) {
            $tempReturn = null;
            switch ($numArgs) {
                case 0:
                    $callArgs = isset( $callArgs ) ? $callArgs : array();
                    if (is_array($event)) {
                        $tempReturn = call_user_func($event);
                    } else {
                        $tempReturn = $event();
                    }
                    break;
                case 1:
                    $callArgs = isset( $callArgs ) ? $callArgs : array(&$one);
                    if (is_array($event)) {
                        $tempReturn = call_user_func_array($event, $callArgs);
                    } else {
                        $tempReturn = $event($one);
                    }
                    break;
                case 2:
                    $callArgs = isset( $callArgs ) ? $callArgs : array(&$one, &$two);
                    if (is_array($event)) {
                        $tempReturn = call_user_func_array($event, $callArgs);
                    } else {
                        $tempReturn = $event($one, $two);
                    }
                    break;
                case 3:
                    $callArgs = isset( $callArgs ) ? $callArgs : array(&$one, &$two, &$three);
                    if (is_array($event)) {
                        $tempReturn = call_user_func_array($event, $callArgs);
                    } else {
                        $tempReturn = $event($one, $two, $three);
                    }
                    break;
                case 4:
                    $callArgs = isset( $callArgs ) ? $callArgs : array(&$one, &$two, &$three, &$four);
                    if (is_array($event)) {
                        $tempReturn = call_user_func_array($event, $callArgs);
                    } else {
                        $tempReturn = $event($one, $two, $three, $four);
                    }
                    break;
                case 5:
                    $callArgs = isset( $callArgs ) ? $callArgs : array(&$one, &$two, &$three, &$four, &$five);
                    if (is_array($event)) {
                        $tempReturn = call_user_func_array($event, $callArgs);
                    } else {
                        $tempReturn = $event($one, $two, $three, $four, $five);
                    }
                    break;
                case 6:
                    $callArgs = isset( $callArgs ) ? $callArgs : array(&$one, &$two, &$three, &$four, &$five, &$six);
                    if (is_array($event)) {
                        $tempReturn = call_user_func_array($event, $callArgs);
                    } else {
                        $tempReturn = $event($one, $two, $three, $four, $five, $six);
                    }
                    break;
                case 7:
                    $callArgs =
                      isset( $callArgs ) ? $callArgs : array(&$one, &$two, &$three, &$four, &$five, &$six, &$seven);
                    if (is_array($event)) {
                        $tempReturn = call_user_func_array($event, $callArgs);
                    } else {
                        $tempReturn = $event($one, $two, $three, $four, $five, $six, $seven);
                    }
                    break;
            }
            if ($tempReturn === self::EVENT_ABORT) {
                break;
            }
            $return = $tempReturn;
        }
        return $return;
    }

    /**
     * This function will get events that match the current
     * event as well as events with wildcard, *.
     *
     * @param $eventName : name of event to be called
     *
     * @return array
     */
    private function mergeEventToCall($eventName)
    {
        $eventKeys = array($eventName);
        if (strpos($eventName, '.')) {
            $eventParts = explode('.', $eventName);
            $eventBase  = '';
            foreach ($eventParts as $part) {
                $eventBase .= $part;
                $eventKeys[] = $eventBase . '*';
                $eventKeys[] = $eventBase . '.*';
            }
        }
        $eventKeys[]    = '*';
        $eventsToReturn = array();
        foreach ($eventKeys as $event) {
            if (isset( $this->events[$event] )) {
                $eventsToReturn =
                  array_merge($eventsToReturn, $this->events[$event]);
            }
        }
        return $eventsToReturn;
    }
}
