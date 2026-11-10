#!/usr/bin/env bash
# Construit les données du thème sur la copie de test, puis le zip.
# Usage : outils/construire.sh [styles|blocs|tout] [filtre de bloc]
set -euo pipefail
ICI="$(cd "$(dirname "$0")" && pwd)"
DEPOT="$(dirname "$ICI")"
THEME="$DEPOT/theme/rolling"
WP="${ROLLING_WP:-/tmp/claude-0/-home-user-rolling/aa679d50-98b0-5524-9b01-90129a80fade/scratchpad/wp}"
ETAPE="${1:-tout}"
FILTRE="${2:-}"
cd "$WP"
dc() { timeout 600 docker compose "$@"; }

envoyer_theme() {
	dc exec -T -u 0 wp rm -rf /var/www/html/wp-content/themes/rolling
	dc cp "$THEME" wp:/var/www/html/wp-content/themes/rolling >/dev/null 2>&1
	dc exec -T -u 0 wp chown -R www-data: /var/www/html/wp-content/themes/rolling
}
installer() {
	# Écran d'administration simulé : comme chez la cliente (Elementor garde alors les réglages complets des balises).
	dc exec -T cli wp eval 'set_current_screen("dashboard"); wp_set_current_user(1); Rolling_Installateur::installer(); print_r(get_transient("rolling_rapport"));'
}
preparer() {
	dc exec -T -u 0 cli bash -c 'rm -rf /tmp/rolling; mkdir -p /tmp/rolling/data/blocs'
	dc cp "$ICI/source" cli:/tmp/rolling/ >/dev/null 2>&1
	dc cp "$ICI/construire-styles.php" cli:/tmp/rolling/ >/dev/null 2>&1
	dc cp "$ICI/construire-blocs.php" cli:/tmp/rolling/ >/dev/null 2>&1
	dc cp "$THEME/data/classes.json" cli:/tmp/rolling/data/classes.json >/dev/null 2>&1
	dc cp "$THEME/data/blocs/." cli:/tmp/rolling/data/blocs/ >/dev/null 2>&1
	dc exec -T -u 0 cli chown -R 33:33 /tmp/rolling
}

if [ "$ETAPE" = "styles" ] || [ "$ETAPE" = "tout" ]; then
	preparer
	dc exec -T cli wp eval-file /tmp/rolling/construire-styles.php /tmp/rolling/source /tmp/rolling/data
	dc cp cli:/tmp/rolling/data/variables.json "$THEME/data/variables.json" >/dev/null 2>&1
	dc cp cli:/tmp/rolling/data/classes.json "$THEME/data/classes.json" >/dev/null 2>&1
	envoyer_theme
	installer
fi

if [ "$ETAPE" = "blocs" ] || [ "$ETAPE" = "tout" ]; then
	python3 -I "$ICI/blocs.py"
	preparer
	dc exec -T cli wp eval-file /tmp/rolling/construire-blocs.php /tmp/rolling/source/blocs /tmp/rolling/data/blocs "$FILTRE"
	for f in $(dc exec -T cli bash -c 'ls /tmp/rolling/data/blocs'); do
		dc cp "cli:/tmp/rolling/data/blocs/$f" "$THEME/data/blocs/$f" >/dev/null 2>&1
	done
	# Une propriété = une seule source par élément (sinon le rendu change avec « case décochée »).
	python3 -I "$ICI/chevauchements.py"
	envoyer_theme
	installer
fi
