---
name: Terra & Silk
colors:
  surface: '#fcf9f6'
  surface-dim: '#dcdad7'
  surface-bright: '#fcf9f6'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f6f3f0'
  surface-container: '#f0edea'
  surface-container-high: '#eae8e5'
  surface-container-highest: '#e5e2df'
  on-surface: '#1b1c1a'
  on-surface-variant: '#51443e'
  inverse-surface: '#31302f'
  inverse-on-surface: '#f3f0ed'
  outline: '#83746d'
  outline-variant: '#d5c3bb'
  surface-tint: '#7f543e'
  primary: '#7d523c'
  on-primary: '#ffffff'
  primary-container: '#986a52'
  on-primary-container: '#fffbff'
  inverse-primary: '#f3ba9f'
  secondary: '#645d53'
  on-secondary: '#ffffff'
  secondary-container: '#e8ded1'
  on-secondary-container: '#686257'
  tertiary: '#675a4d'
  on-tertiary: '#ffffff'
  tertiary-container: '#807264'
  on-tertiary-container: '#fffbff'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#ffdbcb'
  primary-fixed-dim: '#f3ba9f'
  on-primary-fixed: '#311303'
  on-primary-fixed-variant: '#643d28'
  secondary-fixed: '#ebe1d4'
  secondary-fixed-dim: '#cfc5b9'
  on-secondary-fixed: '#1f1b13'
  on-secondary-fixed-variant: '#4c463c'
  tertiary-fixed: '#f2dfcf'
  tertiary-fixed-dim: '#d5c4b4'
  on-tertiary-fixed: '#231a10'
  on-tertiary-fixed-variant: '#504539'
  background: '#fcf9f6'
  on-background: '#1b1c1a'
  surface-variant: '#e5e2df'
typography:
  display-lg:
    fontFamily: DM Serif Display
    fontSize: 48px
    fontWeight: '400'
    lineHeight: 56px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: DM Serif Display
    fontSize: 32px
    fontWeight: '400'
    lineHeight: 40px
  headline-md:
    fontFamily: DM Serif Display
    fontSize: 24px
    fontWeight: '400'
    lineHeight: 32px
  body-lg:
    fontFamily: DM Sans
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: DM Sans
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-sm:
    fontFamily: DM Sans
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-md:
    fontFamily: DM Sans
    fontSize: 14px
    fontWeight: '700'
    lineHeight: 16px
    letterSpacing: 0.05em
  label-sm:
    fontFamily: DM Sans
    fontSize: 12px
    fontWeight: '700'
    lineHeight: 14px
    letterSpacing: 0.05em
  headline-lg-mobile:
    fontFamily: DM Serif Display
    fontSize: 28px
    fontWeight: '400'
    lineHeight: 36px
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  base: 4px
  xs: 8px
  sm: 16px
  md: 24px
  lg: 32px
  xl: 48px
  xxl: 64px
  gutter: 24px
  margin-mobile: 16px
  margin-desktop: 40px
---

## Brand & Style
The design system is centered on a "Sophisticated Organic" aesthetic, specifically tailored for high-end Salon CRM environments. It balances the warmth of a boutique physical space with the efficiency of modern SaaS. 

The personality is approachable yet authoritative, avoiding the sterile coldness of traditional enterprise software. The style leverages a **Corporate/Modern** foundation infused with **Minimalist** and **Tactile** nuances. Expect generous whitespace, high-quality typography, and a "soft-touch" interface that evokes the feeling of premium paper or linen. 

The goal is to reduce cognitive load for busy salon professionals while maintaining a premium brand experience for their clients.

## Colors
The palette is rooted in natural, earthy tones to provide a calming workspace. 

- **Primary (Dusty Terracotta):** Reserved for primary actions, calls to action, and active states. It provides a warm focal point without the aggression of pure red or the coldness of blue.
- **Secondary (Warm Stone):** Used for subtle backgrounds, inactive tabs, and secondary buttons. It bridges the gap between the primary color and the background.
- **Background (Off-White):** The foundation of the UI. This warmth prevents screen glare and makes the software feel more "analog" and high-end.
- **Text (Deep Charcoal):** A warm black that maintains high legibility while appearing softer and more integrated into the color story than a pure hex #000000.

## Typography
This design system utilizes a high-contrast typographic pairing. 

**DM Serif Display** is used for brand moments, large page titles, and editorial-style headers. It injects a sense of luxury and tradition. 

**DM Sans** handles all functional UI tasks. It is chosen for its geometric clarity and exceptional readability at small sizes, crucial for appointment calendars and client databases. 

Labels and small metadata should use the Bold weight of DM Sans with a slight letter-spacing increase to ensure distinction from body text.

## Layout & Spacing
The layout follows a **Fixed Grid** philosophy for the main content area (max-width 1440px) to maintain a curated, editorial feel, while the sidebar/navigation remains anchored. 

- **Grid:** 12-column system on desktop, 4-column on mobile.
- **Rhythm:** An 8px linear scale is used for most components, while 4px increments are reserved for tight internal element spacing (e.g., icon-to-text).
- **Desktop:** 40px outer margins provide "breathing room" that reinforces the minimalist luxury aesthetic.
- **Mobile:** Elements reflow to a single column. Horizontal lists (e.g., service categories) should remain on a single line with overflow scrolling to save vertical space.

## Elevation & Depth
Depth is created through **Tonal Layers** and **Ambient Shadows**. 

1. **Base:** The warm off-white background (#FDFAF7).
2. **Surface:** Cards and containers use pure white (#FFFFFF) to subtly pop from the background.
3. **Shadows:** Avoid heavy, muddy shadows. Use a "Silk Shadow" approach: a multi-layered shadow with low opacity (4-8%) and a slight tint of the primary terracotta or warm grey. This ensures the UI feels light and "floated" rather than heavy or pinned.
4. **Outlines:** Use 1px borders in a light stone color (#E8DED1) for input fields and structural dividers to maintain definition without relying on shadows alone.

## Shapes
The shape language is consistently **Rounded**, specifically utilizing a 10px radius for standard components. This specific radius is soft enough to feel welcoming but structured enough to remain professional.

- **Buttons & Inputs:** 10px (Standard).
- **Cards & Modals:** 16px (Large) to create a clear container hierarchy.
- **Small Elements (Badges/Chips):** 4px or fully pill-shaped depending on the content density.

## Components
- **Buttons:** Primary buttons use the #B5836A background with white text. Secondary buttons use a #E8DED1 background or a 1px border with the text in #5F5852. All buttons have a 10px radius.
- **Input Fields:** Use a subtle warm-grey border. On focus, the border transitions to the primary terracotta color with a 2px soft outer glow.
- **Cards:** White background, 16px corner radius, and a "Silk Shadow." Used for client profiles, appointment details, and dashboard widgets.
- **Calendar Slots:** Use soft, desaturated versions of the primary color to indicate different service types (e.g., Hair, Nails, Spa) while maintaining a cohesive warm aesthetic.
- **Checkboxes & Radios:** Use the primary terracotta for the active state. Avoid sharp corners; checkboxes should have a 2px radius.
- **Lists:** High-density lists (like client rosters) use subtle 1px dividers in #E8DED1. Use generous vertical padding (12px-16px) to ensure touch-targets are accessible for tablets.