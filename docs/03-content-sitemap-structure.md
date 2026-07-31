# 03 — Content & Sitemap Structure

## Pages (Primary Navigation)
1. Home
2. About Us (/about-ogc-newfinity/)
3. Our Services (/services/)
4. Our Mission (/our-mission/)
5. Submit Your Idea (/submit-idea/) — lead-gen form page
6. Blog & Insights (/blog/)
7. Help & Support (/support/)
8. Contact / Get in Touch (/contact/)

Note: No duplicate URLs. No leftover demo pages (Shop, Cart, Typography, Elements, etc. — all must be removed, these were leftovers from the old theme).

## Legal Pages (Footer — required for NL/EU agency)
Required for a Netherlands/EU-based digital agency that takes client payments and refunds. Linked from the site footer Legal column.

1. **Privacy Policy** (`/privacy-policy/`)
2. **Terms and Conditions** (`/terms-and-conditions/`)
3. **Cookie Policy** (`/cookie-policy/`)
4. **Refund and Cancellation Policy** (`/refund-policy/`)

Implemented via `page-legal.php` + `inc/seed-legal.php` + `inc/seed-content/legal-*.md` (version-gated markdown sync; template includes lawyer-review notice).

## Page-by-Page Content Notes

### Home
- Hero section with brand tagline, intro to the 6 services, primary CTA (Submit Idea / Contact).
- Services overview (grid preview of 6 services, links to full Services page).
- Trust/why-us section.
- CTA band before footer.

### About Us
- Hero: "About OGC NewFinity"
- Company description, founded 2022, The Hague NL.
- Three sub-sections: Our Mission, Our Vision, Our Values (as separate cards/anchors — must NOT duplicate content twice like the old site did).
- Services list recap.

### Our Services
- Hero: "Our Services"
- Grid of 6 service cards: Software & App Development, Custom WordPress Development, UI/UX Design, Marketing & Strategy Consulting, Cybersecurity, AI Solutions.
- Each card: icon, title, short description (1-2 lines).

### Our Mission
- Hero: "Our Mission"
- Real, original copy only — NOT the leftover template placeholder text that existed on the old site. Must write genuine OGC NewFinity mission copy.
- 3 pillars: Intelligence With Purpose, Ethical by Design, Future Innovation.

### Submit Your Idea
- Hero + short intro copy.
- Form fields: Project Title, Category (dropdown — matches service list), Description (textarea), Submit button.
- Side panel: 3 trust cards — Expert Review, Confidential, Fast Kickoff.
- Footer meta: "The Hague, Netherlands - Free & Confidential"
- The manual form is the default theme state and will be fully replaced (not supplemented) by Amy Agent once the plugin ships — see docs/09-amy-agent-plugin-scope.md.
- Implemented via page-submit-idea.php + inc/submit-idea-form.php + hero-idea-blueprint + ogc_amy_agent_is_active() stub (manual form default; admin-post mail to contact@ogcnewfinity.com).

### Blog & Insights
- Hero: "Blog & Insights"
- Category cards (adjusted since blockchain is removed) — categories relevant to the new 6-service list, e.g. AI Insights / Tech Guides / Security Updates / Marketing Tips (finalize wording later).
- Standard WP blog loop below hero (latest posts).

### Help & Support
- Hero: "Help & Support" — lead: Browse FAQs · 48h Response Guaranteed; visual: hero-support-network.
- Categories: General, Technical Support, Collaboration (3 cards + matching FAQ accordion sections).
- Client-side FAQ search + guaranteed 48h response note.
- Closing CTA → Get in Touch (/contact/) + contact@ogcnewfinity.com (support questions, not Submit Your Idea).
- Amy Agent on this URL: FAQ-first context for the future plugin — see docs/09-amy-agent-plugin-scope.md.

### Contact / Get in Touch
- Hero: "Get in Touch" — lead: The Hague + 48h response; visual: hero-envelope-send.
- Quick-tag pills for services (must reflect final 6-service list, not old AI/Blockchain/Web only).
- Contact form + "Send a Message" button.
- Right column: 3 short cards describing each core capability area (content must be real, proofread copy — NOT the garbled/broken text seen on the old site).
- Location: The Hague, Netherlands.
- Implemented via page-contact.php + inc/contact-form.php + hero-envelope-send (manual form default; admin-post mail to contact@ogcnewfinity.com).
- Email delivery requires WP Mail SMTP (Hostinger mailbox) — see docs/10-email-delivery-setup.md (not a theme code fix).

## Content Rules (non-negotiable, based on audit of old site)
- No placeholder/demo template text anywhere.
- No duplicated sections on the same page.
- No broken/garbled copy.
- No dead/irrelevant leftover links (Shop, Cart, etc.).
- Every page has exactly one canonical URL.

## Delivery Rule (added per client instruction)
Every page must be delivered fully complete with final, real content directly by Cursor at build time — text, images, and structure. The client should NOT need to log into WordPress after theme upload to add or edit content page by page. WordPress dashboard access afterward is only for future small changes (colors, fonts, images, simple text tweaks) — not for initial content population. See also docs/08-seo-requirements.md for how real final content ties directly into SEO quality.
