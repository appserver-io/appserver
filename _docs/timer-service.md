---
layout: docs
title: Timer Service
position: 100
permalink: /docs/timer-service.html
---

In most of your projects you have the need to schedule things to be processed in regular intervals
or at a given date in future. As PHP itself is a scripting language it lacks of such functionality
and developers use utilities like CRON when working on Mac OS X or a Linux distribution. So if you
are working on Windows, it's a bit more complicated as there is also a Tool called Scheduler, but
that is not as simple to use as CRON is. This is the point where a Timer Service comes into the game
and will be very good and simple to use option.

As CRON does, the Timer Service allows you to schedule processing your functionality at a given
date or in regular intervals. In contrast to CRON it allows you to schedule processing the methods
of your Beans in such a way. How can this be done? I'm sure you know the answer: Simple add an
annotation to your method

```php

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

The `@Schedule` annotation on the `invokedByTimer()` method schedules the invocation of this
method every minute without the need to have an CRON configured or running. Such `Timers` can
also be created programatically, if you want to know more about that, have a look at our [example](https://github.com/appserver-io-apps/example).

> Actually we don't support seconds as period you can schedule (see Issue [#300](#300)).