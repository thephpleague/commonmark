# Release Checklist

## Preparation

 - [ ] Confirm what is actually unreleased with `git log <last-tag>..origin/main` - release branches get merged back into `main`, so commit order alone is misleading.
 - [ ] Ensure all tests are passing (check GitHub workflows).
 - [ ] Ensure changes are documented in `CHANGELOG.md`. Release titles should be linked to GitHub.
 - [ ] If breaking changes or deprecations are introduced, document the upgrade process in the doc site's upgrade page.
 - [ ] Bump the `branch-alias` in `composer.json` if needed.
 - [ ] Ensure all changes above make it into the `main` branch

## Documentation

 - [ ] Update the relevant documentation in `./docs/`
 - [ ] Remember to note the upgrade changes in the docs too
 - [ ] If the release adds an option for hardening untrusted input, give it a section in `docs/2.x/security.md`
 - [ ] Build and preview the docs locally (they publish themselves on push to `main` and on release)

## Release

 - [ ] For a new minor: branch `xx.yy` from `main`, push it, make it the repository's default branch, and follow that locally with `git remote set-head origin --auto`.
 - [ ] Create a signed tag locally and push it up. Tag should be named `xx.yy.zz`. Confirm it with `git verify-tag`.
 - [ ] Go to GitHub and add release notes from the relevant `CHANGELOG` section.
 - [ ] Resync project on <https://libraries.io/packagist/league%2Fcommonmark/> (Packagist needs no action - it updates via webhook)
 - [ ] Update release notes and supported branches on Tidelift
 - [ ] ???
 - [ ] PROFIT!!
