#!/bin/sh
set -e

echo "Starting the Jekyll Action"

if [ -z "${JEKYLL_PAT}" ]; then
  echo "No token provided. Please set the JEKYLL_PAT environment variable."
  exit 1
fi

if [ -n "${INPUT_JEKYLL_SRC}" ]; then
  JEKYLL_SRC=${INPUT_JEKYLL_SRC}
  echo "::debug ::Using parameter value ${JEKYLL_SRC} as a source directory"
elif [ -n "${SRC}" ]; then
  JEKYLL_SRC=${SRC}
  echo "::debug ::Using SRC environment var value ${JEKYLL_SRC} as a source directory"
else
  JEKYLL_SRC=$(find . -path ./vendor/bundle -prune -o -name '_config.yml' -exec dirname {} \;)
  echo "::debug ::Resolved ${JEKYLL_SRC} as a source directory"
fi

cd $JEKYLL_SRC

echo "::debug ::Starting bundle install"
bundle config path vendor/bundle
bundle install --jobs 4 --retry 3
echo "::debug ::Completed bundle install"

JEKYLL_ENV=production bundle exec jekyll build -d build
echo "Jekyll build done"

cd build

# No need to have GitHub Pages to run Jekyll
touch .nojekyll

# Is this a regular repo or an org.github.io type of repo
case "${GITHUB_REPOSITORY}" in
  *.github.io) remote_branch="master" ;;
  *)           remote_branch="gh-pages" ;;
esac

if [ "${GITHUB_REF}" = "refs/heads/${remote_branch}" ]; then
  echo "Cannot publish on branch ${remote_branch}"
  exit 1
fi

echo "Publishing to ${GITHUB_REPOSITORY} on branch ${remote_branch}"

# Hand the token to git through a credential helper instead of embedding it in
# the remote URL. When GitHub answers the push with a redirect, git re-reads
# credentials from the redirected URL, drops the embedded token, then tries to
# prompt for a username and fails ("could not read Username").
export GIT_TERMINAL_PROMPT=0
remote_repo="https://github.com/${GITHUB_REPOSITORY}.git" && \
git init && \
git config user.name "${GITHUB_ACTOR}" && \
git config user.email "${GITHUB_ACTOR}@users.noreply.github.com" && \
git config credential.helper '!f() { echo "username=x-access-token"; echo "password=${JEKYLL_PAT}"; }; f' && \
git add . && \
git commit -m "jekyll build from Action ${GITHUB_SHA}" && \
git push --force $remote_repo master:$remote_branch && \
rm -fr .git && \
cd ..

exit $?
