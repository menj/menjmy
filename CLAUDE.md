# Sasterawan theme — working rules

- **Three viewports, always.** Every visual change is rendered and checked at
  desktop (1440px), tablet (768px portrait and 1024px landscape) and phone
  (390px, with 360px when type is near the edge) before it is committed.
  No overflow, no wrapped header pill, hero title on one line.
- Assets stay separated: CSS in `assets/css/`, JS in `assets/js/`, fonts in
  `assets/fonts/`, images in `assets/img/`. No inline styles or scripts in
  templates beyond what WordPress requires.
- Settings are tabbed (`inc/admin/settings.php`); new options are registered,
  added to the tab save map, and given a field in the right tab.
- Palette: gold, white paper, royal blue, rubric red. Pills (`--bwp-pill`)
  and 20/28/40px radii. Fonts: Kagnue (brand), Sabon Next LT (body),
  Ferments (script), Special Elite (labels), Dubidam (Jawi).
- Spelling: "terbaharu", not "terbaru".
