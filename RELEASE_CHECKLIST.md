# Release Checklist

## Preparation

 - [ ] Ensure all tests are passing (including Travis and StyleCI).
 - [ ] Ensure changes are documented in `CHANGELOG.md`. Release titles should be linked to Github.
 - [ ] If breaking changes or deprecations are introduced, document the upgrade process in the doc site's upgrade page.
 - [ ] Bump the `branch-alias` in `composer.json` if needed.
 - [ ] Ensure all changes above make it into the `master` branch

## Documentation

 - [ ] Update the relevant documentation in `./docs/`
 - [ ] Remember to note the upgrade changes in the docs too
 - [ ] If releasing a new major version, clone the previous one and make the necessary changes. Don't forget to update version numbers in project.yml! And update the redirects too.
 - [ ] Build and preview the docs locally

## Release

 - [ ] Create a new release branch.
 - [ ] Update the `CommonMarkConverter::VERSION` constant and commit just that one change to that release branch. Make sure tests still pass.
 - [ ] Create a signed tag locally and push it up. Tag should be named `xx.yy.zz`.
 - [ ] Go to Github and add release notes from the relevant `CHANGELOG` section.
 - [ ] Bump constraints in league/commonmark-extras (and other official extensions) and tag a new release of them
 - [ ] ???
 - [ ] PROFIT!!
