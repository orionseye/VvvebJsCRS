// ─── CATEGORY MAP ─────────────────────────────────────────────────────────────
// Just change names here — everything else auto-generates!
const sectionCategories = {
  "120": "Hero",
  "102": "Photos",
  "121": "Features",
  "122": "Pricing",
  // ... add more as needed
};

// ─── SNIPPETS ─────────────────────────────────────────────────────────────────
// Keep adding snippets here in the simple format — the engine handles the rest.
const snippets = [
  {
    image: "preview/basic-01b.png",
    category: "120",
    html: `
      <div class="row">
      <div class="display">
        <h1 class="font-medium">Design the way you think.</h1>
        <p>Build your site with more creative freedom.</p>
      </div>
      </div>
    `
  },
  {
    image: "preview/basic-06.png",
    category: "120",
    html: `
      <div class="row">
        <div class="column half">
          <p>Lorem Ipsum has been the industry's standard dummy text.</p>
        </div>
        <div class="column half">
          <p>Lorem Ipsum has been the industry's standard dummy text.</p>
        </div>
      </div>
    `
  },
  {
    image: "preview/photos-07.png",
    category: "102",
    html: `
      <div class="row">
        <div class="column third">
          <img src="assets/minimalist-blocks/images/img-1350x900.png" alt="">
          <h3 class="font-normal size-21">Item One</h3>
          <p>Lorem Ipsum is simply dummy text.</p>
        </div>
      </div>
    `
  },
];

// ─── ENGINE ───────────────────────────────────────────────────────────────────
// Don't touch this part — it auto-registers everything above.
(function registerSections(snippets, categoryMap) {
  const counters = {};
  const groups = {};

  function slugify(name) {
    return name.toLowerCase().replace(/\s+/g, "-");
  }

  function wrapSection(html) {
    const trimmed = html.trim();
    return trimmed.startsWith("<section") ? trimmed : `<section class="row">\n${trimmed}\n</section>`;
  }

  for (const snippet of snippets) {
    const catId   = String(snippet.category);
    const catName = categoryMap[catId] || `category-${catId}`;
    const slug    = slugify(catName);

    counters[slug] = (counters[slug] || 0) + 1;
    const key = `${slug}/${slug}-${counters[slug]}`;

    if (!groups[catName]) groups[catName] = [];
    groups[catName].push(key);

    Vvveb.Sections.add(key, {
      name:  `${catName} ${counters[slug]}`,
      image: Vvveb.themeBaseUrl + "/" + (snippet.image || ""),
      html:  wrapSection(snippet.html || ""),
    });
  }

  for (const [catName, keys] of Object.entries(groups)) {
    Vvveb.SectionsGroup[catName] = keys;
  }

})(snippets, sectionCategories);
