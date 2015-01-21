---
layout: post
title:  Persistence-Container Basics
date:   2014-12-30 18:00:00
author: wagnert
version: 1.0.0beta4
categories: [Persistence-Container]
---

Maybe you had a look at our previous post about the [Servlet-Engine Basics](/servlet-engine/2014/12/24/servlet-engine-basics.html). Beside the Servlet-Engine, the [Persistence-Container](http://appserver.io/documentation/persistence-container.html) is one of the main services appserver.io provides. The name Persistence-Container, can lead to some missunderstanding in our case, as many people think that it mostly refers to database persistence. But persisting data to a database is only one functionality the Persistence-Container provides, but by far not the most important one.

### New options using a Persistence-Container
***

As not persisting data to a database is the main purpose of a Persistence-Container, you may wonder about other reasons you may use it. As PHP till now was used as a scripting language, it'll lack of the possiblity to have objects persistent in memory. The Persistence-Container gives you the possiblity to exactly do this. This is, admittedly, not a problem it can solve for you, but in fact it is a powerful option. This option, beside performance of course, gives you many possibilities you will not benefit from when working with the well known LAMP stack. This post is all about the possibilities the Persistence-Container provides and how they can enable you to write enterprise applications.


 
