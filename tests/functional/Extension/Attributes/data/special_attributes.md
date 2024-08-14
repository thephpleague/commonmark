Header 1            {#header1}
========

## Header 2 ##
{#header2}

## The Site    {.main}

## The Site ##
{.main .shine #the-site}

[link](url){#id1 .class1} ![img](url){#id2 .class2}
**bold**{.bold} paragraph
{#text}

this is just normal text {.main .shine #the-site}

some { brackets

some } brackets

some { } brackets

A link inside of an emphasis tag: *[link](http://url.com){target="_blank"}*.

Attributes without quote and non-whitespace char [link](http://url.com){target=_blank}

Attributes without quote and non-whitespace char and a dot [link](http://url.com){target=_blank}.

Multiple attributes without quote and non-whitespace char and a dot [link](http://url.com){#id .class target=_blank}.

![image](/assets/image.jpg){some-text}

![image](/assets/image.jpg){boolean-attribute="boolean-attribute"}

A paragraph containing {{ mustache }} templating

A paragraph ending with {{ mustache }} templating

{{ mustache }} A paragraph starting with mustache templating

a. [Some{text}](https://example.com).

b. [Some{.text}](https://example.com).

c. [Some](https://example.com){.text}.

d. [Some{text}](https://example.com).

e. [Some](https://example.com){text="text"}.

f. [Some](https://example.com){text=true}.

g. [Some{{text}}](https://example.com).

{hello="hello"}
some

{.test hello="hello"}
some

{hello="hello" .test}
some

{hello="hello" goodbye="goodbye"}
some
