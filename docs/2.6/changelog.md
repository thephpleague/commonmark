---
layout: default
title: Changelog
description: Important changes made in recent releases
redirect_from: /changelog/
---

# Changelog

All notable changes made in `2.x` releases are shown below. See the [full list of releases](/releases) for the complete changelog.

{% assign releases = site.github.releases | where_exp: "r", "r.name >= '2.6'" | where_exp: "r", "r.name < '3.0'" %}

{% for release in releases %}

## [{{ release.name }}]({{ release.html_url }}) - {{ release.published_at | date: "%Y-%m-%d" }}

{{ release.body | markdownify }}
{% endfor %}

## Older Versions

Please see the [full list of releases](/releases) for the complete changelog.
