#!/usr/bin/env python3
"""Métas SEO proposées pour chaque page (à coller dans le panneau Yoast de la page) et contrôle des longueurs.

Règles de la cliente (Règles SEO, 2.2 à 2.4) : title de 50 à 60 caractères, mot-clé au début, marque à la fin ;
description de 140 à 160 caractères avec le mot-clé et un bénéfice concret ; title différent du H1.

Usage : python3 -I outils/metas.py [sortie.html]
"""
import html
import sys

# (page, adresse, H1, title, description)
METAS = [
    ('Accueil', '/', 'Votre agence vidéo illimitée',
     'Agence vidéo illimitée, à prix fixe et sans devis | Rolling',
     "Rolling, l'agence vidéo des équipes com, RH et formation : motion design, tournages, interviews. À prix fixe et en illimité. Réservez un échange."),
    ('Offres', '/offres/', "Nos formules d'agence vidéo, à prix fixe.",
     'Tarifs agence vidéo : 2 formules à prix fixe | Rolling',
     "Création illimitée ou Création + Tournage : deux abonnements vidéo à prix fixe, sans devis ni surprise. Comparez les formules et réservez 30 minutes d'échange."),
    ('Réalisations', '/realisations/', 'Nos réalisations, du contenu à la vidéo.',
     "Exemples de vidéos d'entreprise : nos réalisations | Rolling",
     "Motion design, tournages, interviews, démos produit : découvrez les vidéos produites par notre agence vidéo pour les équipes com, RH et formation."),
    ('Témoignages', '/temoignages/', 'Ce que nos clients disent de notre accompagnement.',
     'Avis clients sur notre agence vidéo : témoignages | Rolling',
     "Comment les équipes com, RH et formation de grandes entreprises travaillent avec notre agence vidéo : leurs besoins, leurs vidéos, leurs résultats."),
    ('Blog', '/blog/', 'Des conseils vidéo pour vos équipes.',
     "Blog vidéo d'entreprise : conseils et méthodes | Rolling",
     "Communication interne, RH, formation, marketing : nos conseils concrets pour produire des vidéos que vos équipes regardent vraiment, par l'agence vidéo Rolling."),
    ('Guides', '/guides/', 'Nos guides vidéo, à télécharger.',
     "Guides vidéo d'entreprise gratuits à télécharger | Rolling",
     "Onboarding, formation, communication interne : téléchargez nos guides gratuits pour lancer et réussir vos vidéos d'entreprise, étape par étape."),
    ('Webinars', '/webinars/', 'Nos webinars, en direct avec nos équipes.',
     "Webinars sur la vidéo d'entreprise : inscription | Rolling",
     "Nos webinars gratuits sur la vidéo en entreprise : méthodes, exemples et questions en direct avec nos équipes. Choisissez une date et réservez votre place."),
    ('Replays', '/replays/', 'Nos webinars, à revoir quand vous voulez.',
     "Replays de webinars sur la vidéo d'entreprise | Rolling",
     "Vous avez manqué un webinar ? Revoyez gratuitement nos sessions sur la vidéo en entreprise : communication interne, RH, formation et marketing."),
    ('Contact', '/contact/', 'Parlons de vos vidéos.',
     'Contacter notre agence vidéo : réserver un échange | Rolling',
     "30 minutes en visio avec notre agence vidéo pour passer en revue vos contenus. Vous repartez avec vos 3 premières vidéos à produire et le prix de votre formule."),
    ("L'agence", '/agence/', "Rolling, l'agence vidéo née chez Pitchy.",
     'Agence vidéo à Paris, par les fondateurs de Pitchy | Rolling',
     "Rolling, c'est l'équipe de Pitchy : 13 ans de vidéo et 500+ grandes entreprises. Créatifs, experts IA et tournages partout en France, depuis Paris."),
    ('Landing (Google Ads, en noindex)', '/agence-video/', "L'agence vidéo qui transforme vos contenus en vidéos, en illimité.",
     'Agence vidéo en abonnement : vidéos illimitées | Rolling',
     "Rolling, agence de production vidéo à Paris : vos emails, articles et modules deviennent des vidéos, à prix fixe et en illimité. Réservez 30 minutes."),
    ('Mentions légales', '/mentions-legales/', 'Mentions légales',
     'Mentions légales du site agence-rolling.fr | Rolling',
     "Éditeur, hébergeur et informations légales du site agence-rolling.fr, celui de Rolling, l'agence vidéo en abonnement des équipes com, RH et formation."),
    ('Politique de confidentialité', '/politique-de-confidentialite/', 'Politique de confidentialité',
     'Politique de confidentialité et vos données | Rolling',
     "Les données que nous collectons sur agence-rolling.fr, pourquoi, combien de temps nous les gardons, et comment exercer vos droits (RGPD) auprès de nous."),
    ('CGV', '/cgv/', 'Conditions générales de vente',
     "Conditions générales de vente des abonnements | Rolling",
     "Les conditions générales de vente des abonnements Rolling : formules, durée, paiement, livraison des vidéos, droits d'utilisation et résiliation."),
]


def verifier():
    erreurs = []
    titres = set()
    descriptions = set()
    for page, _, h1, titre, desc in METAS:
        if not 50 <= len(titre) <= 60:
            erreurs.append('%s : title de %d caractères' % (page, len(titre)))
        if not 140 <= len(desc) <= 160:
            erreurs.append('%s : description de %d caractères' % (page, len(desc)))
        if not titre.endswith('| Rolling'):
            erreurs.append('%s : la marque doit finir le title' % page)
        if titre.split(' | ')[0].strip().lower() == h1.strip(' .').lower():
            erreurs.append('%s : title identique au H1' % page)
        if titre in titres or desc in descriptions:
            erreurs.append('%s : title ou description en double' % page)
        titres.add(titre)
        descriptions.add(desc)
    return erreurs


def page_html():
    lignes = []
    for page, adresse, h1, titre, desc in METAS:
        lignes.append(
            '<tr><th scope="row">%s<br><small>%s</small></th><td><p class="copie">%s</p><small>%d caractères</small></td>'
            '<td><p class="copie">%s</p><small>%d caractères</small></td><td><small>%s</small></td></tr>'
            % (html.escape(page), html.escape(adresse), html.escape(titre), len(titre), html.escape(desc), len(desc), html.escape(h1))
        )
    return """<!doctype html>
<html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Métas SEO des pages</title>
<style>
body { font-family: system-ui, sans-serif; color: #0F0F12; background: #FAF9F7; margin: 0; padding: 32px 16px; line-height: 1.5; }
main { max-width: 1200px; margin: 0 auto; }
h1 { font-size: 30px; margin: 0 0 8px; } p.intro { color: #4A4858; max-width: 820px; }
table { width: 100%; border-collapse: collapse; margin-top: 24px; background: #fff; border-radius: 16px; overflow: hidden; }
th, td { text-align: left; vertical-align: top; padding: 14px; border-bottom: 1px solid #ECE8E2; }
thead th { background: #5B3BEA; color: #fff; font-size: 14px; }
tbody th { width: 16%; font-size: 15px; } td small, th small { color: #6E6B7B; }
.copie { margin: 0 0 4px; font-size: 15px; user-select: all; }
@media (max-width: 760px) { table, thead, tbody, tr, th, td { display: block; } thead { display: none; } tbody th { width: auto; background: #F3F0EC; } }
</style></head><body><main>
<h1>Métas SEO des pages</h1>
<p class="intro">À coller dans le panneau Yoast de chaque page (« Titre SEO » et « Méta description »). Titres de 50 à 60 caractères,
mot-clé au début, « Rolling » à la fin ; descriptions de 140 à 160 caractères ; chaque titre est différent du H1 de la page
(rappelé à droite). Un clic sélectionne tout le texte d'une case.</p>
<table><thead><tr><th scope="col">Page</th><th scope="col">Titre SEO</th><th scope="col">Méta description</th><th scope="col">H1 de la page</th></tr></thead>
<tbody>
""" + '\n'.join(lignes) + """
</tbody></table></main></body></html>
"""


if __name__ == '__main__':
    pb = verifier()
    for page, _, _, titre, desc in METAS:
        print('%-30s title %2d  desc %3d' % (page[:30], len(titre), len(desc)))
    print('\n'.join(pb) if pb else 'Toutes les longueurs sont bonnes.')
    if len(sys.argv) > 1:
        open(sys.argv[1], 'w', encoding='utf-8').write(page_html())
    sys.exit(1 if pb else 0)
