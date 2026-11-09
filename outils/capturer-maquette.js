// Capture des illustrations décoratives de la maquette (fausses interfaces) en PNG transparent, 2x.
// Seul l'élément visé reste visible ; une marge garde son ombre et sa rotation.
// node outils/capturer-maquette.js <url de la maquette> <dossier de sortie> <liste.json>
// liste.json : [{ "selecteur": "#vague .mock", "rang": 0, "fichier": "constat-email.png", "marge": 40, "largeur": 1440 }]
const { chromium } = require('playwright');
const fs = require('fs');

(async () => {
  const [url, dossier, fichierListe] = process.argv.slice(2);
  const liste = JSON.parse(fs.readFileSync(fichierListe, 'utf8'));
  const b = await chromium.launch();
  const pages = {};
  for (const c of liste) {
    const largeur = c.largeur || 1440;
    if (!pages[largeur]) {
      const p = await b.newPage({ viewport: { width: largeur, height: 1000 }, deviceScaleFactor: 2, reducedMotion: 'reduce' });
      await p.goto(url, { waitUntil: 'networkidle' });
      await p.waitForTimeout(1500);
      pages[largeur] = p;
    }
    const p = pages[largeur];
    const el = (await p.$$(c.selecteur))[c.rang || 0];
    if (!el) { console.log('INTROUVABLE', c.selecteur, c.rang); continue; }
    await el.scrollIntoViewIfNeeded();
    await el.evaluate((e) => e.setAttribute('data-capture', '1'));
    const style = await p.addStyleTag({ content: '*{visibility:hidden!important;animation:none!important;transition:none!important}[data-capture],[data-capture] *{visibility:visible!important}html,body{background:transparent!important}' });
    await p.waitForTimeout(150);
    const bb = await el.boundingBox();
    const m = c.marge ?? 40;
    await p.screenshot({ path: dossier + '/' + c.fichier, omitBackground: true, clip: { x: Math.max(0, bb.x - m), y: Math.max(0, bb.y - m), width: bb.width + 2 * m, height: bb.height + 2 * m } });
    await style.evaluate((s) => s.remove());
    await el.evaluate((e) => e.removeAttribute('data-capture'));
    console.log(c.fichier, Math.round(bb.width), 'x', Math.round(bb.height));
  }
  await b.close();
})();
