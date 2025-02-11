#!/usr/bin/env bash
set -euo pipefail
IFS=$'\n\t'
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# project root
cd "$(dirname "$DIR")"

set -x

if [ ! -f tests/Application/var/cache/dev/Tests_MangoSylius_ExtendedChannelsPlugin_KernelDevDebugContainer.xml ]; then
  php bin/console --env=dev cache:warmup --no-optional-warmers
fi

XDEBUG_MODE=off php --no-php-ini --define memory_limit=1G vendor/bin/phpstan analyse \
    --debug \
    --level max \
    src tests \
    "$@"
