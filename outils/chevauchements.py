#!/usr/bin/env python3
"""Repère, dans les blocs du thème, les propriétés posées deux fois sur un même élément.

Pourquoi : avec « Conserver les styles du modèle », case décochée, Elementor recopie les classes dans le
style local de l'élément, APRÈS les réglages locaux. Une propriété posée à la fois par une classe et en local
(ou par deux classes) n'a alors plus le même gagnant que sur le site. Règle : une propriété = une seule source
par élément, taille d'écran et état.

Usage : python3 outils/chevauchements.py   (code de sortie 1 s'il reste des chevauchements)
"""
import base64
import glob
import json
import os
import re
import sys

RACINE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
DONNEES = os.path.join(RACINE, 'theme', 'rolling', 'data')

# Propriétés natives qui se recouvrent (raccourci CSS ↔ détails).
FAMILLES = {
    'background': 'background', 'background-color': 'background',
    'padding': 'padding', 'margin': 'margin', 'border-radius': 'border-radius',
    'border-width': 'border', 'border-color': 'border', 'border-style': 'border', 'border': 'border',
}


def famille(prop):
    return FAMILLES.get(prop, prop)


def props_css_brut(raw):
    """Propriétés de premier niveau d'un CSS personnalisé (hors règles imbriquées & …, @media …)."""
    css = base64.b64decode(raw).decode('utf-8') if raw else ''
    niveau, courant, props = 0, '', set()
    for ch in css:
        if ch == '{':
            niveau += 1
            courant = ''
        elif ch == '}':
            niveau -= 1
            courant = ''
        elif ch == ';' and niveau == 0:
            m = re.match(r'\s*([a-z-]+)\s*:', courant)
            if m:
                props.add(m.group(1))
            courant = ''
        elif niveau == 0:
            courant += ch
    m = re.match(r'\s*([a-z-]+)\s*:', courant)
    if m and niveau == 0:
        props.add(m.group(1))
    return props


def cles(variantes):
    """{(écran, état): {famille de propriété: valeur}}"""
    res = {}
    for v in variantes:
        meta = v.get('meta', {})
        cle = (meta.get('breakpoint') or 'desktop', meta.get('state') or '')
        s = res.setdefault(cle, {})
        for p, val in (v.get('props') or {}).items():
            s.setdefault(famille(p), []).append(json.dumps(val, sort_keys=True))
        cc = v.get('custom_css') or {}
        for p in props_css_brut(cc.get('raw')):
            s.setdefault(famille(p), []).append('css:' + p)
    return res


def main():
    classes = json.load(open(os.path.join(DONNEES, 'classes.json'), encoding='utf-8'))['classes']
    par_id = {c['id']: c for c in classes}
    rang = {c['id']: i for i, c in enumerate(classes)}  # ordre global : la première classe l'emporte
    total = 0
    for fichier in sorted(glob.glob(os.path.join(DONNEES, 'blocs', '*.json'))):
        bloc = json.load(open(fichier, encoding='utf-8'))
        contenu = bloc['contenu'] if isinstance(bloc['contenu'], list) else [bloc['contenu']]

        def parcourir(el):
            nonlocal total
            reglages = el.get('settings') if isinstance(el.get('settings'), dict) else {}
            ids = (reglages.get('classes') or {}).get('value', []) if isinstance(reglages.get('classes'), dict) else []
            sources = []  # (nom, carte, rang sur le site, rang une fois recopié)
            locaux = [i for i in ids if i not in par_id and i in (el.get('styles') or {})]
            for pos, i in enumerate(ids):
                if i in par_id:
                    # Site : les styles locaux passent après les classes ; entre classes, l'ordre global.
                    # Recopié : style local d'abord, puis les classes dans l'ordre de l'élément.
                    sources.append((par_id[i]['label'], cles(par_id[i]['variants']), -rang[i], 1 + pos))
            for i in locaux:
                sources.append(('local', cles(el['styles'][i]['variants']), 10 ** 6, 0))
            vues = {}
            for nom, carte, r_site, r_copie in sources:
                for cle, props in carte.items():
                    for p, val in props.items():
                        vues.setdefault((cle, p), []).append((nom, val, r_site, r_copie))
            for (cle, p), noms in sorted(vues.items()):
                if len(noms) < 2:
                    continue
                site = max(noms, key=lambda x: x[2])
                copie = max(noms, key=lambda x: x[3])
                if site[1] != copie[1]:
                    noms = ['%s%s' % (x[0], ' (site)' if x is site else ' (copie)' if x is copie else '') for x in noms]
                    total += 1
                    print('%-14s %-12s %-22s %-10s %s' % (
                        os.path.basename(fichier)[:14], el.get('widgetType') or el.get('elType'),
                        p, '/'.join(x for x in cle if x), ' + '.join(noms)))
            for enfant in el.get('elements', []):
                parcourir(enfant)

        for el in contenu:
            parcourir(el)
    print('%d chevauchement(s).' % total)
    return 1 if total else 0


if __name__ == '__main__':
    sys.exit(main())
