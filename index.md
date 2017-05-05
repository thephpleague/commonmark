---
layout: default
title: Introduction
---

# Introduction

[![Author](https://img.shields.io/badge/author-@colinodell-blue.svg?style=flat-square)](https://twitter.com/colinodell)
[![Source Code](https://img.shields.io/badge/source-thephpleague%2Fcommonmark-blue.svg?style=flat-square)](https://github.com/thephpleague/commonmark)
[![Latest Version](https://img.shields.io/packagist/v/league/commonmark.svg?style=flat-square)](https://github.com/thephpleague/commonmark/releases)
[![Software License](https://img.shields.io/badge/license-BSD--3-orange.svg?style=flat-square)](https://github.com/thephpleague/commonmark/blob/master/LICENSE)<br />
[![Build Status](https://img.shields.io/travis/thephpleague/commonmark/master.svg?style=flat-square)](https://travis-ci.org/thephpleague/commonmark)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/thephpleague/commonmark.svg?style=flat-square)](https://scrutinizer-ci.com/g/thephpleague/commonmark/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/thephpleague/commonmark.svg?style=flat-square)](https://scrutinizer-ci.com/g/thephpleague/commonmark)
[![Total Downloads](https://img.shields.io/packagist/dt/league/commonmark.svg?style=flat-square)](https://packagist.org/packages/league/commonmark)

**league/commonmark** is a Markdown parser for PHP which supports the full [CommonMark spec](http://spec.commonmark.org). It is directly based on the [CommonMark JS reference implementation](https://github.com/jgm/CommonMark/tree/master/js) by [John MacFarlane](http://johnmacfarlane.net/) ([@jgm](https://github.com/jgm)).

## Goals

While other Markdown parsers focus on speed, or try to enable a wide range of flavors, this parser will strive to match the C and JavaScript implementations of CommonMark to make a logical and similar API.

We will always focus on CommonMark compliance over speed, but performance improvements will definitely happen during efforts to reach v1.0.0 and afterwards.

## Customization

This library allows you to add custom directives, renders, and more.  Check out the [Customization](/customization/overview/) section for more information.

## Integrations & Community Extensions

An updated list of pre-built integrations and extensions can be found in the [Related Packages](https://github.com/thephpleague/commonmark#related-packages) section of the `README`.

## Questions?

This library was created by [Colin O'Dell](https://www.colinodell.com). Find him on Twitter at [@colinodell](https://twitter.com/colinodell).
