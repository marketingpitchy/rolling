#!/usr/bin/env python3
"""Fond les bords des illustrations capturées (ombres coupées net au bord de l'image).

Pourquoi : la capture d'une carte garde son ombre portée, mais l'image s'arrête avant que l'ombre soit transparente ;
sur le site, on voyait une ligne droite là où l'ombre est coupée. L'outil multiplie la transparence par une rampe douce
près de chaque bord encore visible.

Usage : python3 -I outils/adoucir-bords.py fichier.webp [...]   (modifie les fichiers en place, même réglage que vers-webp.py)
"""
import sys

from PIL import Image


def adoucir(chemin):
    im = Image.open(chemin).convert('RGBA')
    w, h = im.size
    a = im.getchannel('A')
    px = a.load()
    n = max(8, min(56, int(min(w, h) * 0.09)))
    bords = {
        'haut': max(px[x, 0] for x in range(w)),
        'bas': max(px[x, h - 1] for x in range(w)),
        'gauche': max(px[0, y] for y in range(h)),
        'droite': max(px[w - 1, y] for y in range(h)),
    }
    if max(bords.values()) <= 3:
        return False

    def rampe(d):
        t = min(1.0, d / n)
        return t * t * (3 - 2 * t)

    for y in range(h):
        for x in range(w):
            v = px[x, y]
            if not v:
                continue
            f = 1.0
            if bords['haut'] > 3:
                f *= rampe(y)
            if bords['bas'] > 3:
                f *= rampe(h - 1 - y)
            if bords['gauche'] > 3:
                f *= rampe(x)
            if bords['droite'] > 3:
                f *= rampe(w - 1 - x)
            if f < 1.0:
                px[x, y] = int(round(v * f))
    im.putalpha(a)
    im.save(chemin, 'WEBP', quality=86, method=6)
    return True


if __name__ == '__main__':
    for f in sys.argv[1:]:
        print(f, 'adouci' if adoucir(f) else 'déjà propre')
