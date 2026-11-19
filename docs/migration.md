# Migration — Video Access System (Laravel + Breeze)

## Skema 5 tabel

| Tabel | Kolom utama | Foreign key |
|---|---|---|
| `roles` | id, role_name (unique) | - |
| `users` | id, name, email, password, **role_id** | role_id → roles.id |
| `videos` | id, judul, keterangan, file_path | - |
| `access_requests` | id, user_id, video_id, durasi_diminta (nullable), status | user_id → users.id, video_id → videos.id |
| `access_grants` | id, access_request_id (unique), approved_by, tanggal_mulai, tanggal_selesai | access_request_id → access_requests.id, approved_by → users.id |

Catatan: status "expired" tidak disimpan. Cukup dicek dari `tanggal_selesai < now()`.

---

## Urutan pembuatan file migration

Urutan penting karena foreign key. Jalankan satu per satu, **jangan sekaligus**, supaya timestamp di nama file berurutan:

```bash
php artisan make:migration create_roles_table
php artisan make:migration add_role_id_to_users_table --table=users
php artisan make:migration create_videos_table
php artisan make:migration create_access_requests_table
php artisan make:migration create_access_grants_table
```

Cek di `database/migrations/` bahwa urutan timestamp-nya: roles → add_role_id_to_users → videos → access_requests → access_grants.
Kalau dua file punya timestamp yang sama (dibuat di detik yang sama), Laravel mengurutkan berdasarkan nama, jadi `access_grants` bisa terbaca lebih dulu dari `access_requests`. Kalau itu terjadi, rename salah satu file.

---

## 1. create_roles_table

```php
public function up(): void
{
    Schema::create('roles', function (Blueprint $table) {
        $table->id();
        $table->string('role_name')->unique();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('roles');
}
```

## 2. add_role_id_to_users_table

```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->foreignId('role_id')->after('id')->constrained('roles');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropConstrainedForeignId('role_id');
    });
}
```

> Kolom ini NOT NULL. Kalau tabel `users` sudah berisi data, migration akan gagal. Pakai `php artisan migrate:fresh` selama masih tahap pengembangan.

## 3. create_videos_table

```php
public function up(): void
{
    Schema::create('videos', function (Blueprint $table) {
        $table->id();
        $table->string('judul');
        $table->text('keterangan')->nullable();
        $table->string('file_path');
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('videos');
}
```

## 4. create_access_requests_table

```php
public function up(): void
{
    Schema::create('access_requests', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->foreignId('video_id')->constrained()->cascadeOnDelete();
        $table->unsignedSmallInteger('durasi_diminta')->nullable(); // dalam hari, opsional
        $table->string('status', 20)->default('pending'); // pending | approved | rejected
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('access_requests');
}
```

## 5. create_access_grants_table

```php
public function up(): void
{
    Schema::create('access_grants', function (Blueprint $table) {
        $table->id();
        $table->foreignId('access_request_id')->unique()->constrained()->cascadeOnDelete();
        $table->foreignId('approved_by')->constrained('users');
        $table->dateTime('tanggal_mulai');
        $table->dateTime('tanggal_selesai');
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('access_grants');
}
```

---

## Penjelasan singkat

- `foreignId('x')->constrained()` membuat kolom **sekaligus** foreign key. Nama tabel ditebak dari nama kolom (`user_id` → `users`).
- `approved_by` bukan nama tabel, jadi harus eksplisit: `constrained('users')`.
- `cascadeOnDelete()` supaya admin bisa menghapus customer atau video tanpa error foreign key.
- `approved_by` sengaja tanpa cascade agar riwayat persetujuan tidak hilang jika admin dihapus.
- `dateTime` (bukan `date`) supaya jam ikut tercatat dan akses bisa berakhir di jam yang tepat.

---

## Seeder (isi data awal)

Buat dengan `php artisan make:seeder RoleSeeder` dan `php artisan make:seeder AdminUserSeeder`.

```php
// RoleSeeder
Role::create(['role_name' => 'admin']);
Role::create(['role_name' => 'customer']);
```

```php
// AdminUserSeeder
$admin = Role::where('role_name', 'admin')->first();

User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => Hash::make('password'),
    'role_id' => $admin->id,
]);
```

Panggil keduanya (RoleSeeder lebih dulu) dari `DatabaseSeeder`:

```php
$this->call([
    RoleSeeder::class,
    AdminUserSeeder::class,
]);
```

Jalankan:

```bash
php artisan migrate
php artisan db:seed
```

Kalau ada yang salah dan ingin mengulang dari nol (menghapus semua data):

```bash
php artisan migrate:fresh --seed
```

---

## Checklist sebelum `php artisan migrate`

- [ ] `.env` sudah `DB_CONNECTION=mysql`, `DB_HOST` dan `DB_PORT` tidak ter-comment
- [ ] Database sudah dibuat di MySQL (HeidiSQL) dengan nama yang sama seperti `DB_DATABASE`
- [ ] MySQL Laragon menyala
- [ ] Urutan timestamp file migration sudah benar
- [ ] Model `Role` dibuat (`php artisan make:model Role`) sebelum seeder dijalankan
