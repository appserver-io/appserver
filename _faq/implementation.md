---
layout: faq
title: Implementation
position: 20
group: FAQ
subNav:
  - title: How to prevent segmentation faults?
    href: how-to-prevent-segmentation-faults?
permalink: /get-started/faq/implementation.html
---

### How to prevent segmentation faults?
> Stackable objects created in specific thread contexts have to be hold in this context while other objects
> references those stackable objects. If The context will be destroyed by e.g. an ending thread, all references will
> point to an invalid memory address which ends up in a segmentation fault. If the context will exists while the
> process runs you don't have to worry about at all.
