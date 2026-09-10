# DESIGN.md — Salon CRM
# Design Token Bridge: Stitch ↔ Antigravity ↔ Claude Code
# Last synced from Stitch: [update this date on every Stitch export]
# Version: 1.0

---

## PURPOSE

This file is the single source of truth for design tokens across the entire
Salon CRM project. It travels with the codebase and is read by:

- **Claude Code (Nova agent)** — for all frontend implementation
- **Antigravity agent** — when building or refactoring components
- **Stitch** — tokens here reflect the exported design system; update after
  every Stitch redesign session

Workflow:
```
Stitch (prompt → design) 
  → Export HTML/CSS via MCP or .zip 
  → Extract tokens into this file 
  → Antigravity agent reads DESIGN.md → builds code 
  → If redesign needed → update Stitch → re-extract → update this file
```

---

## PROJECT CONTEXT

**App**: Salon CRM — appointment, client, and payment management  
**Users**: Non-technical salon specialists (hairdressers, nail techs, salon owners)  
**Platform**: Mobile-first (React Native / Expo). Web admin panel (Next.js)  
**Design philosophy**: Warm, human, touch-optimized. Not clinical. Not corporate.

---

## COLOR TOKENS

```js
// tokens/colors.js — import this everywhere, never hardcode hex values

export const colors = {

  // === BRAND ===
  accent:         '#B5836A',   // Dusty terracotta — primary CTA, active states
  accentLight:    '#F5EDE8',   // Tinted background for accent areas
  accentDark:     '#8C5E4A',   // Hover/pressed state on accent

  accentAlt:      '#7A9E87',   // Sage green — secondary accent (service tags, badges)
  accentAltLight: '#EAF1EC',   // Sage tint background

  // === NEUTRALS (Light Mode) ===
  bg:             '#FDFAF7',   // Warm off-white — main background
  bgSoft:         '#F5F0EB',   // Alternating section / card background
  bgCard:         '#FFFFFF',   // Card surfaces
  text:           '#2C2218',   // Primary text — warm charcoal
  textMuted:      '#7A6E65',   // Secondary text, labels
  textSubtle:     '#ADA49B',   // Timestamps, meta, placeholders
  border:         '#E8E0D8',   // Default border
  borderSoft:     '#F0EBE4',   // Subtle dividers
  badgeBg:        '#EDE8E2',   // Mono badge background
  badgeText:      '#5C5248',   // Mono badge text

  // === DARK MODE ===
  dark: {
    bg:           '#1A1512',
    bgSoft:       '#221D19',
    bgCard:       '#2A2420',
    text:         '#F5EFE9',
    textMuted:    '#ADA49B',
    border:       '#3A322C',
    borderSoft:   '#2E2822',
    badgeBg:      '#2E2822',
    accent:       '#D4A088',   // Lightened for contrast on dark bg (min 4.5:1)
    accentAlt:    '#96BBA3',
  },

  // === SEMANTIC ===
  success:        '#5A8F6B',   // Confirmed appointments
  warning:        '#C9863A',   // Pending / rescheduled
  danger:         '#B05252',   // Cancellations, overdue
  info:           '#5A7FA8',   // Informational states
}
```

---

## TYPOGRAPHY TOKENS

```js
// tokens/typography.js

export const typography = {

  // === FONTS ===
  fontDisplay:  'DM Serif Display',   // Screen titles, client names, section headers
  fontBody:     'DM Sans',            // All body copy, labels, buttons
  fontMono:     'JetBrains Mono',     // Prices, times, IDs, badges

  // === SCALE ===
  // Mobile-first — these are base values; web admin uses larger via clamp()
  sizes: {
    xs:   12,   // Meta, timestamps, fine print
    sm:   13,   // Labels, badges, secondary info
    base: 15,   // Body copy, list items
    md:   17,   // Subheadings, card titles
    lg:   20,   // Section headings (mobile)
    xl:   24,   // Screen titles (mobile)
    xxl:  30,   // Hero / dashboard greeting
  },

  weights: {
    regular:    '400',
    medium:     '500',
    semibold:   '600',
    bold:       '700',
  },

  lineHeights: {
    tight:    1.2,   // Display/hero text
    normal:   1.5,   // Body copy
    relaxed:  1.7,   // Long-form descriptions
  },

  letterSpacing: {
    tight:    '-0.02em',   // Display headings
    normal:   '0em',
    wide:     '0.06em',    // Uppercase labels
    xwide:    '0.10em',    // Section labels (ALL CAPS)
  },
}
```

---

## SPACING & LAYOUT TOKENS

```js
// tokens/spacing.js

export const spacing = {
  // Base unit: 4px
  xs:   4,
  sm:   8,
  md:   16,
  lg:   24,
  xl:   32,
  xxl:  48,
  xxxl: 64,

  // Component-specific
  cardPadding:      20,
  screenPadding:    16,   // Horizontal padding on all screens
  sectionGap:       32,   // Between major sections
  listItemGap:      12,   // Between list rows
  inputHeight:      52,   // Touch-friendly input height
  buttonHeightLg:   52,   // Primary CTA button
  buttonHeightSm:   40,   // Secondary/inline buttons
  navHeight:        64,   // Bottom tab bar height
  avatarSm:         36,
  avatarMd:         48,
  avatarLg:         72,
}

export const radius = {
  sm:   6,    // Badges, chips
  md:   10,   // Cards, inputs
  lg:   16,   // Modals, bottom sheets
  pill: 100,  // Pills, tags, status badges
  full: 9999, // Circular avatars
}
```

---

## SHADOW TOKENS

```js
// tokens/shadows.js (React Native format)

export const shadows = {
  card: {
    shadowColor:    '#2C2218',
    shadowOffset:   { width: 0, height: 2 },
    shadowOpacity:  0.07,
    shadowRadius:   8,
    elevation:      3,
  },
  modal: {
    shadowColor:    '#2C2218',
    shadowOffset:   { width: 0, height: 8 },
    shadowOpacity:  0.15,
    shadowRadius:   24,
    elevation:      12,
  },
  button: {
    shadowColor:    '#B5836A',
    shadowOffset:   { width: 0, height: 3 },
    shadowOpacity:  0.25,
    shadowRadius:   8,
    elevation:      4,
  },
}
```

---

## CSS VARIABLES (Web / Next.js Admin Panel)

```css
/* tokens/globals.css — import in layout.tsx */

:root {
  /* Brand */
  --accent:           #B5836A;
  --accent-light:     #F5EDE8;
  --accent-hover:     #8C5E4A;
  --accent-alt:       #7A9E87;
  --accent-alt-light: #EAF1EC;

  /* Neutrals */
  --bg:               #FDFAF7;
  --bg-soft:          #F5F0EB;
  --bg-card:          #FFFFFF;
  --text:             #2C2218;
  --text-muted:       #7A6E65;
  --text-subtle:      #ADA49B;
  --border:           #E8E0D8;
  --border-soft:      #F0EBE4;
  --badge-bg:         #EDE8E2;
  --badge-text:       #5C5248;

  /* Semantic */
  --success:          #5A8F6B;
  --warning:          #C9863A;
  --danger:           #B05252;
  --info:             #5A7FA8;

  /* Typography */
  --font-display:     'DM Serif Display', serif;
  --font-body:        'DM Sans', sans-serif;
  --font-mono:        'JetBrains Mono', monospace;

  /* Layout */
  --max-w:            420px;    /* Mobile max-width for web admin */
  --radius:           10px;
  --radius-lg:        16px;
  --radius-pill:      100px;
  --transition:       0.2s ease;

  /* Shadows */
  --shadow-card:      0 2px 8px rgba(44,34,24,0.07);
  --shadow-modal:     0 8px 24px rgba(44,34,24,0.15);
}

html[data-theme="dark"] {
  --bg:               #1A1512;
  --bg-soft:          #221D19;
  --bg-card:          #2A2420;
  --text:             #F5EFE9;
  --text-muted:       #ADA49B;
  --border:           #3A322C;
  --border-soft:      #2E2822;
  --badge-bg:         #2E2822;
  --accent:           #D4A088;
  --accent-light:     #2E201A;
  --accent-alt:       #96BBA3;
  --accent-alt-light: #1A2820;
}
```

---

## COMPONENT PATTERNS

### Appointment Status Badge
```
Confirmed   → bg: accentAlt-light  · text: accentAlt  · dot: accentAlt (pulse)
Pending     → bg: warning-light    · text: warning     · dot: warning
Cancelled   → bg: danger-light     · text: danger      · no dot
Completed   → bg: badge-bg         · text: text-muted  · no dot
```

### Service Type Color Coding
```
Hair        → accent (terracotta)
Nails       → accentAlt (sage)
Waxing      → warning (amber)
Facial      → info (blue-grey)
Other       → badge-bg / text-muted
```

### Touch Targets (NON-NEGOTIABLE for non-tech users)
```
Minimum tap area:    44×44px
Preferred:           52×52px for primary actions
List row height:     64px minimum
Input height:        52px
Bottom nav icons:    48×48px tap area
```

### Navigation (Mobile)
```
Bottom tab bar — 4 tabs max:
  Tab 1: Home (today's dashboard)
  Tab 2: Calendar (appointments)
  Tab 3: Clients
  Tab 4: Settings
Active: accent color icon + label
Inactive: text-muted icon + label
```

---

## STITCH SYNC LOG

| Date | Screens Updated | Notes |
|------|----------------|-------|
| [YYYY-MM-DD] | Initial export | First Stitch generation |

**How to update this log:**
After every Stitch session → export HTML/CSS → extract any changed color,
font, or spacing values → update the tokens above → log the date and screens.

---

## AGENT INSTRUCTIONS

### For Claude Code (Nova)
Read this file before generating ANY component for Salon CRM.
Use `colors`, `typography`, `spacing`, and `radius` tokens only — no hardcoded values.
Every component ships with both light and dark mode.
Touch targets must meet the minimums defined above.

### For Antigravity
When the user says "build from DESIGN.md", read this file first via MCP,
then fetch the latest Stitch screens, then implement using these tokens.
If a Stitch-exported color conflicts with a token here, flag it — do not silently override.

### Redesign Trigger
If user says "redesign X screen in Stitch":
1. Open Stitch project → iterate on that screen
2. Export updated HTML/CSS
3. Diff the token values against this file
4. Update DESIGN.md with new values
5. Re-implement affected components

---