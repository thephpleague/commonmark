---
layout: default
title: Changelog
permalink: /changelog/
---

Changelog
=========

{% for release in site.github.releases %}   
  ## {{ release.name }}
  {{ release.body | markdownify }}
{% endfor %}
