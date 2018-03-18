# Release Checklist

To create a new release, branch off of master and...

 - [ ] Ensure all tests are passing (including Travis and StyleCI).
 - [ ] Update the `CommonMarkConverter::VERSION` constant.
 - [ ] Bump the `branch-alias` in `composer.json` if needed.
 - [ ] Ensure changes are documented in `CHANGELOG.md`. Release titles should be linked to Github.
 - [ ] If breaking changes or deprecations are introduced, document the upgrade process in `UPGRADE.md`.
 - [ ] Update the compatibility section in `README.md`.
 - [ ] Commit everything to that new release branch and push.
 - [ ] Create a release in Github; tag should be named `xx.yy.zz`. Copy `CHANGELOG` section into release notes.
 - [ ] ???
 - [ ] PROFIT!!
