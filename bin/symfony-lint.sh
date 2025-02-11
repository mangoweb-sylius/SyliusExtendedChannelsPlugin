#!/usr/bin/env bash
set -euxo pipefail
IFS=$'\n\t'

bin/console --no-interaction lint:yaml src
bin/console --no-interaction lint:container
bin/console --no-interaction lint:twig src
