---
layout: sections
title: Enterprise Edition
meta_title: appserver.io enterprise edition
meta_description: We have a great feature set for our enterprise edition. But we are still looking for partners giving input on it. Interested? Please contact us!
position: 50
group: Products
permalink: /products/enterprise-edition.html
author: all
---

<section class="grey">
<div class="container">
{% capture sectioncontent %}{% include sections/enterprise-edition.md %}{% endcapture %}
{{ sectioncontent | markdownify }}
</div>
</section>

<section class="blue">
<div class="container">
<div class="row">
<div class="col-md-2">
<img class="img-responsive img-hover" src="/assets/img/icon/webserver_invert.png" alt="" style="width:100%">
</div>
<div class="col-md-10">
{% capture sectioncontent %}{% include sections/clustering.md %}{% endcapture %}
{{ sectioncontent | markdownify }}
</div>
</div>
</div>
</section>

<section class="grey text-right">
<div class="container">
<div class="row">
<div class="col-md-10">
{% capture sectioncontent %}{% include sections/load-balancing.md %}{% endcapture %}
{{ sectioncontent | markdownify }}
</div>
<div class="col-md-2">
<img class="img-responsive img-hover" src="/assets/img/icon/rewrite_engine.png" alt="" style="width:100%">
</div>
</div>
</div>
</section>

<section class="midgrey">
<div class="container">
<div class="row">
<div class="col-md-2">
<img class="img-responsive img-hover" src="/assets/img/icon/servlet_engine.png" alt="" style="width:100%">
</div>
<div class="col-md-10">
{% capture sectioncontent %}{% include sections/dashboard.md %}{% endcapture %}
{{ sectioncontent | markdownify }}
</div>
</div>
</div>
</section>

<section class="grey text-right">
<div class="container">
<div class="row">
<div class="col-md-10">
{% capture sectioncontent %}{% include sections/event-monitor.md %}{% endcapture %}
{{ sectioncontent | markdownify }}
</div>
<div class="col-md-2">
<img class="img-responsive img-hover" src="/assets/img/icon/persistence_container.png" alt="" style="width:100%">
</div>
</div>
</div>
</section>

<section class="blue">
<div class="container">
<div class="row">
<div class="col-md-2">
<img class="img-responsive img-hover" src="/assets/img/icon/message_queue_invert.png" alt="" style="width:100%">
</div>
<div class="col-md-10">
{% capture sectioncontent %}{% include sections/authentication-and-authorization-services.md %}{% endcapture %}
{{ sectioncontent | markdownify }}
</div>
</div>
</div>
</section>
