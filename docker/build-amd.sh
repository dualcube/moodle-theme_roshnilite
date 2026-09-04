#!/bin/bash
# Rebuilds amd/build/*.min.js using Moodle core's own Gruntfile/rollup/babel/
# terser pipeline - not a generic minifier run by hand.
#
# Why this exists: moodle-plugin-ci's "Grunt" CI step runs `grunt amd` inside
# a real Moodle core checkout with our plugin dropped in, then does a git diff
# on amd/build/* - if it differs at all from what's committed, the step fails
# with "File is stale and needs to be rebuilt". A plain `terser` invocation
# does not reproduce that output: core's pipeline keeps the JSDoc header,
# uses Babel's gentler variable naming instead of terser's aggressive mangle,
# and - critically - adds the named AMD module id ("theme_roshnilite/xxx") as
# define()'s first argument, which a bare `terser amd/src/x.js` never adds.
# There is no way to reproduce this without actually running core's toolchain.
#
# First run clones Moodle core and runs its (large) npm install - this is
# slow, one-time, and cached in .moodle-core-cache/ (gitignored). Every run
# after that just syncs this theme's source in and re-runs grunt, which is
# fast.

set -euo pipefail

THEME_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
CACHE_DIR="${THEME_DIR}/.moodle-core-cache"
# Any branch this theme supports works here: the classic AMD/rollup/babel/
# terser build pipeline has been stable across Moodle versions for years, so
# one branch's toolchain output matches what every supported branch's CI
# check expects. "main" is used so a first-time clone always tracks Moodle's
# current dev toolchain rather than pinning to a specific stable branch.
CORE_BRANCH="main"
THEME_IN_CORE="${CACHE_DIR}/public/theme/roshnilite"

if [ ! -d "${CACHE_DIR}/.git" ]; then
    echo "No cached Moodle core checkout found - cloning ${CORE_BRANCH} (one-time, several minutes)..."
    rm -rf "${CACHE_DIR}"
    git clone --branch "${CORE_BRANCH}" --depth 1 https://github.com/moodle/moodle.git "${CACHE_DIR}"
fi

if [ ! -d "${CACHE_DIR}/node_modules" ]; then
    echo "Installing Moodle core's npm dependencies (one-time, several minutes)..."
    (cd "${CACHE_DIR}" && npm ci)
fi

echo "Syncing theme source into the cached core checkout..."
mkdir -p "${THEME_IN_CORE}"
rsync -a --delete \
    --exclude='.git' --exclude='node_modules' --exclude='vendor' --exclude='docker' \
    --exclude='.moodle-core-cache' \
    "${THEME_DIR}/" "${THEME_IN_CORE}/"

echo "Running Moodle core's grunt amd task..."
(cd "${CACHE_DIR}" && npx grunt amd --root=public/theme/roshnilite)

echo "Copying the rebuilt amd/build/ back into this repo..."
rsync -a "${THEME_IN_CORE}/amd/build/" "${THEME_DIR}/amd/build/"

echo "Done. Review the diff (git diff amd/build/) before committing."
