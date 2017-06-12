---
layout: docs_1_1
title: Timer Service
meta_title: appserver.io timer service documentation
meta_description: As CRON does, the Timer Service allows you to schedule processing your functionality at a given date or in regular intervals.
position: 110
group: Docs
permalink: /get-started/documentation/1.1/timer-service.html
---

In most of your projects, you need to schedule things at regular intervals or at a given date in future. As PHP itself is a scripting language, it lacks this functionality. Thus, developers use utilities like CRON when working on Mac OS X or a Linux distribution. If you are working on Windows, it is a bit more challenging. There is a tool called Scheduler, which is not as simple to use as CRON. At this point Timer Service, a good and simple option, comes into play.

As CRON, the Timer Service allows you to schedule processing your functionality at a given date or regular intervals. In contrast to CRON, it allows you to schedule processing the methods of your Beans in such a way. This is done by simply adding an annotation to your method.

```php
<?php

namespace Namespace\Modulename;

/**
 * @Singleton(name="ASingletonProcessor")
 */
class ASingletonProcessor extends \Stackable
{

  /**
   * A dummy method invoked by the container upon timer schedule.
   *
   * @param TimerInterface $timer The timer instance
   *
   * @return void
   * @Schedule(dayOfMonth=EVERY, month=EVERY, year=EVERY, minute=EVERY, hour=EVERY)
   */
  public function invokedByTimer(TimerInterface $timer)
  {
    // do something here every minute
  }
}
```

The `@Schedule` annotation on the `invokedByTimer()` method schedules the invocation of this method every minute without a CRON configured or running. Such `Timers` can also be created programmatically. If you want to know more about it, have a look at our [example](https://github.com/appserver-io-apps/example).

Another option to create a schedule that invokes a Beans method in intervals, will be the manual implementation of a schedule. 

The following example creates a schedule on the Message Bean's `timeout()` method that'll be invokde every ten seconds, whereas the timer instance, passed to the `timeout()` method contains the message originall passed to the `onMessage()` method. This can be a directory that should be parsed every then seconds for new upload files, for example.

```php
<?php

namespace Namespace\Modulename;

/**
 * @MessageDriven
 */
class CreateAIntervalTimer extends AbstractMessageListener
  /**
   * Will be invoked when a new message for this message bean will be available.
   *
   * @param \AppserverIo\Psr\Pms\MessageInterface $message   A message this message bean is listen for
   * @param string                                $sessionId The session ID
   *
   * @return void
   * @see \AppserverIo\Psr\Pms\MessageListenerInterface::onMessage()
   */
  public function onMessage(MessageInterface $message, $sessionId)
  {

    // load the timer service registry
    $timerServiceRegistry = $this->getApplication()->search('TimerServiceContextInterface');

    // load the timer service for this class -> that allows us to invoke the
    // CreateAIntervalTimer::timeout() every 10 secondes
    $timerService = $timerServiceRegistry->lookup('CreateAIntervalTimer');

    // our single action timer should be invoked 10 seconds from now, every 10 seconds
    $initialExpiration = 10000000;
    $intervalDuration = 10000000;

    // we create the interval timer
    $timerService->createIntervalTimer($initialExpiration, $intervalDuration, new String($message->getMessage()));
   
    // update the message monitor for this message
    $this->updateMonitor($message);
  }

  /**
   * Invoked by the container upon timer expiration.
   *
   * @param \AppserverIo\Psr\EnterpriseBeans\TimerInterface $timer Timer whose expiration caused this notification
   *
   * @return void
   * @Timeout
  **/
  public function timeout(TimerInterface $timer)
  {
    // do something every ten seconds here
  }
}
```
> The Timer Service is available for Singleton and Stateless Session Beans as well as for Message Driven Beans!