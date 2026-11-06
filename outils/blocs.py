"""Source des blocs Elementor du thème Rolling.

Chaque bloc est un arbre d'éléments atomiques. Ce script écrit outils/source/blocs/<clé>.json
au format attendu par l'outil officiel « elementor/build-composition » (XML + config + classes + style).
Les images de départ sont désignées par https://media.rolling.invalid/<clé du média>.<ext> :
le thème les remplace à l'installation par les images copiées dans la médiathèque.

Usage : python3 -I outils/blocs.py
"""
import json
import os

ICI = os.path.dirname(os.path.abspath(__file__))
MEDIA = 'https://media.rolling.invalid/'


class E:
    """Un élément : type atomique, nom (visible dans le Navigateur d'Elementor), réglages, classes, style local."""

    def __init__(self, type_, nom, cfg=None, cls=None, style=None, enfants=None):
        self.type, self.nom, self.cfg, self.cls, self.style = type_, nom, cfg or {}, cls or [], style or ''
        self.enfants = enfants or []


def F(nom, cls=None, style=None, enfants=None, tag=None, lien=None):
    cfg = {}
    if tag:
        cfg['tag'] = tag
    if lien:
        cfg['link'] = {'destination': lien}
    return E('e-flexbox', nom, cfg, cls, style, enfants)


def D(nom, cls=None, style=None, enfants=None, tag=None):
    return E('e-div-block', nom, {'tag': tag} if tag else {}, cls, style, enfants)


def H(nom, texte, tag='h2', cls=None, style=None):
    return E('e-heading', nom, {'tag': tag, 'title': texte}, cls, style)


def P(nom, texte, cls=None, style=None):
    return E('e-paragraph', nom, {'paragraph': texte}, cls, style)


def B(nom, texte, lien, cls=None, style=None):
    return E('e-button', nom, {'text': texte, 'link': {'destination': lien}}, cls, style)


def I(nom, media, alt='', cls=None, style=None, taille='full'):
    src = {'url': MEDIA + media}
    if alt:
        src['alt'] = alt
    return E('e-image', nom, {'image': {'src': src, 'size': taille}}, cls, style)


def separer_imbrique(css):
    """Sépare les règles imbriquées « & … { … } » (hors @media) : l'outil de composition les ignore
    dans les styles locaux, le script de construction les pose ensuite dans le CSS personnalisé."""
    garde, brut, i, n = [], [], 0, len(css)
    while i < n:
        if css.startswith('&', i) or css.startswith('@media', i):
            debut = i
            j = css.index('{', i)
            profondeur, k = 1, j + 1
            while profondeur:
                profondeur += {'{': 1, '}': -1}.get(css[k], 0)
                k += 1
            (brut if css.startswith('&', debut) else garde).append(css[debut:k])
            i = k
        else:
            garde.append(css[i])
            i += 1
    return ''.join(garde).strip(), ' '.join(brut).strip()


def composer(racine):
    """Transforme l'arbre en entrée pour build-composition."""
    vus, config, classes, style, brut = set(), {}, {}, {}, {}

    def xml(e):
        if e.nom in vus:
            raise ValueError('Nom en double : ' + e.nom)
        vus.add(e.nom)
        if e.cfg:
            config[e.nom] = e.cfg
        if e.cls:
            classes[e.nom] = e.cls
        if e.style:
            natif, imbrique = separer_imbrique(e.style)
            if natif:
                style[e.nom] = natif
            if imbrique:
                brut[e.nom] = imbrique
        nom = e.nom.replace('&', '&amp;').replace('"', '&quot;')
        if not e.enfants:
            return '<%s configuration-id="%s"></%s>' % (e.type, nom, e.type)
        return '<%s configuration-id="%s">%s</%s>' % (e.type, nom, ''.join(xml(x) for x in e.enfants), e.type)

    x = xml(racine)
    return {'xml_structure': x, 'element_config': config, 'classes': classes, 'style': style}, brut


# --------------------------------------------------------------------------------------------
# Morceaux réutilisés
# --------------------------------------------------------------------------------------------

def halo(nom, couleur, position):
    return D(nom, ['halo-' + couleur], position)


def bandeau(nom, texte, rec=False, style=None):
    enfants = [D(nom + ' · point', ['point-rec'])] if rec else [D(nom + ' · point', None, 'width: 6px; height: 6px; min-width: 6px; padding: 0; border-radius: 50%; background: #FFD1BF; box-shadow: 0 0 0 3px rgba(255,209,191,0.3);')]
    return F(nom, ['bandeau-video'], style, enfants + [P(nom + ' · texte', texte)])


def lecture(nom, style=None, verre=False):
    return D(nom, ['bouton-lecture', 'bouton-lecture-verre'] if verre else ['bouton-lecture'], style)


# --------------------------------------------------------------------------------------------
# Hero
# --------------------------------------------------------------------------------------------



hero = F('Hero', ['section'], 'padding: 104px 0 70px; @media(--tablet) { padding: 116px 0 56px; } @media(--mobile) { padding: 124px 0 48px; }', tag='section', enfants=[
    halo('Halo lilas', 'lilas', 'left: -300px; top: -380px;'),
    halo('Halo pêche', 'peche', 'right: -380px; top: -200px; opacity: 0.7;'),
    F('Hero · contenu', ['conteneur'], 'display: flex; flex-direction: row; flex-wrap: wrap; align-items: center; gap: 48px; z-index: 2;', enfants=[
        F('Promesse', ['pile'], 'flex: 1 1 560px; min-width: 0; gap: 0; padding-top: 40px; @media(--tablet) { padding-top: 0; }', enfants=[
            F('Pastille', ['pastille'], enfants=[D('Pastille · point', ['point']), P('Pastille · texte', 'Votre agence vidéo illimitée')]),
            H('Titre H1', "Le meilleur <u>de l'humain.</u><br>Le meilleur <b>de l'IA.</b>", 'h1', ['titre-lg'], 'margin-top: 26px;'),
            P('Chapô', "Vos clients, vos candidats, vos collaborateurs s'informent tous en vidéo. Mais vos équipes n'ont pas le temps d'en faire ? La nôtre s'en charge, à prix fixe, <u>en illimité.</u>", ['chapo'], 'margin-top: 26px; max-width: 620px;'),
            F('Boutons', ['rangee'], 'margin-top: 40px;', enfants=[
                B('Bouton principal', 'Réserver un échange', '/contact/', ['bouton-principal']),
                B('Bouton secondaire', 'Voir les offres', '/offres/', ['bouton-verre']),
            ]),
            F('Preuve', ['rangee'], 'margin-top: 24px;', enfants=[
                F('Visages', ['visages'], enfants=[
                    I('Visage 1', 'visage-1.jpg', taille='thumbnail'), I('Visage 2', 'visage-2.jpg', taille='thumbnail'),
                    I('Visage 3', 'visage-3.jpg', taille='thumbnail'), I('Visage 4', 'visage-4.jpg', taille='thumbnail'),
                ]),
                P('Preuve · texte', 'Par les équipes de <strong>Pitchy</strong> — 10 ans de vidéo, 500+ grandes entreprises.', ['texte-petit'], 'font-size: 14px; color: var(--legende);'),
            ]),
        ]),
        D('Visuels', None, 'position: relative; flex: 1 1 460px; min-width: 0; height: 680px; padding: 0; @media(--tablet) { height: auto; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; } @media(--mobile) { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }', enfants=[
            D('Visuels · lueur', None, 'position: absolute; left: -40px; right: -40px; top: -20px; bottom: -40px; padding: 0; background: radial-gradient(closest-side, rgba(255,162,122,0.6), rgba(255,209,191,0.3) 55%, rgba(250,249,247,0) 100%); @media(--tablet) { display: none; }'),
            D('Tournage', ['cadre-verre'], 'position: absolute; left: 0; right: 0; top: 0; border-radius: 42px; @media(--tablet) { position: relative; grid-column: 1 / -1; }', enfants=[
                D('Tournage · photo', ['cadre-photo'], 'height: 652px; border-radius: 29px; @media(--tablet) { height: 420px; } @media(--mobile) { height: 280px; }', enfants=[
                    I('Tournage · image', 'hero-tournage.jpg', 'Une caméra sur trépied, en plein tournage dans des bureaux', ['image-couvrante'], 'object-position: 56% 50%;'),
                    bandeau('Tournage · bandeau', 'Tournage', rec=True, style='top: 18px; left: 18px;'),
                ]),
            ]),
            D('Interview', ['cadre-verre'], 'position: absolute; left: 28px; top: 252px; width: 208px; z-index: 2; border-radius: 34px; @media(--tablet) { position: relative; left: auto; top: auto; width: auto; }', enfants=[
                D('Interview · vidéo', ['cadre-photo'], 'aspect-ratio: 9 / 16; border-radius: 21px; background: #3F2BB8;', enfants=[
                    I('Interview · image', 'hero-interview.jpg', 'Une experte filmée en interview', ['image-couvrante'], 'object-position: 53% 28%;'),
                    bandeau('Interview · bandeau', 'Interview', style='top: 10px; left: 10px;'),
                    F('Interview · plaque', ['plaque-verre'], 'position: absolute; z-index: 2; left: 8px; right: 8px; bottom: 8px; width: auto; justify-content: space-between; padding: 10px 8px 10px 12px;', enfants=[
                        P('Interview · titre', '3 gestes<br><span>qui sauvent</span>', ['titre-vignette']),
                        lecture('Interview · lecture', verre=True),
                    ]),
                ]),
            ]),
            D('Démo produit', ['cadre-verre'], 'position: absolute; right: 28px; top: 24px; width: 294px; z-index: 2; @media(--tablet) { position: relative; right: auto; top: auto; width: auto; align-self: start; } @media(--mobile) { grid-column: 1 / -1; order: 3; }', enfants=[
                D('Démo produit · vidéo', ['cadre-photo'], 'aspect-ratio: 16 / 9; border-radius: 19px;', enfants=[
                    I('Démo produit · image', 'hero-demo-produit.jpg', "Un pot de crème ouvert flotte au-dessus d'une pierre", ['image-couvrante'], 'object-position: 50% 40%;'),
                    bandeau('Démo produit · bandeau', 'Démo produit', style='top: 10px; left: 10px;'),
                    F('Démo produit · lecteur', ['plaque-verre'], 'position: absolute; z-index: 2; left: 8px; right: 8px; bottom: 8px; width: auto; gap: 12px; padding: 6px 14px 6px 6px; border-radius: 999px;', enfants=[
                        lecture('Démo produit · lecture', 'width: 30px; height: 30px;', verre=True),
                        D('Démo produit · piste', ['piste-lecture'], enfants=[
                            D('Démo produit · progression'),
                        ]),
                    ]),
                ]),
            ]),
            D('Motion design', ['cadre-verre'], 'position: absolute; right: 28px; top: 432px; width: 208px; z-index: 2; @media(--tablet) { position: relative; right: auto; top: auto; width: auto; align-self: start; }', enfants=[
                D('Motion design · vidéo', ['fond-video-orange'], 'position: relative; aspect-ratio: 1 / 1; overflow: hidden; padding: 0; border-radius: 19px;', enfants=[
                    D('Motion design · bulle', None, 'position: absolute; left: 18px; top: 54px; width: 62px; height: 62px; padding: 0; border-radius: 50%; background: radial-gradient(circle at 32% 28%, #FFFFFF 0%, rgba(255,255,255,0.75) 40%, rgba(255,255,255,0.25) 100%); box-shadow: 0 10px 22px -10px rgba(160,50,10,0.6);'),
                    F('Motion design · barres', ['barres-graphique'], 'position: absolute; right: 16px; top: 46px;', enfants=[
                        D('Barre 1'), D('Barre 2'), D('Barre 3'), D('Barre 4'),
                    ]),
                    F('Motion design · bandeau', ['bandeau-video'], 'top: 10px; left: 10px; color: #0F0F12; background: linear-gradient(165deg, rgba(255,255,255,0.72) 0%, rgba(255,255,255,0.32) 100%);', enfants=[
                        D('Motion design · point', None, 'width: 6px; height: 6px; min-width: 6px; padding: 0; border-radius: 50%; background: #E0562A; box-shadow: 0 0 0 3px rgba(224,86,42,0.22);'),
                        P('Motion design · texte', 'Motion design'),
                    ]),
                    F('Motion design · plaque', ['plaque-verre'], 'position: absolute; z-index: 2; left: 8px; right: 8px; bottom: 8px; width: auto; justify-content: space-between; padding: 10px 8px 10px 12px; color: #0F0F12;', enfants=[
                        P('Motion design · titre', 'Résultats<br><span>2026</span>', ['titre-vignette', 'titre-vignette-sombre'], 'font-size: 17px; line-height: 0.98; letter-spacing: -0.04em;'),
                        lecture('Motion design · lecture', verre=True),
                    ]),
                ]),
            ]),
        ]),
    ]),
])

# --------------------------------------------------------------------------------------------
# Ils nous font confiance (logos qui défilent)
# --------------------------------------------------------------------------------------------

LOGOS = [
    ('logo-groupement-mousquetaires.png', 'Groupement Mousquetaires', 46),
    ('logo-club-med.png', 'Club Med', 26),
    ('logo-sncf.png', 'SNCF', 40),
    ('logo-loccitane.png', 'L’Occitane en Provence', 30),
    ('logo-air-france.png', 'Air France', 14),
    ('logo-bnp-paribas.png', 'BNP Paribas', 30),
    ('logo-fnac-darty.png', 'Fnac Darty', 30),
    ('logo-groupama.png', 'Groupama', 28),
    ('logo-veolia.png', 'Veolia', 32),
]

logos = F('Ils nous font confiance', ['section'], 'padding: 0 0 104px; @media(--tablet) { padding: 0 0 72px; }', tag='section', enfants=[
    F('Logos · titre', ['conteneur'], 'justify-content: center;', enfants=[
        P('Logos · libellé', 'Ils nous font confiance', ['label'], 'text-align: center; width: 100%;'),
    ]),
    F('Logos · bande', ['masque-bords'], 'margin-top: 22px; width: 100%;', enfants=[
        F('Logos · liste', ['logos-defilants', 'grille-6'], enfants=[
            F('Logo · ' + alt, ['carte-verre', 'tuile-logo'], 'padding: 0 14px; gap: 0;', enfants=[
                I('Logo · image · ' + alt, fichier, cls=None, style='height: %dpx;' % h, taille='medium'),
            ]) for fichier, alt, h in LOGOS
        ]),
    ]),
    F('Logos · commande', ['conteneur'], 'justify-content: center; margin-top: 4px;', enfants=[
        B('Bouton pause', '<span>Arrêter le défilement</span><span>Relancer le défilement</span>', '#rl-pause', ['bouton-verre', 'bouton-petit', 'bascule-libelles'], 'min-height: 44px; font-size: 14px;'),
    ]),
])

# --------------------------------------------------------------------------------------------
# Showreel
# --------------------------------------------------------------------------------------------

showreel = F('Showreel', ['section'], 'padding: 0 0 120px; @media(--tablet) { padding: 0 0 84px; }', tag='section', enfants=[
    F('Showreel · contenu', ['conteneur', 'pile'], 'gap: 0;', enfants=[
        F('Annotation', ['rangee'], 'justify-content: flex-end; align-items: flex-start; gap: 6px; margin-bottom: 6px; padding-right: 30px; @media(--tablet) { display: none; }', enfants=[
            P('Annotation · texte', 'ce que nous faisons, en vrai', ['note-main']),
            I('Annotation · flèche', 'fleche-annotation.png', style='width: 34px; margin-top: 14px;'),
        ]),
        D('Showreel · cadre', ['carte-verre'], 'padding: 12px; border-radius: 40px; @media(--mobile) { padding: 8px; border-radius: 28px; }', enfants=[
            D('Showreel · écran', None, 'position: relative; overflow: hidden; padding: 0; border-radius: 30px; background: #2A2233; @media(--mobile) { border-radius: 22px; }', enfants=[
                E('e-self-hosted-video', 'Showreel · vidéo', {'autoplay': True, 'mute': True, 'loop': True, 'playsinline': True, 'controls': True, 'preload': 'none', 'poster_enabled': True, 'poster': {'src': {'url': MEDIA + 'showreel-affiche.jpg'}, 'size': 'full'}}, None, 'display: block; width: 100%; aspect-ratio: 16 / 9;'),
                B('Bouton son', 'Activer le son', '#rl-son', ['bouton-son']),
            ]),
        ]),
        F('Showreel · boutons', ['rangee'], 'justify-content: center; margin-top: 28px;', enfants=[
            B('Showreel · bouton principal', 'Réserver un échange', '/contact/', ['bouton-principal']),
            B('Showreel · bouton secondaire', 'Voir les offres', '/offres/', ['bouton-verre']),
        ]),
    ]),
])

BLOCS = [
    ('01-hero', 'Rolling · 01 Hero', hero),
    ('02-logos', 'Rolling · 02 Ils nous font confiance', logos),
    ('03-showreel', 'Rolling · 03 Showreel', showreel),
]

if __name__ == '__main__':
    dossier = os.path.join(ICI, 'source', 'blocs')
    os.makedirs(dossier, exist_ok=True)
    for cle, titre, racine in BLOCS:
        composition, brut = composer(racine)
        donnees = {'cle': cle, 'titre': titre, 'type': 'e-flexbox', 'composition': composition, 'brut': brut}
        with open(os.path.join(dossier, cle + '.json'), 'w', encoding='utf8') as f:
            json.dump(donnees, f, ensure_ascii=False, indent=1)
        print(cle, len(donnees['composition']['xml_structure']), 'caractères de XML')
