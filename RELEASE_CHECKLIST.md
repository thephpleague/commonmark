# Release Checklist

## Preparation

 - [ ] Ensure all tests are passing (check GitHub workflows).
 - [ ] Ensure changes are documented in `CHANGELOG.md`. Release titles should be linked to GitHub.
 - [ ] If breaking changes or deprecations are introduced, document the upgrade process in the doc site's upgrade page.
 - [ ] Bump the `branch-alias` in `composer.json` if needed.
 - [ ] Ensure all changes above make it into the `main` branch

## Documentation

 - [ ] Update the relevant documentation in `./docs/`
 - [ ] Remember to note the upgrade changes in the docs too
 - [ ] If releasing a new major or minor version, make sure to clone the previous one and make the necessary changes.  This will allow people to submit new features to the dev-main version. Don't forget to update version numbers in project.yml! And update the redirects too. Especially for /security/.
 - [ ] Build and preview the docs locally

## Release

 - [ ] Create a signed tag locally and push it up. Tag should be named `xx.yy.zz`.
 - [ ] Go to GitHub and add release notes from the relevant `CHANGELOG` section.
 - [ ] Resync project on <https://libraries.io/packagist/league%2Fcommonmark/>
 - [ ] Update release notes and supported branches on Tidelift
 - [ ] ???
 - [ ] PROFIT!!
