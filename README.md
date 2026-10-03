# TechJournal

Blog pribadi berbasis Laravel 11, Filament 3, Tailwind CSS, dan Vite.

## Menjalankan secara lokal

1. Salin `.env.example` menjadi `.env`.
2. Jalankan `composer install` dan `npm ci`.
3. Jalankan `php artisan key:generate`.
4. Untuk development lokal tanpa MySQL, setel `.env`:

   ```env
   APP_URL=http://localhost:8000
   DB_CONNECTION=sqlite
   DB_DATABASE=database/database.sqlite
   SESSION_DRIVER=file
   CACHE_STORE=file
   QUEUE_CONNECTION=sync
   ```

   Buat file SQLite jika belum ada, lalu jalankan migrasi dan seeder:

   ```powershell
   if (-not (Test-Path database\database.sqlite)) { New-Item -ItemType File database\database.sqlite }
   php artisan migrate --seed
   ```

5. Jalankan `php artisan serve` dan `npm run dev` di dua terminal terpisah; buka aplikasi di <http://localhost:8000>.

Seeder admin membuat `admin@myblog.com` sebagai admin pertama. Jika `ADMIN_PASSWORD` kosong, password acak yang kuat dibuat dan ditampilkan satu kali pada output perintah seeder. Simpan password tersebut dan ubah setelah login. Untuk menentukan password sendiri sebelum seeding, isi `ADMIN_NAME`, `ADMIN_EMAIL`, dan `ADMIN_PASSWORD` pada `.env`. Seeder tidak mengubah atau mereset akun yang sudah ada.

Panel Filament tersedia di `/admin`.

## Markdown, syntax highlighting, dan matematika

Konten artikel disimpan sebagai Markdown dan dikonversi oleh helper parser CommonMark Laravel `Illuminate\Support\Str::markdown()` saat halaman artikel dirender. Contoh fenced code block:

````markdown
```java
public class Main {
    public static void main(String[] args) {
        System.out.println("Hello, world!");
    }
}
```

Rata-rata linear: $T(n) = \frac{1}{n}\sum_{i=1}^{n}x_i$.

$$
T(n) = \frac{1}{n}\sum_{i=1}^{n}x_i
$$
````

Client membaca penanda bahasa `language-*` hasil parser, lalu Shiki menyorot Java, C/C++, JavaScript/TypeScript, PHP, SQL, Python, Bash, dan bahasa lain yang terdaftar di `resources/js/app.js` dengan tema `github-dark`. KaTeX auto-render membaca ekspresi `$...$`, `$$...$$`, `\(...\)`, dan `\[...\]`. Parser Markdown tetap berjalan di server; npm hanya diperlukan untuk membangun/menyajikan highlighter dan renderer matematika. Setelah menambah bahasa, tambahkan grammar tersebut ke daftar `languages` di `resources/js/app.js`, lalu jalankan `npm run build`.

Artikel dipercaya berasal dari admin panel. Jika Markdown kelak dapat ditulis pengguna yang tidak dipercaya, konfigurasikan parser CommonMark agar menghapus raw HTML dan unsafe links sebelum merender HTML dengan `{!! !!}`.

## Menjalankan dengan Docker

Prasyarat: Docker Desktop dan Docker Compose v2.

Di PowerShell, dari folder proyek:

```powershell
Copy-Item .env.example .env
```

Sebelum mulai, atur `APP_URL=http://localhost:8000` dan kredensial `DOCKER_DB_*` di `.env`. Nilai database bawaan hanya untuk development lokal; jangan pakai untuk deployment publik.

```powershell
docker compose up --build -d
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

Baris `migrate --seed` menjalankan seeder admin. Salin password acak dari output terminal bila `ADMIN_PASSWORD` dibiarkan kosong. Buka blog di <http://localhost:8000>, panel admin di <http://localhost:8000/admin>, dan server Vite tersedia di port `5173` untuk hot reload.

Perintah harian:

```powershell
docker compose logs -f app vite mysql
docker compose down
```

`docker compose down` mempertahankan data database. Untuk menghapus database lokal beserta seluruh isinya secara permanen, jalankan `docker compose down -v`.

## CSS Filament tidak tampil

Filament 3 menyajikan stylesheet dan skrip panel sebagai aset package; asetnya tidak berasal dari `resources/css/app.css` atau Vite. Penyebab umum tampilan login tanpa CSS adalah URL aset yang salah (misalnya `APP_URL` menunjuk host lain), aset package yang belum dipublikasikan atau sudah kedaluwarsa setelah upgrade, cache konfigurasi, atau akses web server/proxy ke path aset yang gagal. Periksa tab Network browser untuk respons 404/403 dan pastikan domain/HTTPS serta izin baca pada `public/` sesuai alamat yang sedang dipakai.

Perbaikan dasar dari root proyek:

```powershell
# Sesuaikan .env dengan alamat yang benar-benar dibuka di browser.
# Contoh lokal: APP_URL=http://localhost:8000
php artisan optimize:clear
php artisan filament:assets
```

Jika upload storage yang rusak, buat symlink publik dengan `php artisan storage:link`. Untuk Docker, jalankan perintah Artisan melalui kontainer aplikasi, misalnya:

```powershell
docker compose exec app php artisan optimize:clear
docker compose exec app php artisan filament:assets
```

Jika aset Filament masih gagal, periksa URL stylesheet yang gagal di Network dan log Apache/Laravel; `APP_URL` yang sesuai saja tidak memperbaiki 404 ketika file aset tidak ada atau web server tidak menyajikan direktori `public/`.
