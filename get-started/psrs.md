---
layout: default
title: PSRs
group: Get Started
position: 90
permalink: /get-started/psrs.html
---

#<i class="fa fa-file-text-o"></i> PSRs
***

During the last years, [PHP-FIG](http://http://www.php-fig.org) has been established as the group that takes care about `PSR's`. `PSR` stands for `PHP Standard Recommendation`. Since they started with [PSR-0](http://www.php-fig.org/psr/psr-0) many things in the PHP ecosystem turn to the better, we think! So, with our `PSR's` we don't want to replace PHP-FIG nor getting in conflict with them. `appserver.io` is no framwework, but a infrastructure solution and a new player in the PHP ecosystem that provides functionality that has **NOT** been available yet or has partially been covered by webservers like [nginx](http://nginx.org) or [Apache](http://apache.org).

## Intention for us to write own `PSR's`

The intention behind our `PSR's` is, to give Admins, DevOps, Solution Providers, Developers and all other contributors of `appserver.io` a public, stable and easy to access API they can implement their applications and components against.

> We guarantee, that appserver.io will follow semantic versioning and take care, that backwards compatibility within a major version will **NEVER** be broken.

## What does this mean for you

This means, that if you implement an application that works with 1.0.0, we guarantee that it'll work without changes for all releases, up to 2.x. PSR's will only be supplemented with new interfaces and methods, whereas existing interfaces and methods will **NOT** be touched.

## What is our vision

As our `PSR's` are on a higher level that the ones maintained by `PHP-FIG` we hope, that someday we've the possiblity to merge them together and let them be maintained and extended by the PHP community. As `appserver.io` actually is the only infrastructure for PHP completely written in PHP, the `PSR's` will help other developers and companies to get a better understanding of how things are implemented and encourages them to work on their own, hopefully almost compatible solutions, which enables developers to deploy their applications on every `PHP Application Server` that implements that `PSR's`.

More information about our *PHP Standard Recommendations* will follow...

> Meanwhile you can checkout our [appserver-io-psr organisation on GitHub](<https://github.com/appserver-io-psr>).

