# 🍛 Restaurant Management API

Backend API yang tangguh untuk sistem manajemen restoran, dibangun menggunakan **Laravel 11** dan **Filament PHP**. Mendukung autentikasi OAuth, manajemen order realtime, dan penyimpanan object storage.

## 🚀 Fitur Utama

- **Authentication:** Login via Email & Google OAuth (Sanctum).
- **Role Management:** Admin Panel (Filament) & Customer App.
- **Product Management:** Upload gambar menu ke S3/MinIO.
- **Order System:** Checkout, Cart, dan Kitchen Display System.
- **Performance:** Rate Limiting & Asynchronous Email Queue (Redis/Database).
- **Testing:** 100% Code Coverage pada fitur krusial.

## 🛠️ Prasyarat Sistem

Pastikan Anda telah menginstall:
- PHP >= 8.2
- Composer
- MySQL
- Redis 
- Docker (Untuk menjalankan MinIO)
- xampp

## 📦 Panduan Instalasi

1. **Clone Repository**

    git clone https://github.com/aawzyy/restaurant-api.git
    cd restaurant-api

2. **Install Dependencies**
    composer install

3. **Setup Environment Variables Duplikat file .env.example menjadi .env**
    cp .env.example .env

4. **Generate App Key**
    php artisan key:generate

## ⚙️ Konfigurasi Environment (.env)

Buka file .env dan sesuaikan konfigurasi berikut:

1. **Database (MySQL)**
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=restaurant_api
    DB_USERNAME=root
    DB_PASSWORD=

2. **Google OAuth (Login)**
(Dapatkan credential dari Google Cloud Console.)

    GOOGLE_CLIENT_ID=your-client-id.apps.googleusercontent.com
    GOOGLE_CLIENT_SECRET=your-client-secret
    GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback

3. **MinIO (Object Storage)**

    FILESYSTEM_DISK=s3
    AWS_ACCESS_KEY_ID=minioadmin
    AWS_SECRET_ACCESS_KEY=minioadmin
    AWS_DEFAULT_REGION=us-east-1
    AWS_BUCKET=restaurant-bucket
    AWS_USE_PATH_STYLE_ENDPOINT=true
    AWS_ENDPOINT=http://127.0.0.1:9000

4. **Queue & Cache**
    QUEUE_CONNECTION=database
    CACHE_STORE=redis   

5. **Email 2FA**
    MAIL_MAILER=smtp
    MAIL_HOST=smtp.gmail.com
    MAIL_PORT=465
    MAIL_USERNAME=your_email
    MAIL_PASSWORD=your_app_password
    MAIL_ENCRYPTION=ssl
    MAIL_FROM_ADDRESS="your_email"
    MAIL_FROM_NAME="${APP_NAME}"
    
## 🗄️ Setup Database & MinIO

1. **Setup MinIO (Via Docker)**
    cd docker
    docker compose up -d
    (
        Buka http://localhost:9001 di browser, 
        login, dan buat Bucket bernama restaurant-bucket
        ubah access bucket menjadi read only 
            docker exec -it minio_server sh
            mc alias set local http://localhost:9000 minioadmin minioadmin
            mc anonymous set download local/restaurant-bucket
    )

2. **Jalankan Migrasi & Seeder**
    php artisan migrate --seed

3. **Link Storage**
    php artisan storage:link

## 🏃‍♂️ Menjalankan Aplikasi

1. **Jalankan Server Laravel**
(pastikan xampp telah berjalan)

    php artisan serve --host=0.0.0.0 --port=8000

2. **Jalankan Queue Worker (Untuk Email OTP)**
    php artisan queue:work

## ✅ Menjalankan Testing

    php artisan tes 

## ⚙️ Dashboard admin 
    http://localhost:8000/admin

**Dibuat oleh Muhammad Fauzi Osama**