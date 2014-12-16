---
layout: news
title: Releases
position: 60
group: News
permalink: /news/releases.html
author: all
---

## appserver releases
<hr>

{% for post in site.categories.release %}
  {% include news_item.html %}
{% endfor %}
