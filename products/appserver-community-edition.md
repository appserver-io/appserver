---
layout: sections
title: Community Edition
meta_title: appserver.io community edition
meta_description: The Community Edition is your entry in the new and amazing world of multithreading enabled PHP. We hope you'll give it a try. Enjoy!
position: 30
group: Products
permalink: /products/community-edition.html
author: all
---

<section class="grey">
<div class="container">
{% capture sectioncontent %}{% include sections/community-edition.md %}{% endcapture %}
{{ sectioncontent | markdownify }}
</div>
</section>

<section class="darkgrey">
<div class="container">
{% capture sectioncontent %}{% include sections/webserver.md %}{% endcapture %}
{{ sectioncontent | markdownify }}
</div>
</section>

<section class="grey text-right">
<div class="container">
{% capture sectioncontent %}{% include sections/servlet-engine.md %}{% endcapture %}
{{ sectioncontent | markdownify }}
</div>
</section>

<section class="darkgrey">
<div class="container">
{% capture sectioncontent %}{% include sections/persistence-container.md %}{% endcapture %}
{{ sectioncontent | markdownify }}
</div>
</section>

<section class="grey text-right">
<div class="container">
{% capture sectioncontent %}{% include sections/message-queue.md %}{% endcapture %}
{{ sectioncontent | markdownify }}
</div>
</section>

<section class="darkgrey">
<div class="container">
{% capture sectioncontent %}{% include sections/timer-service.md %}{% endcapture %}
{{ sectioncontent | markdownify }}
</div>
</section>




