---
layout: default
title: Upgrading from 0.17 to 0.18
---

# Upgrading from 0.17 to 0.18

No breaking changes were introduced, but we did add a new interface: `ConverterInface`. Consider depending on this interface in your code instead of the concrete implementation. (See [#330](https://github.com/thephpleague/commonmark/issues/330))