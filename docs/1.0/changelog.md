---
layout: default
title: Changelog
description: Important changes made in recent releases
redirect_from: /0.20/changelog/
---

# Changelog

All notable changes made in `1.0` - `1.2` releases are shown below. See the [full list of releases](/releases) for the complete changelog.

{% assign releases = site.github.releases | where_exp: "r", "r.name >= '1.0'" | where_exp: "r", "r.name < '1.3'" %}

{% for release in releases %}

## [{{ release.name }}]({{ release.html_url }}) - {{ release.published_at | date: "%Y-%m-%d" }}

{{ release.body | markdownify }}
{% endfor %}

## Older Versions

Please see the [full list of releases](/releases) for the complete changelog.
