# Sistem Ikon & IconPicker

Sistem Ikon dalam aplikasi ini dirancang khusus untuk memfasilitasi penggunaan ikon yang dinamis dari database tanpa harus mengimpor seluruh *library* SVG ke dalam *bundle* frontend (yang dapat menyebabkan membengkaknya ukuran file JavaScript).

## Arsitektur

Fitur ini terbagi menjadi 3 bagian utama:
1. **Icon Registry (`icon-registry.ts`)**: Berisi koleksi preset dari `lucide-react` yang paling sering digunakan. Terbatas pada ikon yang terdaftar secara eksplisit sehingga mendukung proses *tree-shaking* di Vite.
2. **IconPicker Component (`IconPicker.tsx`)**: Antarmuka interaktif berupa modal dengan dua tab. Tab pertama untuk memilih ikon dari *Library*, dan tab kedua untuk mengunggah ikon sendiri (Custom Upload).
3. **Blade Component (`<x-icon>`)**: Komponen sisi server yang dapat memproses format string khusus untuk merender ikon dengan tepat di frontend publik.

---

## 1. Format Penyimpanan (Database)

Data ikon disimpan dalam kolom JSON atau VARCHAR di database dalam format string. Sistem mengenali sumber ikon melalui awalan *(prefix)*:

- **`lucide:`** → Menandakan ikon dari *Icon Registry* (contoh: `lucide:BookOpen`).
- **`url:`** → Menandakan ikon kustom yang diunggah secara lokal (contoh: `url:/storage/icons/custom.svg`).

*(Catatan: Jika string di database sama sekali tidak memiliki awalan, sistem secara fallback akan menganggapnya sebagai ikon Lucide).*

---

## 2. Penggunaan di Dashboard (React)

### Komponen `<IconPicker>`

Gunakan komponen ini untuk menggantikan elemen `<Input>` teks biasa pada form yang membutuhkan ikon.

**Props:**
- `label`: Teks label input (default: `"Ikon"`)
- `value`: State string ikon (berisi format `lucide:...` atau `url:...`)
- `onChange`: *Callback function* yang dipanggil saat ikon dipilih atau berhasil diunggah.

**Contoh Penggunaan:**
```tsx
import { IconPicker } from '@dashboard/Components/ui/icon-picker';

// Di dalam form:
<div className="space-y-1.5">
    <IconPicker
        label="Ikon Fasilitas"
        value={data.icon}
        onChange={(val) => setData('icon', val)}
    />
</div>
```

---

## 3. Penggunaan di Frontend Publik (Blade)

### Blade Component `<x-icon>`

Komponen Blade ini membaca format `lucide:` maupun `url:` dan melakukan konversi tag HTML secara otomatis.

- Jika nilai `lucide:IconName`, akan diubah ke: `<i data-lucide="icon-name" class="..."></i>`.
- Jika nilai `url:/path`, akan diubah ke: `<img src="/path" class="..." />`.

**Contoh Penggunaan:**
```blade
<div class="icon-container">
    <x-icon :icon="$item['icon']" class="w-6 h-6 text-primary" />
</div>
```

> **Catatan Penting:** Ikon Lucide di sisi Blade merender tag `<i>`. Pastikan script utama frontend memanggil inisialisasi `lucide.createIcons()` agar tag `<i>` tersebut dikonversi menjadi tag `<svg>`.

---

## 4. Alur Upload Ikon Kustom

Jika admin memilih tab **Upload Ikon** pada *IconPicker*, aplikasi akan melakukan unggahan HTTP POST (AJAX) ke endpoint khusus: `POST /icons/upload`.

**Mekanisme Backend (`IconUploadController`):**
- File SVG/PNG divalidasi (maksimal 2MB).
- Disimpan secara lokal di direktori `storage/app/public/icons`.
- **Tidak** dicampur ke dalam *Media Library* utama, guna memisahkan secara tegas antara aset artikel/galeri dengan aset kosmetik antarmuka UI (seperti ikon program).
- Mengembalikan response JSON yang berisi tautan *(URL)* menuju ikon tersebut.
