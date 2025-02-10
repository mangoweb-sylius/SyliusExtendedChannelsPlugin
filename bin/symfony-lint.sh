#!/usr/bin/env bash
set -euxo pipefail
IFS=$'\n\t'
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

bin/console --no-interaction lint:yaml src
bin/console --no-interaction lint:container
bin/console --no-interaction lint:twig src --show-deprecations
