#!/bin/bash

# USAGE: ./new.sh <day>

DAY=$1

# Define some SOFT colors
BLUE='\033[0;34m'
GREEN='\033[0;32m'
RED='\033[1;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Fail early
set -e

# Validate day input
if [ -z "$DAY" ]; then
    echo -e "${RED}Error: Day number is required${NC}"
    echo "USAGE: ./new.sh <day>"
    exit 1
fi

echo -e "${GREEN}Creating new day: ${RED}Day ${DAY}${NC}"

# Copy the template file to the src/Solutions folder if it doesn't already exist
if [ ! -f src/Solutions/Day${DAY}.php ]; then
    cp templates/Day_Template.php src/Solutions/Day${DAY}.php

    # 1. Replace the class name
    # 4. Update the link to the problem description
    sed -i "s/class Day_Template/class Day${DAY}/g" src/Solutions/Day${DAY}.php
    sed -i "s/Problem description: https:\/\/adventofcode.com\/2025\/day\//Problem description: https:\/\/adventofcode.com\/2025\/day\/${DAY}/g" src/Solutions/Day${DAY}.php

    # Remove trailing empty lines
    sed -i -e :a -e '/^\s*$/d;N;ba' src/Solutions/Day${DAY}.php

    echo -e "Created new file: ${BLUE}src/Solutions/Day${DAY}.php${NC}"
fi

# Create 2 new data files for the day if they don't already exist
if [ ! -f data/day_${DAY} ]; then
    touch data/day_${DAY}
    touch data/day_${DAY}.ex

    echo -e "Created new data files: ${BLUE}data/day_${DAY}${NC} and ${BLUE}data/day_${DAY}.ex${NC}"
fi

# Grab the puzzle input from Advent of Code
if [ ! -s data/day_${DAY} ]; then
    echo -e "${YELLOW}Fetching puzzle input for Day ${DAY}...${NC}"

    # Load session key from .env
    if [ -f .env ]; then
        source .env
    else
        echo -e "${RED}Error: .env file not found${NC}"
        exit 1
    fi

    if [ -z "$AOC_SESSION_KEY" ]; then
        echo -e "${RED}Error: AOC_SESSION_KEY not set in .env${NC}"
        exit 1
    fi

    # Fetch the input
    curl -s -b "session=${AOC_SESSION_KEY}" \
        "https://adventofcode.com/2025/day/${DAY}/input" \
        -o data/day_${DAY}

    if [ -s data/day_${DAY} ]; then
        # Strip trailing newlines
        sed -i -e ':a' -e '$!{N;ba' -e '}; s/[[:space:]]*$//' data/day_${DAY}
        echo -e "${GREEN}Successfully fetched input for Day ${DAY}${NC}"
    else
        echo -e "${RED}Failed to fetch input for Day ${DAY}${NC}"
        exit 1
    fi
fi

echo -e "${GREEN}Done creating new day: ${RED}Day ${DAY}${NC}"