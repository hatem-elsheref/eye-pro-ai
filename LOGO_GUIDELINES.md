# Eye Pro - Logo Guidelines & Recommendations

## 📐 **Recommended Logo Sizes**

### **Sidebar Logo (Current Best Practice)**
- **Size:** `48px × 48px` (h-12 w-12)
- **Display:** Square with rounded corners (rounded-lg or rounded-xl)
- **Why:** Perfectly visible without overwhelming sidebar

### **Alternative Sizes**
- **Small:** `32px × 32px` (h-8 w-8) - For compact headers
- **Medium:** `48px × 48px` (h-12 w-12) - **RECOMMENDED** for sidebar
- **Large:** `64px × 64px` (h-16 w-16) - For auth pages, hero sections
- **Extra Large:** `96px × 96px` (h-24 w-24) - For splash screens, welcome pages

## 🎨 **Logo Color Recommendations**

### **Primary Logo Colors (Based on Brand Palette)**

#### **Option 1: White/Transparent Logo (RECOMMENDED)**
- **Use:** White logo on dark sidebar background
- **Background:** Transparent or white fill
- **When:** Sidebar, dark headers, dark backgrounds
- **Format:** PNG with transparency (recommended)

#### **Option 2: Cyan/Gradient Logo**
- **Colors:** Use brand cyan gradient (#0891b2 → #0e7490)
- **When:** Light backgrounds, footer, standalone branding
- **Format:** SVG or PNG

#### **Option 3: Monochrome Logo**
- **Colors:** Single cyan (#0891b2) or dark gray (#1e293b)
- **When:** Print, monochrome contexts

### **Logo Transparency**
✅ **YES - Use Transparent Background**
- **Why:** Works on all backgrounds (dark sidebar, light content areas)
- **Format:** PNG-24 with alpha channel or SVG
- **Best Practice:** Logo should adapt to background

## 🖼️ **Logo Implementation**

### **Current Implementation:**
```html
<img src="{{ asset('logo.jpeg') }}" alt="Eye Pro" class="h-12 w-12 rounded-lg">
```

### **Recommended Update:**
```html
<!-- Sidebar Logo -->
<img src="{{ asset('logo.png') }}" alt="Eye Pro" class="h-12 w-12 rounded-lg shadow-lg">

<!-- Auth Page Logo (Larger) -->
<img src="{{ asset('logo.png') }}" alt="Eye Pro" class="h-16 w-16 rounded-xl shadow-xl">
```

## ✅ **Logo Requirements Checklist**
- [ ] Transparent background (PNG-24 or SVG)
- [ ] Square format (1:1 aspect ratio)
- [ ] High resolution (at least 512×512px source)
- [ ] White icon/symbol for dark backgrounds
- [ ] Scalable (SVG preferred for web)

## 🎯 **Logo Placement**
1. **Sidebar:** 48px × 48px, rounded-lg
2. **Navbar:** 40px × 40px, rounded
3. **Auth Pages:** 64px × 64px, rounded-xl
4. **Favicon:** 32px × 32px, rounded

---

**Summary:** Use **transparent PNG or SVG** with **white/cyan colors**, sized at **48px × 48px** for sidebar, **64px × 64px** for auth pages.



