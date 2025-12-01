#!/bin/bash

# USAGE: ./run.sh <day> [example]
# Example: ./run.sh 1         # Run Day 1 (both parts, real input)
# Example: ./run.sh 1 1       # Run Day 1 with benchmarking (real input)
# Example: ./run.sh 1 0 1     # Run Day 1 (example input)
# Example: ./run.sh 1 1 1     # Run Day 1 with benchmarking (example input)

DAY=$1
BENCH=${2:-0}
EXAMPLE=${3:-0}

# Define colors
RED='\033[1;31m'
NC='\033[0m' # No Color

# Fail early
set -e

# Validate day input
if [ -z "$DAY" ]; then
    echo -e "${RED}Error: Day number is required${NC}"
    echo "USAGE: ./run.sh <day> [example]"
    exit 1
fi

# Use PHP with opcache enabled for maximum performance
php -d opcache.enable=1 \
    -d opcache.enable_cli=1 \
    -d opcache.optimization_level=0x7FFFFFFF \
    -d memory_limit=512M \
    -r "
    require 'vendor/autoload.php';
    \$day = $DAY;
    \$bench = $BENCH;
    \$example = $EXAMPLE;
    \$className = 'frhel\\\\adventofcode2025php\\\\Solutions\\\\Day' . \$day;
    if (!class_exists(\$className)) {
        echo \"${RED}Error: Solution for Day \$day not found${NC}\n\";
        exit(1);
    }
    new \$className(\$day, \$bench, \$example);
    "
