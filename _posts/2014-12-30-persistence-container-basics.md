---
layout: post
title:  Persistence-Container Basics
date:   2014-12-30 18:00:00
author: wagnert
version: 1.0.0beta4
categories: [Persistence-Container]
---

Maybe you had a look at our previous post about the [Servlet-Engine Basics](/servlet-engine/2014/12/24/servlet-engine-basics.html). Beside the Servlet-Engine, the [Persistence-Container](http://appserver.io/documentation/persistence-container.html) is one of the main services an application server has to provide. The name Persistence-Container can lead to some missunderstanding, as many people think that it mostly refers to database persistence. But persisting data to a database is only one functionality a Persistence-Container provides, but by far not the most important one. Its more about holding objects persistent in memory, because this, beside performance of course, gives you many possibilities you will not benefit from when working with the well known LAMP stack. This post is all about the possibilities a Persistence-Container provides and how you can use them.

### Problems without a Persistence-Container
***

As not persisting data to a database is the main purpose of a Persistence-Container, you may wonder, what other problems you'll have. Maybe you didn't thought that there could be problems, because for every problem, there'll be a solution. That is, as in many other cases, also true for the problems you can solve with a Persistence-Container. But using a Persistence-Container, there are other, in most cases, more efficient solutions.


 
