// Icônes des boutons de partage (PNG transparents, 72 px, encre #0F0F12) : theme/rolling/assets/medias/partage-*.png
// Usage : node outils/icones-partage.js
const { chromium } = require('playwright');
const path = require('path');
const C = '#0F0F12';
const ICONES = {
  linkedin: `<path d="M4.98 3.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5zM3 9h4v12H3zM9 9h3.8v1.7h.05c.53-1 1.84-2.05 3.78-2.05C20.6 8.65 21 11.2 21 14.5V21h-4v-5.8c0-1.4 0-3.2-1.95-3.2S12.8 13.5 12.8 15.1V21H9z" fill="${C}"/>`,
  facebook: `<path d="M14 8h3V4h-3c-2.8 0-4.5 1.7-4.5 4.6V11H7v4h2.5v7h4v-7h3l.5-4h-3.5V8.8c0-.6.3-.8 1-.8z" fill="${C}"/>`,
  x: `<path d="M17.5 3h3.1l-6.8 7.8 8 10.2h-6.3l-4.9-6.4L5 21H1.9l7.3-8.3L1.5 3h6.4l4.4 5.9zm-1.1 16.2h1.7L7.7 4.7H5.9z" fill="${C}"/>`,
  whatsapp: `<path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm5.3 14.2c-.2.6-1.3 1.2-1.8 1.2-.5.1-1 .1-3.3-.8-2.8-1.1-4.6-4-4.7-4.2-.1-.2-1.1-1.5-1.1-2.9s.7-2.1 1-2.4c.2-.3.5-.3.7-.3h.5c.2 0 .4 0 .6.5l.8 2c.1.2.1.4 0 .5l-.3.5-.4.4c-.1.1-.3.3-.1.6.2.3.8 1.3 1.7 2.1 1.2 1 2.1 1.3 2.4 1.5.3.1.5.1.6-.1l.9-1c.2-.3.4-.2.6-.1l1.9.9c.3.1.5.2.5.3.1.2.1.7-.1 1.3z" fill="${C}"/>`,
  email: `<path d="M3 5h18v14H3z" fill="none" stroke="${C}" stroke-width="2"/><path d="M3 6l9 7 9-7" fill="none" stroke="${C}" stroke-width="2"/>`,
  copier: `<rect x="8" y="8" width="12" height="12" rx="2" fill="none" stroke="${C}" stroke-width="2"/><path d="M16 8V5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h3" fill="none" stroke="${C}" stroke-width="2"/>`,
};
(async () => {
  const b = await chromium.launch();
  const p = await b.newPage({ viewport: { width: 72, height: 72 } });
  for (const [nom, svg] of Object.entries(ICONES)) {
    await p.setContent(`<html><body style="margin:0;background:transparent"><svg width="72" height="72" viewBox="0 0 24 24">${svg}</svg></body></html>`);
    await p.screenshot({ path: path.join(__dirname, '../theme/rolling/assets/medias/partage-' + nom + '.png'), omitBackground: true });
  }
  await b.close();
})();
