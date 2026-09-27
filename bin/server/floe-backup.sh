#!/bin/bash
# Nightly backup of the live Floe database, with retention. Installed on the
# TrueNAS box as /mnt/ssd_pool/scripts/floe-backup.sh and run by TrueNAS cron
# at 02:00. This file in the repo is the source: after changing it, copy it
# there again (see documentation/live-site.md).
#
# Keeps nightly-*.sql.gz backups:
#   - every one from the last 7 days
#   - the newest of each ISO week for 3 months
#   - the newest of each month for 12 months
#   - the newest of each year, forever
# and deletes the backups floe-sync makes before a push or restore
# (pre-push-*, pre-restore-*, enquiries-*, uploads-replaced-*) after 90 days.
# Anything else in the folder (hand-made backups) is left alone.
#
# Usage: floe-backup.sh [--dry-run]    --dry-run only reports what it would delete
set -euo pipefail

APP=/mnt/ssd_pool/apps/floe
BACKUPS=$APP/backups
LOG=$BACKUPS/backup.log

log() { echo "$(date '+%F %T') $*" >> "$LOG"; }

if [ "${1:-}" != --dry-run ]; then
	file="$BACKUPS/nightly-$(date +%Y%m%d-%H%M%S).sql.gz"
	docker run --rm --volumes-from ix-floe-wordpress-1 --network ix-floe_default \
		--env-file "$APP/secrets.env" --user 33:33 wordpress:cli-2.12 wp db export - </dev/null \
		| gzip > "$file.part"
	mv "$file.part" "$file"
	chown 33:33 "$file"
	log "backed up $(basename "$file") ($(du -h --apparent-size "$file" | cut -f1))"
fi

BACKUPS=$BACKUPS DRY_RUN=${1:-} python3 - <<'PY' | while IFS= read -r line; do log "$line"; done
import os, re, shutil
from datetime import datetime, timedelta
from pathlib import Path

folder = Path(os.environ["BACKUPS"])
dry = os.environ["DRY_RUN"] == "--dry-run"
now = datetime.now()

def months_ago(n):
    y, m = divmod(now.year * 12 + now.month - 1 - n, 12)
    return now.replace(year=y, month=m + 1, day=min(now.day, 28))

def remove(path):
    print(f"{'would delete' if dry else 'deleted'} {path.name}")
    if not dry:
        shutil.rmtree(path) if path.is_dir() else path.unlink()

nightly = []
for path in folder.iterdir():
    m = re.match(r"(nightly|pre-push|pre-restore|enquiries-pre-push|uploads-replaced)-(\d{8}-\d{6})", path.name)
    if not m or path.name.endswith(".part"):
        continue
    when = datetime.strptime(m.group(2), "%Y%m%d-%H%M%S")
    if m.group(1) == "nightly":
        nightly.append((when, path))
    elif when < now - timedelta(days=90):
        remove(path)

keep, newest = set(), {}
for when, path in nightly:
    if when >= now - timedelta(days=7):
        keep.add(path)
    buckets = [("year", when.year)]
    if when >= months_ago(3):
        buckets.append(("week", when.isocalendar()[:2]))
    if when >= months_ago(12):
        buckets.append(("month", (when.year, when.month)))
    for bucket in buckets:
        if bucket not in newest or when > newest[bucket][0]:
            newest[bucket] = (when, path)
keep.update(path for _, path in newest.values())
for when, path in sorted(nightly):
    if path not in keep:
        remove(path)
PY
