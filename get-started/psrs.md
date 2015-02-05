---
layout: default
title: PSRs
group: Get Started
position: 90
permalink: /get-started/psrs.html
---

#<i class="fa fa-file-text-o"></i> PSRs
***

During the last years, [PHP-FIG](http://http://www.php-fig.org) has been established as the group that takes care about `PSRs`. `PSR` stands for `PHP Standard Recommendation`. Since they started with [PSR-0](http://www.php-fig.org/psr/psr-0) many things in the PHP ecosystem turn to the better, we think! So, with our `PSR's` we don't want to replace `PHP-FIG` nor getting in conflict with them. `appserver.io` is **NO** framwework per definition, but a infrastructure solution and a new player in the PHP ecosystem that provides functionality that has **NOT** been available yet or has partially been covered by webservers like [nginx](http://nginx.org) or [Apache](http://apache.org).

## Intention for us to write own `PSRs`
***

The intention behind our `PSRs` is, to give Admins, DevOps, Solution Providers, Developers and all other contributors of `appserver.io` a public, stable and easy to access API they can implement their applications and components against.

With version 1.0.0 we will provide the following, application and server specifc, `PSRs`

### Application specific `PSRs`

Applications specific `PSRs` describe all interfaces a developers needs to build an application that runs on `appserver.io` and uses `Server-Side component types` like `Servlets`, `Session` or `Message Driven` beans.

* [servlet](https://github.com/appserver-io-psr/servlet)
* [epb](https://github.com/appserver-io-psr/epb)
* [pms](https://github.com/appserver-io-psr/pms)
* [application](https://github.com/appserver-io-psr/application)
* [naming](https://github.com/appserver-io-psr/naming)

> Up with the final version 1.0.0 `Iron Horse`, we guarantee, that `appserver.io` will follow [Semantic Versioning](http://semver.org) and take care, that backwards compatibility for the [application specific PSRs](#application-specific-psrs) within a major version will **NEVER** be broken.

### Server specific `PSRs`

Beside `application specific PSRs` we're also working on `PSRs` that are implemented by containers, servers or modules `appserver.io` is built on. As theses `PSRs` are still in progress and our guarantee actually is **NOT** valid for those. We hope, that we can extend the guarantee with the next release, so it'll also include the `server specific PSRs` then.  

* [socket](https://github.com/appserver-io-psr/socket)
* [context](https://github.com/appserver-io-psr/context)
* [socket](https://github.com/appserver-io-psr/http-message)
* [deplyoment](https://github.com/appserver-io-psr/deployment)

## What does our `PSRs` mean for you
***

This means, that if you implement an application that works with 1.0.0, we guarantee that it'll run without changes for all releases < 2.0.0. The [application specific PSRs](#application-specific-psrs) will only be supplemented with new interfaces, whereas existing interfaces nor their methods will be touched.

## Our vision regarding `PSRs`
***

As our `PSRs` are on a higher level that the ones maintained by `PHP-FIG` we hope, that someday we've the possiblity to merge them together and let them be maintained and extended by the PHP community. As `appserver.io` actually is the only infrastructure (that we know) for PHP, completely written in PHP, the `PSRs` will hopefully help other developers and companies to get a better understanding of how things are implemented and encourages them to work on their own, almost compatible solutions, which will enable developers to deploy their applications on every `PHP Application Server` that implements these `PSRs`.

More information about our *PHP Standard Recommendations* will follow...

> Meanwhile you can checkout our [appserver-io-psr organisation on GitHub](<https://github.com/appserver-io-psr>).

