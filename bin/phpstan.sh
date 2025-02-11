#!/usr/bin/env bash
set -euo pipefail
IFS=$'\n\t'
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# project root
cd "$(dirname "$DIR")"

set -x
tests/Application/bin/console cache:warmup --no-optional-warmers
XDEBUG_MODE=off php -d memory_limit=1G vendor/bin/phpstan analyse \
    -v --error-format=table \
    --debug \
    --level max \
    src tests \
    "$@"
