#!/bin/bash
# floe-sync, server side. Runs as root on the TrueNAS box that hosts the live
# site, called over ssh by bin/floe-sync, which copies this folder to
# /mnt/ssd_pool/apps/floe/sync/lib before every run. Use bin/floe-sync rather
# than calling this directly.
#
#   remote.sh paths                    where the live uploads and backups are, and the live theme commit
#   remote.sh fingerprint              content fingerprint of the live site (JSON)
#   remote.sh export                   the whole live database, gzipped, on stdout
#   remote.sh backup <label>           save the live database as backups/<label>-<time>.sql.gz
#   remote.sh import <file> <from-url> replace live content with sync/incoming/<file> (a push)
#   remote.sh restore <backup>         put a database backup from backups/ back on the live site
#   remote.sh backups                  list the backups
#
# The FLOE_* variables point it at a copy of the site for testing.
set -euo pipefail

APP=/mnt/ssd_pool/apps/floe
SYNC=${FLOE_SYNC:-$APP/sync}
BACKUPS=${FLOE_BACKUPS:-$APP/backups}
CONTAINER=${FLOE_WP_CONTAINER:-ix-floe-wordpress-1}
NETWORK=${FLOE_NETWORK:-ix-floe_default}
SECRETS=${FLOE_SECRETS:-$APP/secrets.env}
DB_NAME=${FLOE_DB_NAME:-}
HEALTH_URL=${FLOE_HEALTH_URL:-http://localhost:8090/}
DEPLOYED=${FLOE_DEPLOYED:-$APP/deploy/deployed-sha}
CLI_IMAGE=wordpress:cli-2.12

die() { echo "floe-sync (live): $*" >&2; exit 1; }
stamp() { date +%Y%m%d-%H%M%S; }

# wp-cli in a throwaway container that shares the live site's files and database.
wp() {
	docker run --rm -i --volumes-from "$CONTAINER" --network "$NETWORK" --env-file "$SECRETS" \
		${DB_NAME:+-e WORDPRESS_DB_NAME="$DB_NAME"} \
		-v "$SYNC:/floe-sync:ro" -v "$BACKUPS:/backups" \
		--user 33:33 "$CLI_IMAGE" wp "$@"
}

lock() {
	exec 8>"$SYNC/.lock"
	flock -n 8 || die "another sync is already running on the live site"
}

healthy() {
	local code
	code=$(curl -s -o /dev/null -w '%{http_code}' --max-time 20 "$HEALTH_URL" || true)
	[ "$code" = 200 ]
}

backup() {
	local file
	file="$BACKUPS/$1-$(stamp).sql.gz"
	wp db export - </dev/null | gzip > "$file.part"
	mv "$file.part" "$file"
	chown 33:33 "$file"
	echo "$file"
}

# Rewrites the other site's address to this one's, in plain and JSON-escaped
# form. search-replace keeps serialized data valid; guids are left alone.
replace_urls() {
	local from_host=$1 to=$2 scheme
	for scheme in http https; do
		wp search-replace "$scheme://$from_host" "$to" --skip-columns=guid --report-changed-only </dev/null
		wp search-replace "$scheme:\\/\\/$from_host" "${to//\//\\/}" --skip-columns=guid --report-changed-only </dev/null
	done
}

flush() {
	wp cache flush </dev/null
	wp transient delete --all </dev/null
	wp rewrite flush </dev/null
}

import_sql() { # import_sql <file.sql[.gz]>
	case $1 in
		*.gz) gunzip -c "$1" ;;
		*) cat "$1" ;;
	esac | wp db import -
}

cmd_import() {
	local file="$SYNC/incoming/$(basename "$1")" from_host to ts saved restore_point
	from_host=$(sed -E 's#^https?://##; s#/.*##' <<< "$2")
	[ -f "$file" ] || die "no such upload: $file"
	lock
	ts=$(stamp)
	to=$(wp option get home </dev/null)

	restore_point=$(backup pre-push)
	echo "Backed up the live database: $(basename "$restore_point")"
	saved="enquiries-pre-push-$ts.json"
	wp eval-file /floe-sync/lib/enquiries.php export </dev/null > "$BACKUPS/$saved"
	chown 33:33 "$BACKUPS/$saved"

	import_sql "$file"
	replace_urls "$from_host" "$to"
	wp eval-file /floe-sync/lib/enquiries.php purge </dev/null
	wp eval-file /floe-sync/lib/enquiries.php import "/backups/$saved" </dev/null
	flush

	if ! healthy; then
		echo "The live site isn't responding after the import. Restoring $(basename "$restore_point")…" >&2
		import_sql "$restore_point"
		flush
		healthy && die "push rolled back; the live site is back as it was" \
			|| die "push rolled back but the site still isn't healthy: check $HEALTH_URL"
	fi
	rm -f "$file"
	echo "Live site healthy after the import."
}

cmd_restore() {
	local file="$BACKUPS/$(basename "$1")" restore_point
	[ -f "$file" ] || die "no such backup: $file"
	lock
	restore_point=$(backup pre-restore)
	echo "Backed up the current live database first: $(basename "$restore_point")"
	import_sql "$file"
	flush
	healthy || die "the live site isn't responding after the restore. $(basename "$restore_point") has the database from just before it."
	echo "Restored $(basename "$file"); the live site is healthy."
}

cmd=${1:-}
shift || true
case $cmd in
	paths)
		root=$(docker inspect -f '{{range .Mounts}}{{if eq .Destination "/var/www/html"}}{{.Source}}{{end}}{{end}}' "$CONTAINER")
		[ -n "$root" ] || die "couldn't find the site files of $CONTAINER"
		echo "uploads=$root/wp-content/uploads"
		echo "backups=$BACKUPS"
		echo "theme=$(cat "$DEPLOYED" 2>/dev/null || true)"
		;;
	fingerprint) wp eval-file /floe-sync/lib/fingerprint.php </dev/null ;;
	export) wp db export - </dev/null | gzip ;;
	backup) backup "${1:?label}" ;;
	import) cmd_import "${1:?file}" "${2:?from url}" ;;
	restore) cmd_restore "${1:?backup}" ;;
	backups) ls -lht --time-style=+'%Y-%m-%d %H:%M' "$BACKUPS" | tail -n +2 ;;
	*) die "unknown command '$cmd'" ;;
esac
