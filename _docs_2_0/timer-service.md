---
layout: docs_2_0
title: Timer Service
meta_title: appserver.io timer service documentation
meta_description: As CRON does, the Timer Service allows you to schedule processing your functionality at a given date or in regular intervals.
position: 110
group: Docs
permalink: /get-started/documentation/2.0/timer-service.html
---

In most of your projects, you need to schedule things at regular intervals
or at a given date in future. As PHP itself is a scripting language, it lacks this functionality. Thus, developers use utilities like CRON when working on Mac OS X or a Linux distribution. If you
are working on Windows, it is a bit more challenging. There is a tool called Scheduler, which
is not as simple to use as CRON. At this point Timer Service, a good and simple option, comes into play.

As CRON, the Timer Service allows you to schedule processing your functionality at a given
date or regular intervals. In contrast to CRON, it allows you to schedule processing the methods
of your Beans in such a way. This is done by simply adding an annotation to your method.

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

The `@Schedule` annotation on the `invokedByTimer()` method schedules the invocation of this
method every minute without a CRON configured or running. Such `Timers` can
also be created programmatically. If you want to know more about it, have a look at our [example](https://github.com/appserver-io-apps/example).

> Currently, we do not support seconds as a period you can schedule (see Issue [#300](#300)).
