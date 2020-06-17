# Contributing

Contributions are **welcome** and will be fully **credited**. We accept contributions via Pull Requests on [GitHub](https://github.com/thephpleague/commonmark).

## Project Goals

Please keep these project goals in mind as you propose changes:

* Fully support the [CommonMark spec] (100% compliance)
* Provide an extensible parser/renderer which users may customize as needed
* Continuously improve performance without sacrificing quality or compliance
* Match the underlying logic of the official reference implementations of CommonMark

## Pull Requests

- **[PSR-2 Coding Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)** - The easiest way to apply the conventions is to install [PHP Code Sniffer](https://pear.php.net/package/PHP_CodeSniffer).

- **Add tests!** - Your patch won't be accepted if it doesn't have tests.

- **Document any change in behaviour** - Make sure the `README.md` and any other relevant documentation are kept up-to-date.

- **Consider our release cycle** - We try to follow [SemVer v2.0.0](https://semver.org/). Randomly breaking public APIs is not an option.

- **Create feature branches** - Don't ask us to pull from your default branch.

- **One pull request per feature** - If you want to do more than one thing, send multiple pull requests.

- **Send coherent history** - Make sure each individual commit in your pull request is meaningful. If you had to make multiple intermediate commits while developing, please squash them before submitting.


## Running Tests

``` bash
$ composer test
```


**Happy coding**!
