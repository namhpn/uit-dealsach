#!/usr/bin/env bash
set -euo pipefail

docker exec dealsach-app php spark migrate:refresh
docker exec dealsach-app php spark db:seed DemoSeeder
docker exec dealsach-app php spark dealsach:crawl all
docker exec dealsach-app php spark dealsach:alerts
