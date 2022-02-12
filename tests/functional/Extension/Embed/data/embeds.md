This is an embed:

https://www.youtube.com/watch?v=dQw4w9WgXcQ

So is this, with only three spaces of indentation:

   https://www.youtube.com/watch?v=dQw4w9WgXcQ

This is not, because it's fully indented:

    https://www.youtube.com/watch?v=dQw4w9WgXcQ

This is not, because it has extra bits after the URL:

https://www.youtube.com/watch?v=dQw4w9WgXcQ <-- you gotta watch this!

This is not, because it's in a fenced code block:

```md

https://www.youtube.com/watch?v=dQw4w9WgXcQ

```

And this isn't either because it's inline: ![](https://www.youtube.com/watch?v=dQw4w9WgXcQ)

Embeds can't be nested in other blocks:

- https://www.youtube.com/watch?v=dQw4w9WgXcQ
    - https://www.youtube.com/watch?v=dQw4w9WgXcQ

This isn't valid because it's a lazy paragraph continuation:
https://www.youtube.com/watch?v=dQw4w9WgXcQ

https://www.youtube.com/watch?v=dQw4w9WgXcQ
^ This is fine, though
