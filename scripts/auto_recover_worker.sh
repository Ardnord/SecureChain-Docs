#!/usr/bin/env bash
# Bash worker to run CodeIgniter CLI auto:recover every 5 minutes
# Usage: run under systemd or screen/tmux, or as a cron @reboot job

PROJECT_DIR="/var/www/blockchain"   # <-- update to your project path
PHP="php"                            # <-- update to full php path if needed
SLEEP_SECONDS=300

echo "Starting AutoRecover worker for project: ${PROJECT_DIR}"
while true; do
  echo "$(date '+%Y-%m-%d %H:%M:%S') - Running auto:recover"
  cd "${PROJECT_DIR}" || exit 1
  ${PHP} spark auto:recover >> "${PROJECT_DIR}/writable/logs/auto_recover.log" 2>&1 || echo "auto:recover failed"
  sleep ${SLEEP_SECONDS}
done
