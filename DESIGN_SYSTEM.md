# Design System - Notion Style E-Book Library

## 🎨 Overview

Design system terinspirasi dari **Notion** dengan warm minimalism, clean typography, dan whisper borders philosophy.

## 📐 Design Principles

### 1. Warm Minimalism
- Background warm white (#f6f5f4) bukan pure white
- Text near-black (rgba(0,0,0,0.95)) bukan pure black
- Warm gray palette dengan yellow-brown undertones

### 2. Whisper Borders
- Ultra-thin borders: `1px solid rgba(0,0,0,0.1)`
- Borders yang "felt rather than seen"
- Tidak menggunakan heavy borders

### 3. Multi-layer Shadows
- Shadows dengan 4-5 layers
- Individual opacity never exceed 0.05
- Creates depth yang natural, bukan floating effect

## 🎨 Color Palette

```css
/* Primary Colors */
--color-bg-primary: #ffffff        /* Pure white untuk cards */
--color-bg-warm: #f6f5f4          /* Warm white untuk page background */
--color-text-primary: rgba(0,0,0,0.95)  /* Near-black text */
--color-text-secondary: #615d59    /* Warm gray 500 */
--color-text-muted: #a39e98        /* Warm gray 300 */

/* Notion Blue - Primary Accent */
--color-accent: #0075de           /* Primary CTA & links */
--color-accent-hover: #005bab     /* Hover state */

/* Semantic Colors */
--color-success: #1aae39          /* Success states */
--color-danger: #dd5b00           /* Warning/danger */
--color-teal: #2a9d99            /* Alternative accent */

/* Badge Colors */
--color-badge-bg: #f2f9ff         /* Pill badge background */
--color-badge-text: #097fe8       /* Pill badge text */
```

## 🔤 Typography

### Font Family
**Inter** (Google Fonts) dengan fallback system fonts:
```css
font-family: 'Inter', -apple-system, system-ui, 'Segoe UI', Roboto, sans-serif;
```

### Type Scale

| Element | Size | Weight | Letter Spacing |
|---------|------|--------|----------------|
| H1 | 48px | 700 | -1.5px |
| H2 | 40px | 700 | -1px |
| H3 | 26px | 700 | -0.625px |
| H4 | 22px | 700 | -0.25px |
| H5 | 20px | 600 | normal |
| Body | 16px | 400 | normal |
| Button/Nav | 15px | 600 | normal |
| Caption | 14px | 500 | normal |
| Badge | 12px | 600 | 0.125px |

### Typography Rules
- **Compression at scale**: Negative letter-spacing untuk display sizes
- **Four-weight system**: 400 (body), 500 (UI), 600 (emphasis), 700 (headings)
- **Line height**: 1.5 untuk body, tighter (1.0-1.2) untuk headings

## 🧩 Components

### Buttons
```css
/* Primary - Notion Blue */
.btn-primary {
    background: #0075de;
    color: #ffffff;
    border-radius: 4px;
    padding: 8px 16px;
    font-weight: 600;
}

/* Secondary - Translucent Gray */
.btn-secondary {
    background: rgba(0,0,0,0.05);
    color: rgba(0,0,0,0.95);
}
```

**States:**
- Hover: `transform: scale(1.02)`
- Active: `transform: scale(0.98)`

### Badges (Pills)
```css
.badge {
    border-radius: 9999px;  /* Full pill */
    padding: 4px 8px;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.125px;
}
```

**Variants:**
- Admin: Purple background (#f3e8ff)
- Member: Blue tint (#f2f9ff)
- Success: Green tint (#e6f7ed)
- Warning: Orange tint (#fff4e6)

### Cards
```css
.card {
    background: #ffffff;
    border: 1px solid rgba(0,0,0,0.1);  /* Whisper border */
    border-radius: 12px;
    box-shadow: var(--shadow-card);     /* Multi-layer */
}

.card:hover {
    box-shadow: var(--shadow-deep);
    transform: translateY(-2px);
}
```

### Forms
```css
.form-control {
    border: 1px solid rgba(0,0,0,0.1);
    border-radius: 4px;
    padding: 8px 12px;
}

.form-control:focus {
    border-color: #0075de;
    box-shadow: 0 0 0 2px #f2f9ff;  /* Blue tint ring */
}
```

## 📏 Spacing System

Base unit: **8px**

```css
--space-xs: 4px    /* 0.5x */
--space-sm: 8px    /* 1x */
--space-md: 16px   /* 2x */
--space-lg: 24px   /* 3x */
--space-xl: 32px   /* 4x */
--space-2xl: 48px  /* 6x */
--space-3xl: 64px  /* 8x */
```

## 🔲 Border Radius Scale

```css
--radius-sm: 4px     /* Buttons, inputs */
--radius-md: 8px     /* Small cards */
--radius-lg: 12px    /* Standard cards */
--radius-xl: 16px    /* Hero cards */
--radius-pill: 9999px /* Badges */
```

## 🌊 Shadows & Depth

### Card Shadow (4 layers)
```css
--shadow-card: 
    rgba(0,0,0,0.04) 0px 4px 18px,
    rgba(0,0,0,0.027) 0px 2.025px 7.847px,
    rgba(0,0,0,0.02) 0px 0.8px 2.925px,
    rgba(0,0,0,0.01) 0px 0.175px 1.041px;
```

### Deep Shadow (5 layers)
```css
--shadow-deep:
    rgba(0,0,0,0.01) 0px 1px 3px,
    rgba(0,0,0,0.02) 0px 3px 7px,
    rgba(0,0,0,0.02) 0px 7px 15px,
    rgba(0,0,0,0.04) 0px 14px 28px,
    rgba(0,0,0,0.05) 0px 23px 52px;
```

## 📱 Responsive Breakpoints

```css
/* Mobile */
@media (max-width: 480px) { }

/* Tablet */
@media (max-width: 768px) { }

/* Desktop Small */
@media (max-width: 1080px) { }

/* Desktop */
@media (min-width: 1200px) { }
```

### Mobile Strategy
- Grid: 3-column → 2-column → 1-column
- Typography: Scale down H1 from 48px → 32px
- Spacing: Reduce from 24px → 16px
- Navigation: Compact menu dengan smaller gaps

## 🎯 Usage Guidelines

### DO ✅
- Use warm neutrals untuk background (#f6f5f4)
- Use whisper borders (1px rgba)
- Use Notion Blue (#0075de) untuk CTAs
- Use pill badges untuk status
- Use multi-layer shadows
- Use negative letter-spacing pada headings
- Maintain generous whitespace

### DON'T ❌
- Don't use pure black (#000000)
- Don't use heavy borders (>1px)
- Don't use hard shadows (single layer)
- Don't use blue-gray tones
- Don't use aggressive gradients
- Don't center everything
- Don't use excessive decoration

## 📦 Implementation

### HTML Template
```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Title</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Content here -->
</body>
</html>
```

### Button Example
```html
<button class="btn btn-primary">Primary Action</button>
<button class="btn btn-secondary">Secondary Action</button>
```

### Card Example
```html
<div class="card">
    <div class="card-body">
        <h4 class="card-title">Card Title</h4>
        <p class="card-text">Card description text.</p>
        <span class="badge badge-success">Active</span>
    </div>
</div>
```

### Form Example
```html
<div class="form-group">
    <label class="form-label">Label</label>
    <input type="text" class="form-control" placeholder="Enter text...">
</div>
```

## 🚀 Benefits

1. **Warm & Approachable**: Warm neutrals lebih nyaman untuk mata
2. **Clean & Minimal**: Whisper borders tidak mengganggu konten
3. **Professional**: Notion Blue memberikan trust & authority
4. **Scalable**: Component-based system mudah di-maintain
5. **Responsive**: Mobile-first design dengan breakpoints jelas
6. **Accessible**: High contrast text (18:1 ratio)
7. **Modern**: Inter font + clean typography
8. **Familiar**: User sudah kenal Notion aesthetic

## 📚 References

- Notion Design System (official)
- Inter Font Family (Google Fonts)
- popular-web-designs skill (Hermes Agent)
- claude-design skill (design process)

---

**Version:** 1.0  
**Last Updated:** 2026-11-11  
**Maintained by:** UTS Pemrograman Web Project
