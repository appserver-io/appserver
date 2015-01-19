---
layout: default
title: Features
position: 20
group: Products
permalink: /products/features.html
subnavigation: features
author: all

webserver:
  - title       : Webserver
    description : A fully HTTP/1.1 compliant webserver which can process requests over HTTP as well as HTTPS.
    link   : /products/features/webserver.html
    image  : /assets/img/icon/webserver.png

rewrite-engine:
  - title       : Rewrite Engine
    description : Easy to use Rewrite Engine which is compatible to Apache rewrites<br/><br/><br/>
    link   : /products/features/rewrite-engine.html
    image  : /assets/img/icon/rewrite_engine.png
    
servlet-engine:
  - title       : Servlet Engine
    description : The Servlet Engine provides a web container that enables developers to load applications and objects in memory<br/><br/><br/>
    link   : /products/features/servlet-engine.html
    image  : /assets/img/icon/servlet_engine.png
    
message-queue:
  - title       : Message Queue
    description : The Message Queue provides services that enables developers to process messages asynchronously<br/><br/><br/>
    link   : /products/features/message-queue.html
    image  : /assets/img/icon/message_queue.png
    
timer-service:
  - title       : Timer Service
    description : The Timer Service enables the execution of methods at a determined point of time <br/><br/><br/>
    link   : /products/features/timer-service.html
    image  : /assets/img/icon/timer_service.png
    
persistence-container:
  - title       : Persistence Container
    description : The Persistence Container enables developers to hold objects, so-called beans, in memory.<br/><br/><br/>
    link   : /products/features/persistence-container.html
    image  : /assets/img/icon/persistence_container.png

---

## <i class="fa fa-bars"> Features</i>
***

<div class="row">
    {% include widgets/feature-box.html box = page.webserver %}
    {% include widgets/feature-box.html box = page.rewrite-engine %}
    {% include widgets/feature-box.html box = page.servlet-engine %}
    {% include widgets/feature-box.html box = page.message-queue %}
    {% include widgets/feature-box.html box = page.timer-service %}
    {% include widgets/feature-box.html box = page.persistence-container %}
</div>