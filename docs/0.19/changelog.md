---
layout: default
title: Changelog
---

# Changelog

All notable changes made in `0.19.x` releases are shown below. See the [full list of releases](/releases) for the complete changelog.

{% assign releases = site.github.releases | where_exp: "r", "r.name >= '0.19'" | where_exp: "r", "r.name < '0.20'" %}

{% for release in releases %}   
## [{{ release.name }}]({{ release.html_url }}) - {{ release.published_at | date: "%Y-%m-%d" }}
{{ release.body | markdownify }}
{% endfor %}

## Older Versions

See the [full list of releases](/releases) for the complete changelog.
