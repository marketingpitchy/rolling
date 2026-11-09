#!/usr/bin/env python3
"""Convertit les captures PNG de la maquette (liste outils/captures/*.json) en WebP avec transparence.

Usage : python3 -I outils/vers-webp.py outils/captures/accueil.json theme/rolling/assets/medias
Le PNG est supprimé ; data/medias.json doit pointer vers le fichier .webp.
"""
import json
import os
import sys

from PIL import Image

liste, dossier = sys.argv[1], sys.argv[2]
for capture in json.load(open(liste, encoding='utf-8')):
    png = os.path.join(dossier, capture['fichier'])
    if not os.path.exists(png):
        continue
    webp = png[:-4] + '.webp'
    Image.open(png).save(webp, 'WEBP', quality=86, method=6)
    os.remove(png)
    print(os.path.basename(webp), os.path.getsize(webp) // 1024, 'Ko')
