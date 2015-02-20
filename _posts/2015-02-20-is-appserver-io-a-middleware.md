---
layout: post
title: Is appserver.io a Middleware
meta_title: is appserver.io a middleware
meta_description: 
date: 2015-02-20 00:00:00
author: wagnert
version: 1.0.0
categories: [servlet-engine]
---

Recently, i came across a very interesting and informational blog post on [HTTP, Middleware, and PSR-7](https://mwop.net/blog/2015-01-08-on-http-middleware-and-psr-7.html) from a guy named Matthew Weier O`Phinney. He obviously worked hard on PSR-7, a PSR many developers out there are waiting to be accepted for.

Beside the post's content, the notable thing is, that this was the first time, i've found someone in the PHP universe, who ever thought about re-use capabilities of components across several frameworks, what shouldn't mean, that beside him, there is no one out there ;)

In the conclusion of his blog post, he wrote

> If PSR-7 is ratified, I think we have a strong foot forward towards building framework-agnostic web-focused components that have real re-use capabilities -- not just re-use within our chosen framework fiefdoms.

**Tanks for that!**

## PSR-0, Github and Composer

When i think back to times before PSR-0, Github and Composer, the only nameable library, that doesn't think in framework borders, was PEAR in my opinion. The first sentence on the [PEAR website](http://pear.php.net) is

> PEAR is a framework and distribution system for reusable PHP components.

PEAR components and package management are parts of my developers life for a long time, replaced by the `PSR-0, Github and Composer` combination since about 2 years. With the
introduction of PSR-0 and the increasing number of libraries implementing it, PHP has made a first step towards reusability of libraries. A small step for software development altogether, a big step for PHP particularly.

## PSR-7 in the context of appserver.io

When we 2004 started, with a component that covers the Controller part in an MVC pattern based framework, there was nothing available that looks like a framework. Funny people commented things like "Why should someone needs a framework, i'll be implement that on my own". Only some years later, nearly everyone uses a framework. Actually this means, that when you wrote an application using one of the frameworks, you're deeply coupled to it. As Matthew mentioned, one reason is, because PHP lacks of a good HTTP abstraction layer.

With [PSR-7](https://github.com/php-fig/fig-standards/tree/master/proposed) accepted, again, another big step towards reusability, maintainability and standardization in the PHP ecosystem can be done.

Working for a PHP service provider that provides Magento and TYPO3 solutions since many years, we faced situations, where a middleware would be a good solution to help solve recurring problems and reduce costs. So, in case of a framework change, in most cases, it'll be the best to solution, to rewrite the software from the scratch. A middleware, based on PSR-7, together with frameworks that implements PSR-7 would allow a real refactoring that will save time, money and last but not least hopefully good and tested code.

appserver.io is a typical application server that comes with a middleware out-of-the-box.
When we started development about 2 years ago, we where looking for a HTTP abstraction
layer we can build the HTTP related middleware around. All available implementations has been implemented from a client point of view, what it makes impossible for us to integrate them. During implementation we see PSR-7 and decided to implement our HTTP server component against it.

In one ouf our previous blog posts you can read a brief introduction to the [Servlet-Engine](<{{ "/get-started/documentation/servlet-engine.html" | prepend: site.baseurl }}>), the middleware service of appserver.io. As Matthew described in his blog post, the `Servlet-Engine` is the first real world implementation of his `MiddlewareRunner`. The middleware implementation is represented by a `Servlet`. By adding servlets, a developer can already implement components, that are compliant with an early PSR-7 version. Let's have a quick look at the `ServletInterface`

```php
<?php
interface ServletInterface
{
   
    // other methods
   
    /**
     * Request and Response both implements the interfaces provided by PSR-7.
     */
    public function service(
    	ServletRequestInterface $servletRequest, 
    	ServletResponseInterface $servletResponse);
}

```  

To add a Servlet, or better a `Middleware`, to the `MiddlewareRunner` you can simply annotate it like this

```php
<?php

/**
 * @Route(name="blog", urlPattern={"/blog.do", "/blog.do*"})
 */
class BlogMiddleware extends HttpServlet
{
   
    /**
     * Request and Response both implements the interfaces provided by PSR-7.
     */
    public function doPost(
    	ServletRequestInterface $servletRequest, 
    	ServletResponseInterface $servletResponse
    ) {
    	// place your middleware functionality here ...
    }
}
```

The `ServletEngine` will process the request and routes it to the component with the matching annotation and invokes the appropriate method, in that example `doPost` for a HTTP `POST` request.

As PSR-7 unfortunately has not been accepted yet, we copied a early version to a separate Github repository which allows us to add it as Composer dependency. As soon as PSR-7 will be accepted, we'll refactor appserver.io and implement the original one.

Hopefully this will be soon!

## Conclusion

appserver.io **IS** a `Middleware`, anyway by the definition of Matthew and from our point of view!

As appserver.io bootstraps applications on startup, it is possible to massively reduce the process of dispatching a request. This will result in promising benchmark results when running our JMeter tests. Nevertheless, we and PHP itself are closely at a threshold to enter a new era, the era of PHP middleware. We think that is awesome!

Altogether i came to the conclusion, that standardization started with PHP-FIG and the available PSRs are a good start for PHP to get rid of its amateur smell, making the next step into professional software development. Nevertheless, it'll be a long way, as PSRs are not enough to write solid, reusable components and libraries. It'll also be necessary, that developers will start to stick on contracts introduced by the PSRs together with following semantic versioning to guarantee compatibility and stability.

If this will not be the case, PSRs will only be a nice vision, because if you have interfaces, but implementing applications, components and libraries that breaks compatibility by ignoring semantic versioning, the basic idea behind the PSRs will nearly be useless.
