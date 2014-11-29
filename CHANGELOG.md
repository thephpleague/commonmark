CHANGELOG for commonmark-php
============================

This changelog references the changes contained in each release.

* Pending changes

* **0.3.0**

 * Made compatible with spec version 0.12
 * Stack-based parsing now used for emphasis, links and images
 * Remove unnecessary distinction between ATX and Setext headers
 * Made renderer options configurable (issue #7)
 * Protected some of the internal renderer methods which shouldn't have been `public`
 * Minor code clean-up (including PSR-2 compliance)

* **0.2.1**

 * Removed "is" prefix from boolean methods
 * Updated to latest version of PHPUnit
 * Added simpler string replacement to a method
 * Target specific spec version

* **0.2.0**

 * Mirrored significant changes and improvements from stmd.js
 * Made compatible with spec version 0.10
 * Removed composer.lock
 * Removed fixed reference to jgm/stmd@0275f34
 * Updated location of JGM's repository
 * Allowed HHVM tests to fail without affecting overall build success

* **0.1.2**

 * Fix JS -> PHP null judgement (issue #4)
 * Added performance benchmarking tool (issue #2)
 * Updated phpunit dependency
 * Added more badges to the README

* **0.1.1**

 * Updated target spec (now compatible with jgm/stmd:spec.txt @ 2cf0750)
 * Adjust HTML output for fenced code
 * Add anchors to regexes
 * Adjust block-level tag regex (remove "br", add "iframe")
 * Fix incorrect handling of nested emphasis

* **0.1.0**

 * Initial commit (compatible with jgm/stmd:spec.txt @ 0275f34)

