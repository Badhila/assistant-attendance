## System requirement
1. PHP 8.2
1. Composer 2+

## Quick setup
### 1. Install projek
via SSH
```sh
git clone git@github.com:presidenwashil/assistant-attendance.git "assistant-attendance"
```

via HTTPS
```sh
git clone https://github.com/presidenwashil/assistant-attendance.git "assistant-attendance"
```

Kemudian masuk ke directory dan install dependency.
```sh
cd "assistant-attendance"
composer install
```

### 2. Setup environment
Copy file .env.example ke .env

Linux/Mac OS
```sh
cp .env.example .env
```

Windows (CMD)
```bat
copy .env.example .env
```

Windows (Powershell)
```powershell
Copy-Item .env.example -Destination .env
```

Kemudian inisiasi encrypt key
```sh
php artisan key:generate
```

### 3. Migrasi database
Jalankan perintah berikut untuk melakukan migrasi database, pastikan nama database sudah benar dan sesuai.
```sh
php artisan migrate
```

### 4. Buat user
Jalankan perintah berikut untuk membuat user admin agar dapat login sebagai admin
```sh
php artisan make:filament-user
```

### 5. Jalankan server
```sh
php artisan serve
```