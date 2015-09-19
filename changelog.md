---
layout: default
title: Changelog
permalink: /changelog/
---

Changelog
=========

{% for release in site.github.releases %}   
  ## [{{ release.name }}]({{ release.html_url }}) - {{ release.published_at | date: "%Y-%m-%d" }}

  {{ release.body | markdownify }}
{% endfor %}
