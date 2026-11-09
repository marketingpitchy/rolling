#!/usr/bin/env bash
# Fabrique le zip de livraison du thème (avec les images de départ, qui ne sont pas dans git).
set -euo pipefail
DEPOT="$(cd "$(dirname "$0")/.." && pwd)"
VERSION="$(grep -m1 '^Version:' "$DEPOT/theme/rolling/style.css" | awk '{print $2}')"
SORTIE="$DEPOT/livraisons"
mkdir -p "$SORTIE"
test -d "$DEPOT/theme/rolling/assets/medias" || { echo "Images de départ absentes (theme/rolling/assets/medias)"; exit 1; }
cd "$DEPOT/theme"
ZIP="$SORTIE/rolling-$VERSION.zip"
rm -f "$ZIP"
zip -qr "$ZIP" rolling -x 'rolling/**/.DS_Store' 'rolling/**/*.map'
echo "$ZIP ($(du -h "$ZIP" | cut -f1))"
