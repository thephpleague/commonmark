# Release Checklist

## Preparation

 - [ ] Ensure all tests are passing (including Travis and StyleCI).
 - [ ] Ensure changes are documented in `CHANGELOG.md`. Release titles should be linked to Github.
 - [ ] If breaking changes or deprecations are introduced, document the upgrade process in `UPGRADE.md`.
 - [ ] Update the compatibility section in `README.md`.
 - [ ] Bump the `branch-alias` in `composer.json` if needed.
 - [ ] Ensure all changes above make it into the `master` branch

## Documentation

 - [ ] Update the relevant documentation in `./docs/`
 - [ ] Migrate any relevant `UPGRADE` changes into the docs
 - [ ] If releasing a new major version, clone the previous one and make the necessary changes. Don't forget to update version numbers in links and the homepage!
 - [ ] Build and preview the docs locally

## Release

 - [ ] Create a new release branch.
 - [ ] Update the `CommonMarkConverter::VERSION` constant and commit just that one change to that release branch. Make sure tests still pass.
 - [ ] Create a release in Github **BASED ON THE RELEASE BRANCH, NOT MASTER!**; tag should be named `xx.yy.zz`. Copy `CHANGELOG` section into release notes.
 - [ ] Remove that release branch once tagged
 - [ ] Bump constraints in league/commonmark-extras and tag a new release
 - [ ] ???
 - [ ] PROFIT!!
