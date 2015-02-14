---
layout: default
title: PSR
meta_title: appserver.io PSRs
meta_description: PSR stands for **PHP Standard Recommendation**. Since PSR-0 appeared, a lot in the PHP ecosystem improved in our opinion!
group: Get Started
position: 90
permalink: /get-started/psrs.html
---

#<i class="fa fa-file-text-o"></i> PSRs
***

After years, [PHP-FIG](http://www.php-fig.org) has been established as the group witch takes care about the `PSR` Standard. `PSR` stands for `PHP Standard Recommendation`. Since they started with [PSR-0](http://www.php-fig.org/psr/psr-0) a lot in the PHP ecosystem improved in our opinion! So, with our `PSRs` we don't want to replace `PHP-FIG` neither getting in conflict with them. `appserver.io` is **not a framework** per definition, but a infrastructure solution and a new player in the PHP ecosystem that provides functionality witch has **NOT** been available so far or was partially been covered by webservers like [nginx](http://nginx.org) or [Apache](http://apache.org).

## Intention behind our own `PSRs`
***

The intention behind our `PSRs` is, to provide Admins, DevOps, Solution Providers, Developers and all other contributors of `appserver.io` a public, stable and easy accessible API witch makes it possible to implement own applications and components.

With version 1.0.0 we will provide sequenced application specific `PSRs`

### Application specific `PSRs`

Application specific `PSRs` describe all interfaces a developer needs to build a application that runs on `appserver.io` and uses `Server-Side component types` like `Servlets`, `Session` or `Message Driven` beans.

* [servlet](https://github.com/appserver-io-psr/servlet)
* [epb](https://github.com/appserver-io-psr/epb)
* [pms](https://github.com/appserver-io-psr/pms)
* [application](https://github.com/appserver-io-psr/application)
* [naming](https://github.com/appserver-io-psr/naming)
* [mop](https://github.com/appserver-io-psr/mop)

> With the final ersion 1.0.0 `Iron Horse`, we guarantee, that `appserver.io` will follow [Semantic Versioning](http://semver.org) and take cares of backward compatibility for the `application specific PSRs`. The major version will never be broken.

### Server specific `PSRs`

Beside [application specific PSRs](#application-specific-psrs) we're also working on `PSRs` witch are implemented by containers, servers or modules `appserver.io` is built on. 

* [socket](https://github.com/appserver-io-psr/socket)
* [context](https://github.com/appserver-io-psr/context)
* [socket](https://github.com/appserver-io-psr/http-message)
* [deplyoment](https://github.com/appserver-io-psr/deployment)

> As long as the `PSRs` are in progress, there will be no guarantee for our `PSRs`. We hope we can extend the guarantee with the next releases, so it will also include the `server specific PSRs`. 

## What does our `PSRs` mean for you
***

In Case you implement an application that works with 1.0.0, we guarantee that it will run without changes on all releases < 2.0.0. The [application specific PSRs](#application-specific-psrs) will only be supplemented with new interfaces that effect existing interfaces otherwise their methods will not be effected.

## Our vision regarding `PSRs`
***

As our `PSRs` are on a higher level then the `PSRs` maintained by `PHP-FIG` we hope someday we will have the possiblity to merge them together and let them be maintained and extended by the PHP community. As `appserver.io` actually is the only infrastructure (to the best of our knowledge) for PHP, completely written in PHP, the `PSRs`. the `PSRs` will hopefully help other developers and companies to get a better understanding of how certain mechanisms are implemented and encourages People to work on their autonomous, almost compatible solutions, which will enable developers to deploy their applications on every `PHP Application Server` that implements the `PSRs`.

> Soon, you will find detailed information for each PSR on our [appserver-io-psr organisation page on GitHub](<https://github.com/appserver-io-psr>).

