# 🎨 Domaintik Design System

Dokumentasi sistem desain untuk aplikasi **Domaintik** - Layanan Domain & Hosting TIK Universitas Lampung.

---

## 📋 Daftar Isi

- [Font](#-font)
- [Warna](#-warna)
- [Komponen](#-komponen)
- [Gradient](#-gradient)
- [Spacing & Layout](#-spacing--layout)
- [Panduan Penggunaan](#-panduan-penggunaan)

---

## 🔤 Font

### Font Utama
| Nama | Kegunaan |
|------|----------|
| **Instrument Sans** | Font utama untuk semua teks UI |

### Konfigurasi
```css
--font-sans: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
```

### Penggunaan di HTML
Font di-import dari **Bunny Fonts** di file layout:
```html
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />
```

### Font Weight
| Class | Weight | Kegunaan |
|-------|--------|----------|
| `font-normal` | 400 | Body text, paragraf |
| `font-medium` | 500 | Label form, subtitle |
| `font-semibold` | 600 | Button, heading kecil |
| `font-bold` | 700 | Heading utama, judul |

---

## 🎨 Warna

### Primary Colors (Unila Brand)

Warna utama menggunakan palet **MyUnila** dengan kode hex `#0B5EA8`.

| Token | Hex Code | Preview | Kegunaan |
|-------|----------|---------|----------|
| `myunila` | `#0B5EA8` | ![#0B5EA8](https://via.placeholder.com/20/0B5EA8/0B5EA8) | Primary default |
| `myunila-50` | `#E6F2FA` | ![#E6F2FA](https://via.placeholder.com/20/E6F2FA/E6F2FA) | Background sangat terang |
| `myunila-100` | `#CCE5F5` | ![#CCE5F5](https://via.placeholder.com/20/CCE5F5/CCE5F5) | Background terang |
| `myunila-200` | `#99CBE8` | ![#99CBE8](https://via.placeholder.com/20/99CBE8/99CBE8) | Border, divider |
| `myunila-300` | `#66B1DC` | ![#66B1DC](https://via.placeholder.com/20/66B1DC/66B1DC) | Icon disabled |
| `myunila-400` | `#3397CF` | ![#3397CF](https://via.placeholder.com/20/3397CF/3397CF) | Icon secondary |
| `myunila-500` | `#0B5EA8` | ![#0B5EA8](https://via.placeholder.com/20/0B5EA8/0B5EA8) | Primary (sama dengan default) |
| `myunila-600` | `#094B86` | ![#094B86](https://via.placeholder.com/20/094B86/094B86) | Hover state |
| `myunila-700` | `#073864` | ![#073864](https://via.placeholder.com/20/073864/073864) | Active/pressed state |
| `myunila-800` | `#052542` | ![#052542](https://via.placeholder.com/20/052542/052542) | Dark variant |
| `myunila-900` | `#021220` | ![#021220](https://via.placeholder.com/20/021220/021220) | Paling gelap |

### Semantic Colors

| Token | Hex Code | Light Variant | Kegunaan |
|-------|----------|---------------|----------|
| `success` | `#10B981` | `#D1FAE5` | Sukses, berhasil, aktif |
| `warning` | `#F59E0B` | `#FEF3C7` | Peringatan, pending |
| `error` | `#EF4444` | `#FEE2E2` | Error, gagal, ditolak |
| `info` | `#3B82F6` | `#DBEAFE` | Informasi, tips |

### Neutral Colors (Gray)

| Token | Hex Code | Kegunaan |
|-------|----------|----------|
| `gray-50` | `#F9FAFB` | Background page |
| `gray-100` | `#F3F4F6` | Background card |
| `gray-200` | `#E5E7EB` | Border, divider |
| `gray-300` | `#D1D5DB` | Input border |
| `gray-400` | `#9CA3AF` | Placeholder text |
| `gray-500` | `#6B7280` | Secondary text |
| `gray-600` | `#4B5563` | Body text |
| `gray-700` | `#374151` | Heading secondary |
| `gray-800` | `#1F2937` | Heading primary |
| `gray-900` | `#111827` | Text paling gelap |

---

## 🧩 Komponen

### Buttons

#### Primary Button
```html
<button class="btn-primary">
    Label Button
</button>
```

**Spesifikasi:**
- Background: `bg-myunila`
- Text: `text-white`
- Padding: `px-6 py-3`
- Border Radius: `rounded-xl`
- Hover: `bg-myunila-700` dengan shadow

#### Secondary Button
```html
<button class="btn-secondary">
    Label Button
</button>
```

**Spesifikasi:**
- Background: `bg-white`
- Border: `border border-myunila-200`
- Text: `text-myunila-700`
- Hover: `bg-myunila-50`

### Form Input

#### Standard Input
```html
<input type="text" class="form-input" placeholder="Placeholder...">
```

**Spesifikasi:**
- Border: `border-gray-300`
- Padding: `px-4 py-3`
- Border Radius: `rounded-lg`
- Focus: `border-myunila-500` + ring

#### Error State Input
```html
<input type="text" class="form-input form-input-error">
<p class="text-sm text-error">Pesan error</p>
```

### Cards

#### Standard Card
```html
<div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 bg-myunila-50 px-6 py-4">
        <h2 class="font-semibold text-gray-900">Judul Card</h2>
    </div>
    <div class="p-6">
        <!-- Content -->
    </div>
</div>
```

### Status Badges

```html
<!-- Draft -->
<span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">
    <span class="h-1.5 w-1.5 rounded-full bg-gray-500"></span>
    Draft
</span>

<!-- Submitted -->
<span class="inline-flex items-center gap-1.5 rounded-full bg-info-light px-3 py-1 text-xs font-medium text-info">
    <span class="h-1.5 w-1.5 rounded-full bg-info"></span>
    Submitted
</span>

<!-- In Review -->
<span class="inline-flex items-center gap-1.5 rounded-full bg-warning-light px-3 py-1 text-xs font-medium text-warning">
    <span class="h-1.5 w-1.5 rounded-full bg-warning"></span>
    In Review
</span>

<!-- Completed -->
<span class="inline-flex items-center gap-1.5 rounded-full bg-success-light px-3 py-1 text-xs font-medium text-success">
    <span class="h-1.5 w-1.5 rounded-full bg-success"></span>
    Completed
</span>

<!-- Rejected -->
<span class="inline-flex items-center gap-1.5 rounded-full bg-error-light px-3 py-1 text-xs font-medium text-error">
    <span class="h-1.5 w-1.5 rounded-full bg-error"></span>
    Rejected
</span>
```

---

## 🌈 Gradient

### Gradient Presets

| Class | Gradient | Kegunaan |
|-------|----------|----------|
| `.bg-gradient-unila` | `#0B5EA8` → `#073864` | Primary gradient (CTA, header) |
| `.bg-gradient-ocean` | `#0B5EA8` → `#00CED1` | Secondary gradient (hosting card) |
| `.bg-gradient-sky` | `#00B4DB` → `#0083B0` | Alternative gradient |
| `.bg-gradient-blue-modern` | `#667eea` → `#764ba2` | Purple-blue gradient |

### Penggunaan
```html
<!-- Gradient Unila untuk CTA Section -->
<div class="bg-gradient-unila p-8 text-white">
    <h3>Butuh Bantuan?</h3>
</div>

<!-- Gradient untuk Icon Container -->
<div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-unila text-white">
    <svg>...</svg>
</div>
```

---

## 📐 Spacing & Layout

### Container
```html
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <!-- Content -->
</div>
```

### Section Spacing
| Class | Spacing | Kegunaan |
|-------|---------|----------|
| `py-8 lg:py-12` | 32px / 48px | Content section |
| `py-16 lg:py-20` | 64px / 80px | Major section |

### Border Radius
| Class | Radius | Kegunaan |
|-------|--------|----------|
| `rounded-lg` | 8px | Input, small cards |
| `rounded-xl` | 12px | Buttons, badges |
| `rounded-2xl` | 16px | Cards, sections |
| `rounded-3xl` | 24px | Large cards, hero elements |
| `rounded-full` | 50% | Avatar, circular icons |

### Shadow
| Class | Kegunaan |
|-------|----------|
| `shadow-sm` | Card default |
| `shadow-lg` | Elevated elements |
| `shadow-xl` | Modals, dropdowns |
| `shadow-2xl` | Hero sections |
| `shadow-myunila/30` | Colored shadow untuk primary |
| `shadow-success/30` | Colored shadow untuk success |

---

## 📖 Panduan Penggunaan

### Do's ✅

1. **Gunakan warna `myunila` untuk aksen utama**
   ```html
   <a href="#" class="text-myunila hover:text-myunila-700">Link</a>
   ```

2. **Gunakan semantic colors sesuai konteks**
   - `success` → untuk status berhasil, aktif
   - `warning` → untuk pending, perhatian
   - `error` → untuk gagal, ditolak
   - `info` → untuk informasi

3. **Konsisten dengan spacing**
   - Gap antar elemen: `gap-4`, `gap-6`, `gap-8`
   - Padding card: `p-6`, `p-8`

4. **Gunakan class utility yang sudah didefinisikan**
   ```html
   <button class="btn-primary">Submit</button>
   <input class="form-input">
   ```

### Don'ts ❌

1. **Jangan gunakan warna hardcoded**
   ```html
   <!-- ❌ Salah -->
   <div style="color: #0B5EA8">

   <!-- ✅ Benar -->
   <div class="text-myunila">
   ```

2. **Jangan campur style lama (slate, indigo, purple)**
   ```html
   <!-- ❌ Salah - warna lama -->
   <div class="bg-slate-100 text-indigo-600">

   <!-- ✅ Benar - gunakan myunila -->
   <div class="bg-myunila-50 text-myunila">
   ```

3. **Jangan gunakan bg-gradient-to-* (Tailwind v3 syntax)**
   ```html
   <!-- ❌ Salah - Tailwind v3 -->
   <div class="bg-gradient-to-r from-blue-600 to-indigo-600">

   <!-- ✅ Benar - Tailwind v4 atau custom class -->
   <div class="bg-gradient-unila">
   <!-- atau -->
   <div class="bg-linear-to-r from-myunila to-myunila-700">
   ```

---

## 🔧 File Referensi

| File | Lokasi | Keterangan |
|------|--------|------------|
| CSS Utama | `resources/css/app.css` | Definisi @theme dan custom classes |
| Layout | `resources/views/layouts/app.blade.php` | Base layout dengan nav & footer |
| Tailwind Config | Inline via `@theme` di app.css | Tailwind CSS v4 style |

---

## 📝 Changelog

| Tanggal | Perubahan |
|---------|-----------|
| 12 Jan 2026 | Initial design system dengan Unila brand colors |
| 12 Jan 2026 | Migrasi dari blue/indigo ke myunila palette |
| 12 Jan 2026 | Update ke Tailwind CSS v4 syntax |

---

*Dokumentasi ini dibuat untuk menjaga konsistensi desain di seluruh aplikasi Domaintik.*
