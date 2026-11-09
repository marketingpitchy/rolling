#!/usr/bin/env python3
"""Fabrique maquette-chiffres.html (images intégrées) à partir de template.html.

Usage : python3 -I maquettes/chiffres/build.py <dossier des images (theme/rolling/assets/medias)> <fichier de sortie>
Les chiffres en verre de la cliente ne sont pas dans git : ils sont lus dans le dossier donné.
"""
import base64
import os
import re
import sys

ICI = os.path.dirname(os.path.abspath(__file__))
images, sortie = sys.argv[1], sys.argv[2]


def img(nom):
    with open(os.path.join(images, nom + '.webp'), 'rb') as f:
        return 'data:image/webp;base64,' + base64.b64encode(f.read()).decode()


def nombre(chiffres, lu, unite=None):
    html = '<p class="nombre">' + ''.join('<img src="%s" alt="">' % img('chiffre-' + c) for c in chiffres)
    html += '<span class="masque">%s</span>' % lu
    if unite:
        html += '<span class="unite" aria-hidden="true">%s</span>' % unite
    return html + '</p>'


FIGURES = [
    (['1', '0'], '10 ans', 'ans', 'de vidéo pour les entreprises'),
    (['500-plus'], '500+', None, 'grandes entreprises équipées'),
    (['1'], '1', None, 'studio motion &amp; tournage intégré'),
    (['1'], '1', None, 'plateforme de production propriétaire'),
]


def figures(classe):
    corps = ''.join('<div>%s<p class="libelle">%s</p></div>' % (nombre(c, lu, u), lib) for c, lu, u, lib in FIGURES)
    return '<div class="%s">%s</div>' % (classe, corps)


def option(m):
    lettre, titre, texte = m.group(1), m.group(2), m.group(3)
    scene = '<div class="scene"><div class="halo" aria-hidden="true"></div>%s</div>' % figures(lettre.lower())
    return ('<section class="option" aria-label="%s"><h2>%s</h2><p>%s</p><div class="apercus">'
            '<div class="apercu ordi"><p class="nom">Ordinateur</p>%s</div>'
            '<div class="apercu mobile"><p class="nom">Mobile</p>%s</div></div></section>') % (titre, titre, texte, scene, scene)


modele = open(os.path.join(ICI, 'template.html'), encoding='utf-8').read()
html = re.sub(r'\{\{option:([A-Z])\|([^|]+)\|([^}]+)\}\}', option, modele)
with open(sortie, 'w', encoding='utf-8') as f:
    f.write(html)
print(sortie, len(html) // 1024, 'Ko')
