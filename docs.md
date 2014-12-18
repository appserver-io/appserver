---
layout: default
title: Docs
position: 30
permalink: /documentation.html
---

## Documentation
***

{% for section in site.data.docs %}
<h4>{{ section.title }}</h4>
<p>
  {% capture doc %}{% include docs/{{section.doc}} %}{% endcapture %}
  {{ doc | markdownify }}
</p>
{% endfor %}

