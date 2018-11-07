#!/usr/bin/env bash
set -euo pipefail
IFS=$'\n\t'
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

# project root
cd "$(dirname "$DIR")"

set -x
tests/Application/bin/console --env=test cache:warmup
vendor/bin/phpstan analyse \
	--level 6 \
	--memory-limit 1G \
	--configuration phpstan.neon \
	src tests
