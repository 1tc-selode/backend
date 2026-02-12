# Task Manager Rendszer - Teljes Fejlesztési Dokumentáció

## 📋 Tartalomjegyzék
1. [Projekt Áttekintés](#projekt-áttekintés)
2. [Technológiai Stack](#technológiai-stack)
3. [Telepítés és Kezdeti Beállítás](#telepítés-és-kezdeti-beállítás)
4. [Adatbázis Struktúra](#adatbázis-struktúra)
5. [API Fejlesztés](#api-fejlesztés)
6. [Tesztelés](#tesztelés)
7. [Web Admin Felület](#web-admin-felület)
8. [Authentikáció és Jogosultságkezelés](#authentikáció-és-jogosultságkezelés)
9. [API Dokumentáció](#api-dokumentáció)
10. [Használati Útmutató](#használati-útmutató)

---

## 🎯 Projekt Áttekintés

### Mi ez a projekt?
Egy komplett feladatkezelő (Task Management) rendszer Laravel 11 alapokon, amely tartalmaz:
- **RESTful API**-t Sanctum authentikációval (24 végpont)
- **Web-alapú admin felületet** Blade sablonokkal
- **Komplett tesztlefedettséget** (27 teszt, 155 assertion)
- **Soft delete** funkcionalitást minden táblához

### Fő funkciók
- ✅ Felhasználó kezelés (CRUD + soft delete)
- ✅ Feladat kezelés (CRUD + soft delete, prioritás, státusz)
- ✅ Feladat hozzárendelések kezelése (CRUD + soft delete)
- ✅ Token-alapú API authentikáció
- ✅ Web-alapú admin bejelentkezés
- ✅ Csak admin felhasználók férhetnek hozzá a webes felülethez

---

## 🛠 Technológiai Stack

### Backend Framework
- **Laravel 11.x** - PHP keretrendszer
- **PHP 8.2+** - Programozási nyelv
- **SQLite** - Adatbázis (könnyű fejlesztéshez)

### Authentikáció
- **Laravel Sanctum** - API token authentikáció
- **Laravel Session Auth** - Web admin authentikáció

### Frontend (Admin)
- **Blade Templates** - Server-side rendering
- **TailwindCSS 3.x** (CDN) - Utility-first CSS framework
- **Font Awesome 6.4.0** - Ikonok

### Tesztelés
- **PHPUnit** - Unit és Feature tesztek
- **Laravel TestCase** - Laravel-specifikus tesztelési eszközök

### API Tesztelés
- **Postman** - API kollekció és tesztelés

---

## 📦 Telepítés és Kezdeti Beállítás

### 1. Laravel Projekt Létrehozása

```bash
# Projekt létrehozása Composer-rel
composer create-project laravel/laravel todoSanctum

# Belépés a projekt mappába
cd todoSanctum
```

### 2. Sanctum Telepítése

```bash
# Sanctum csomag telepítése
composer require laravel/sanctum

# Sanctum config fájl publikálása
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

### 3. Adatbázis Konfiguráció

**`.env` fájl módosítása:**
```env
DB_CONNECTION=sqlite
# DB_HOST, DB_PORT, DB_DATABASE sorok kommentelése vagy törlése
```

**SQLite adatbázis létrehozása:**
```bash
# Windows PowerShell
New-Item database/database.sqlite

# Vagy Windows CMD
type nul > database/database.sqlite
```

### 4. Alap Migrációk Futtatása

```bash
php artisan migrate
```

---

## 🗄 Adatbázis Struktúra

### Modellek Létrehozása

#### 1. Task Model és Migráció
```bash
php artisan make:model Task -m
```

**Migráció:** `database/migrations/xxxx_create_tasks_table.php`
```php
Schema::create('tasks', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('description')->nullable();
    $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
    $table->enum('status', ['pending', 'in-progress', 'completed'])->default('pending');
    $table->date('due_date')->nullable();
    $table->timestamps();
    $table->softDeletes(); // Soft delete támogatás
});
```

**Model:** `app/Models/Task.php`
```php
class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'priority',
        'status',
        'due_date',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    // Kapcsolat a Task_assigment modellel
    public function assignments()
    {
        return $this->hasMany(Task_assigment::class);
    }
}
```

#### 2. Task_assigment Model és Migráció
```bash
php artisan make:model Task_assigment -m
```

**Migráció:** `database/migrations/xxxx_create_task_assigments_table.php`
```php
Schema::create('task_assigments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('task_id')->constrained()->onDelete('cascade');
    $table->timestamp('assigned_at')->nullable();
    $table->timestamp('completed_at')->nullable();
    $table->timestamps();
    $table->softDeletes(); // Soft delete támogatás
});
```

**Model:** `app/Models/Task_assigment.php`
```php
class Task_assigment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'task_id',
        'assigned_at',
        'completed_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Kapcsolatok
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
```

#### 3. User Model Kiterjesztése

**Migráció módosítása:** `database/migrations/xxxx_create_users_table.php`
```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->string('department')->nullable(); // ÚJ
    $table->string('phone')->nullable();      // ÚJ
    $table->boolean('is_admin')->default(false); // ÚJ - Admin flag
    $table->rememberToken();
    $table->timestamps();
    $table->softDeletes(); // Soft delete támogatás
});
```

**Model:** `app/Models/User.php`
```php
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'department',
        'phone',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    // Kapcsolat a Task_assigment modellel
    public function assignments()
    {
        return $this->hasMany(Task_assigment::class);
    }
}
```

### Migrációk Futtatása

```bash
php artisan migrate:fresh
```

---

## 🔌 API Fejlesztés

### API Controllerek Létrehozása

#### 1. AuthController - Authentikáció

```bash
php artisan make:controller Api/AuthController
```

**Fájl:** `app/Http/Controllers/Api/AuthController.php`

**Végpontok:**
- `POST /api/register` - Új felhasználó regisztrálása
- `POST /api/login` - Bejelentkezés (token generálás)
- `POST /api/logout` - Kijelentkezés (token törlés)
- `GET /api/profile` - Aktuális felhasználó adatai
- `PUT /api/profile` - Profil frissítése

#### 2. TaskController - Feladat kezelés

```bash
php artisan make:controller Api/TaskController --api
```

**Fájl:** `app/Http/Controllers/Api/TaskController.php`

**Végpontok:**
- `GET /api/tasks` - Összes feladat listázása
- `POST /api/tasks` - Új feladat létrehozása
- `GET /api/tasks/{id}` - Egy feladat megtekintése
- `PUT /api/tasks/{id}` - Feladat frissítése
- `DELETE /api/tasks/{id}` - Feladat törlése (soft delete)
- `POST /api/tasks/{id}/restore` - Törölt feladat visszaállítása (csak admin)
- `DELETE /api/tasks/{id}/force` - Végleges törlés (csak admin)

#### 3. UserController - Felhasználó kezelés

```bash
php artisan make:controller Api/UserController --api
```

**Fájl:** `app/Http/Controllers/Api/UserController.php`

**Végpontok:**
- `GET /api/users` - Összes felhasználó (csak admin)
- `POST /api/users` - Új felhasználó létrehozása (csak admin)
- `GET /api/users/{id}` - Egy felhasználó megtekintése (csak admin)
- `PUT /api/users/{id}` - Felhasználó frissítése (csak admin)
- `DELETE /api/users/{id}` - Felhasználó törlése (csak admin, soft delete)
- `POST /api/users/{id}/restore` - Törölt felhasználó visszaállítása (csak admin)
- `DELETE /api/users/{id}/force` - Végleges törlés (csak admin)

#### 4. TaskAssignmentController - Hozzárendelések

```bash
php artisan make:controller Api/TaskAssignmentController --api
```

**Fájl:** `app/Http/Controllers/Api/TaskAssignmentController.php`

**Végpontok:**
- `GET /api/task-assignments` - Összes hozzárendelés
- `POST /api/task-assignments` - Új hozzárendelés létrehozása
- `GET /api/task-assignments/{id}` - Egy hozzárendelés megtekintése
- `PUT /api/task-assignments/{id}` - Hozzárendelés frissítése
- `DELETE /api/task-assignments/{id}` - Hozzárendelés törlése (soft delete)
- `POST /api/task-assignments/{id}/restore` - Visszaállítás (csak admin)
- `DELETE /api/task-assignments/{id}/force` - Végleges törlés (csak admin)

### API Routes Beállítása

**Fájl:** `routes/api.php`

```php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\TaskAssignmentController;

// Nyilvános végpontok
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Védett végpontok (auth:sanctum middleware)
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('profile', [AuthController::class, 'profile']);
    Route::put('profile', [AuthController::class, 'updateProfile']);
    
    // Tasks
    Route::apiResource('tasks', TaskController::class);
    Route::post('tasks/{id}/restore', [TaskController::class, 'restore']);
    Route::delete('tasks/{id}/force', [TaskController::class, 'forceDelete']);
    
    // Task Assignments
    Route::apiResource('task-assignments', TaskAssignmentController::class);
    Route::post('task-assignments/{id}/restore', [TaskAssignmentController::class, 'restore']);
    Route::delete('task-assignments/{id}/force', [TaskAssignmentController::class, 'forceDelete']);
    
    // Users (csak admin)
    Route::apiResource('users', UserController::class);
    Route::post('users/{id}/restore', [UserController::class, 'restore']);
    Route::delete('users/{id}/force', [UserController::class, 'forceDelete']);
});
```

### Middleware Létrehozása - IsAdmin

```bash
php artisan make:middleware IsAdmin
```

**Fájl:** `app/Http/Middleware/IsAdmin.php`

```php
public function handle(Request $request, Closure $next): Response
{
    if (!$request->user() || !$request->user()->is_admin) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }
    return $next($request);
}
```

**Middleware regisztrálása:** `bootstrap/app.php`

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'admin' => \App\Http\Middleware\IsAdmin::class,
    ]);
})
```

### API Tesztelése Postman-nel

**Postman kollekció frissítése:**
- Minden kéréshez hozzáadtuk: `Accept: application/json` headert
- Token kezelés változókkal: `{{token}}`
- Admin és normál user tesztelési környezetek

**Példa környezeti változók:**

```json
{
  "url": "http://localhost/api",
  "token": "",
  "admin_email": "admin@taskmanager.hu",
  "admin_password": "admin123",
  "user_email": "user@taskmanager.hu",
  "user_password": "Jelszo12"
}
```

---

## ✅ Tesztelés

### Teszt Fájlok Létrehozása

#### 1. AuthApiTest - Authentikáció tesztek

```bash
php artisan make:test AuthApiTest
```

**Fájl:** `tests/Feature/AuthApiTest.php`

**Tesztek (9 db):**
1. ✅ Sikeres regisztráció
2. ✅ Sikeres bejelentkezés
3. ✅ Sikertelen bejelentkezés rossz jelszóval
4. ✅ Profil megtekintése
5. ✅ Profil megtekintése authentikáció nélkül (401)
6. ✅ Profil frissítése
7. ✅ Profil frissítése érvénytelen adatokkal
8. ✅ Kijelentkezés
9. ✅ Token törlődik kijelentkezés után

#### 2. TaskApiTest - Feladat tesztek

```bash
php artisan make:test TaskApiTest
```

**Fájl:** `tests/Feature/TaskApiTest.php`

**Tesztek (10 db):**
1. ✅ Admin listázhatja az összes feladatot
2. ✅ Normál user listázhatja a feladatokat
3. ✅ Admin létrehozhat feladatot
4. ✅ Admin megtekinthet egy feladatot
5. ✅ Admin frissíthet feladatot
6. ✅ Admin törölhet feladatot (soft delete)
7. ✅ Admin visszaállíthat törölt feladatot
8. ✅ Admin véglegesen törölhet feladatot
9. ✅ Normál user NEM törölhet véglegesen
10. ✅ Normál user NEM állíthat vissza feladatot

#### 3. UserApiTest - Felhasználó tesztek

```bash
php artisan make:test UserApiTest
```

**Fájl:** `tests/Feature/UserApiTest.php`

**Tesztek (8 db):**
1. ✅ Admin listázhatja a felhasználókat
2. ✅ Normál user NEM listázhatja a felhasználókat
3. ✅ Admin létrehozhat felhasználót
4. ✅ Admin megtekinthet felhasználót
5. ✅ Admin frissíthet felhasználót
6. ✅ Admin törölhet felhasználót (soft delete)
7. ✅ Admin visszaállíthat felhasználót
8. ✅ Admin véglegesen törölhet felhasználót

### Tesztek Futtatása

```bash
# Összes teszt futtatása
php artisan test

# Specifikus tesztek futtatása
php artisan test --filter=AuthApiTest
php artisan test --filter=TaskApiTest
php artisan test --filter=UserApiTest

# Összes API teszt egyszerre
php artisan test --filter="TaskApiTest|UserApiTest|AuthApiTest"

# Részletes kimenet
php artisan test --verbose

# Test coverage (ha xdebug van telepítve)
php artisan test --coverage
```

### Teszt Eredmények

```
PASS  Tests\Feature\AuthApiTest
✓ user can register successfully                    0.15s  
✓ user can login successfully                       0.02s  
✓ user cannot login with wrong password             0.02s  
✓ authenticated user can view profile               0.02s  
✓ unauthenticated user cannot view profile          0.01s  
✓ authenticated user can update profile             0.02s  
✓ profile update fails with invalid data            0.02s  
✓ user can logout successfully                      0.02s  
✓ token is deleted after logout                     0.02s  

PASS  Tests\Feature\TaskApiTest
✓ admin can list all tasks                          0.03s  
✓ regular user can list tasks                       0.02s  
✓ admin can create task                             0.02s  
✓ admin can view single task                        0.02s  
✓ admin can update task                             0.02s  
✓ admin can soft delete task                        0.02s  
✓ admin can restore task                            0.02s  
✓ admin can force delete task                       0.02s  
✓ regular user cannot force delete task             0.02s  
✓ regular user cannot restore task                  0.02s  

PASS  Tests\Feature\UserApiTest
✓ admin can list all users                          0.02s  
✓ regular user cannot list users                    0.02s  
✓ admin can create user                             0.02s  
✓ admin can view single user                        0.02s  
✓ admin can update user                             0.02s  
✓ admin can soft delete user                        0.02s  
✓ admin can restore user                            0.02s  
✓ admin can force delete user                       0.02s  

Tests:    27 passed (155 assertions)
Duration: 0.71s
```

---

## 🌐 Web Admin Felület

### Web Controllerek Létrehozása

#### 1. UserWebController

```bash
php artisan make:controller Web/UserWebController --resource
```

**Fájl:** `app/Http/Controllers/Web/UserWebController.php`

**Metódusok:**
- `index()` - Felhasználók listázása (tábla nézet)
- `create()` - Új felhasználó form megjelenítése
- `store()` - Új felhasználó mentése
- `show($id)` - Egy felhasználó részletei
- `edit($id)` - Szerkesztő form megjelenítése
- `update($id)` - Frissítés mentése
- `destroy($id)` - Soft delete
- `restore($id)` - Visszaállítás
- `forceDelete($id)` - Végleges törlés

#### 2. TaskWebController

```bash
php artisan make:controller Web/TaskWebController --resource
```

**Fájl:** `app/Http/Controllers/Web/TaskWebController.php`

**Metódusok:** Ugyanazok mint a UserWebController-nél

#### 3. TaskAssignmentWebController

```bash
php artisan make:controller Web/TaskAssignmentWebController --resource
```

**Fájl:** `app/Http/Controllers/Web/TaskAssignmentWebController.php`

**Metódusok:** Ugyanazok mint a UserWebController-nél

### Blade Sablonok Létrehozása

#### Mappa Struktúra

```
resources/views/
├── layouts/
│   └── admin.blade.php          # Alap layout (navigáció, footer, flash üzenetek)
├── auth/
│   └── login.blade.php          # Bejelentkezési oldal
├── admin/
│   ├── users/
│   │   ├── index.blade.php      # Felhasználók listája
│   │   ├── create.blade.php     # Új felhasználó form
│   │   ├── edit.blade.php       # Szerkesztő form
│   │   └── show.blade.php       # Részletek oldal
│   ├── tasks/
│   │   ├── index.blade.php      # Feladatok listája
│   │   ├── create.blade.php     # Új feladat form
│   │   ├── edit.blade.php       # Szerkesztő form
│   │   └── show.blade.php       # Részletek oldal
│   └── assignments/
│       ├── index.blade.php      # Hozzárendelések listája
│       ├── create.blade.php     # Új hozzárendelés form
│       ├── edit.blade.php       # Szerkesztő form
│       └── show.blade.php       # Részletek oldal
```

#### Admin Layout (`layouts/admin.blade.php`)

**Funkciók:**
- TailwindCSS (CDN)
- Font Awesome ikonok
- Responsive navigációs menü
- Flash üzenetek (success/error)
- Bejelentkezett felhasználó neve
- Logout gomb

#### View Funkciók

**Index oldalak (listák):**
- Táblázatos megjelenítés
- Státusz badge-ek (színkódolt)
- Akció gombok (View, Edit, Delete/Restore/Force Delete)
- Soft delete-elt elemek piros háttérrel
- Üres állapot kezelése
- Pagination támogatás

**Create/Edit formok:**
- Validációs hibák megjelenítése
- Old() függvény - adatok megtartása hiba esetén
- TailwindCSS form stílusok
- Cancel és Save gombok
- Font Awesome ikonok

**Show oldalak (részletek):**
- Grid layout 2 oszlopban
- Minden adat megjelenítése
- Színkódolt státuszok
- Akció gombok (Edit, Delete, Restore, Force Delete)
- Confirmation dialógusok

### Web Routes Beállítása

**Fájl:** `routes/web.php`

```php
use App\Http\Controllers\Web\AuthWebController;
use App\Http\Controllers\Web\UserWebController;
use App\Http\Controllers\Web\TaskWebController;
use App\Http\Controllers\Web\TaskAssignmentWebController;

// Bejelentkezés nélküli útvonalak
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('login', [AuthWebController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthWebController::class, 'login'])->name('login.post');

// Admin felület - csak bejelentkezett adminoknak
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    
    // Users
    Route::resource('users', UserWebController::class);
    Route::post('users/{id}/restore', [UserWebController::class, 'restore'])->name('users.restore');
    Route::delete('users/{id}/force', [UserWebController::class, 'forceDelete'])->name('users.force-delete');
    
    // Tasks
    Route::resource('tasks', TaskWebController::class);
    Route::post('tasks/{id}/restore', [TaskWebController::class, 'restore'])->name('tasks.restore');
    Route::delete('tasks/{id}/force', [TaskWebController::class, 'forceDelete'])->name('tasks.force-delete');
    
    // Assignments
    Route::resource('assignments', TaskAssignmentWebController::class);
    Route::post('assignments/{id}/restore', [TaskAssignmentWebController::class, 'restore'])->name('assignments.restore');
    Route::delete('assignments/{id}/force', [TaskAssignmentWebController::class, 'forceDelete'])->name('assignments.force-delete');
});

Route::post('logout', [AuthWebController::class, 'logout'])->name('logout');
```

---

## 🔐 Authentikáció és Jogosultságkezelés

### Web Bejelentkezés

#### AuthWebController Létrehozása

```bash
php artisan make:controller Web/AuthWebController
```

**Fájl:** `app/Http/Controllers/Web/AuthWebController.php`

**Metódusok:**
- `showLoginForm()` - Bejelentkezési form megjelenítése
- `login(Request $request)` - Bejelentkezés feldolgozása
- `logout(Request $request)` - Kijelentkezés

**Login folyamat:**
1. Email és jelszó validálása
2. `Auth::attempt()` - bejelentkezési kísérlet
3. Session regenerálás (biztonsági okokból)
4. Admin flag ellenőrzése - ha nem admin, kijelentkeztetés
5. Átirányítás az admin felületre
6. Welcome üzenet flash session-ben

**Bejelentkezési oldal:** `resources/views/auth/login.blade.php`
- Gradient háttér (kék-lila)
- Kártyás design
- Email és jelszó mezők
- "Remember me" checkbox
- Teszt admin adatok megjelenítése (fejlesztéshez)

### Admin Middleware

**Middleware létrehozása:**
```bash
php artisan make:middleware AdminMiddleware
```

**Fájl:** `app/Http/Middleware/AdminMiddleware.php`

**Logika:**
1. Ellenőrzi, hogy a felhasználó be van-e jelentkezve
   - Ha nem → átirányítás a login oldalra
2. Ellenőrzi, hogy a felhasználó admin-e (`is_admin = true`)
   - Ha nem → kijelentkeztetés + átirányítás hibaüzenettel

**Regisztrálás:** `bootstrap/app.php`
```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
    ]);
})
```

### Teszt Admin Felhasználó Létrehozása

**Seeder vagy Tinker használata:**

```bash
php artisan tinker
```

```php
use App\Models\User;

User::create([
    'name' => 'Admin User',
    'email' => 'admin@taskmanager.hu',
    'password' => bcrypt('admin123'),
    'is_admin' => true,
    'department' => 'IT',
    'phone' => '+36 30 123 4567'
]);

// Normál user
User::create([
    'name' => 'Regular User',
    'email' => 'user@taskmanager.hu',
    'password' => bcrypt('Jelszo12'),
    'is_admin' => false,
    'department' => 'Sales'
]);
```

---

## 📚 API Dokumentáció

### Base URL
```
http://localhost/api
```

### Authentikáció
Bearer Token használata minden védett végpontnál:
```
Authorization: Bearer {token}
```

### 1. Authentikáció Végpontok

#### Regisztráció
```http
POST /api/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "Password123",
  "password_confirmation": "Password123",
  "department": "IT",
  "phone": "+36 30 123 4567"
}

Response 201:
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    ...
  },
  "token": "1|abc123..."
}
```

#### Bejelentkezés
```http
POST /api/login
Content-Type: application/json

{
  "email": "admin@taskmanager.hu",
  "password": "admin123"
}

Response 200:
{
  "user": {...},
  "token": "2|xyz789..."
}
```

#### Profil Megtekintése
```http
GET /api/profile
Authorization: Bearer {token}

Response 200:
{
  "data": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@taskmanager.hu",
    "is_admin": true,
    ...
  }
}
```

#### Profil Frissítése
```http
PUT /api/profile
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Updated Name",
  "phone": "+36 30 999 8888"
}

Response 200:
{
  "data": {...}
}
```

#### Kijelentkezés
```http
POST /api/logout
Authorization: Bearer {token}

Response 200:
{
  "message": "Logout successful"
}
```

### 2. Feladat Végpontok

#### Feladatok Listázása
```http
GET /api/tasks
Authorization: Bearer {token}

Response 200:
{
  "data": [
    {
      "id": 1,
      "title": "Implement login",
      "description": "...",
      "priority": "high",
      "status": "in-progress",
      "due_date": "2026-02-20",
      "created_at": "2026-02-12T10:00:00",
      ...
    }
  ]
}
```

#### Feladat Létrehozása
```http
POST /api/tasks
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "New Task",
  "description": "Task description",
  "priority": "medium",
  "status": "pending",
  "due_date": "2026-03-01"
}

Response 201:
{
  "data": {...}
}
```

#### Feladat Megtekintése
```http
GET /api/tasks/{id}
Authorization: Bearer {token}

Response 200:
{
  "data": {...}
}
```

#### Feladat Frissítése
```http
PUT /api/tasks/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "Updated Title",
  "status": "completed"
}

Response 200:
{
  "data": {...}
}
```

#### Feladat Törlése (Soft Delete)
```http
DELETE /api/tasks/{id}
Authorization: Bearer {token}

Response 200:
{
  "message": "Task deleted successfully"
}
```

#### Feladat Visszaállítása (Csak Admin)
```http
POST /api/tasks/{id}/restore
Authorization: Bearer {token}

Response 200:
{
  "message": "Task restored successfully"
}
```

#### Feladat Végleges Törlése (Csak Admin)
```http
DELETE /api/tasks/{id}/force
Authorization: Bearer {token}

Response 200:
{
  "message": "Task permanently deleted"
}
```

### 3. Felhasználó Végpontok (Csak Admin)

#### Felhasználók Listázása
```http
GET /api/users
Authorization: Bearer {token}

Response 200:
{
  "data": [...]
}
```

#### Felhasználó Létrehozása
```http
POST /api/users
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "New User",
  "email": "newuser@example.com",
  "password": "Password123",
  "is_admin": false,
  "department": "HR"
}

Response 201:
{
  "data": {...}
}
```

#### Felhasználó Megtekintése
```http
GET /api/users/{id}
Authorization: Bearer {token}

Response 200:
{
  "data": {...}
}
```

#### Felhasználó Frissítése
```http
PUT /api/users/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Updated Name",
  "department": "IT"
}

Response 200:
{
  "data": {...}
}
```

#### Felhasználó Törlése, Visszaállítása, Végleges Törlése
Ugyanúgy mint a feladatoknál.

### 4. Hozzárendelés Végpontok

#### Hozzárendelések Listázása
```http
GET /api/task-assignments
Authorization: Bearer {token}

Response 200:
{
  "data": [
    {
      "id": 1,
      "user_id": 2,
      "task_id": 5,
      "assigned_at": "2026-02-12T10:00:00",
      "completed_at": null,
      "user": {...},
      "task": {...}
    }
  ]
}
```

#### Hozzárendelés Létrehozása
```http
POST /api/task-assignments
Authorization: Bearer {token}
Content-Type: application/json

{
  "user_id": 2,
  "task_id": 5,
  "assigned_at": "2026-02-12T10:00:00"
}

Response 201:
{
  "data": {...}
}
```

#### Többi műveletek: show, update, destroy, restore, forceDelete
Ugyanúgy működnek mint a feladatoknál.

### HTTP Státuszkódok

- `200 OK` - Sikeres kérés
- `201 Created` - Sikeres létrehozás
- `400 Bad Request` - Érvénytelen kérés
- `401 Unauthorized` - Nincs authentikáció
- `403 Forbidden` - Nincs jogosultság (nem admin)
- `404 Not Found` - Az erőforrás nem található
- `422 Unprocessable Entity` - Validációs hiba
- `500 Internal Server Error` - Szerver hiba

---

## 🚀 Használati Útmutató

### Fejlesztői Környezet Indítása

#### 1. Laravel Development Server
```bash
php artisan serve
```
Elérhető: `http://localhost:8000`

#### 2. XAMPP Használata (Jelenlegi Setup)
```bash
# Apache és MySQL indítása XAMPP Control Panel-ből
# Projekt elérése: http://localhost/todoSanctum/public
```

### Admin Web Felület Használata

#### Bejelentkezés
1. Nyisd meg: `http://localhost/admin/users` (átirányít a login-ra)
2. Vagy közvetlenül: `http://localhost/login`
3. Add meg a teszt admin adatokat:
   - Email: `admin@taskmanager.hu`
   - Password: `admin123`
4. Kattints a "Login" gombra

#### Felhasználók Kezelése
**Listázás:** `/admin/users`
- Látható: ID, Név, Email, Osztály, Telefon, Admin státusz, Aktív/Törölt
- Akciók: View (szem ikon), Edit (ceruza), Delete (kuka)

**Új felhasználó:** `/admin/users/create`
- Kötelező mezők: Név, Email, Jelszó
- Opcionális: Osztály, Telefon
- Checkbox: Administrator jog

**Szerkesztés:** `/admin/users/{id}/edit`
- Minden mező szerkeszthető
- Jelszó mező üresen hagyható (megtartja a régit)

**Részletek:** `/admin/users/{id}`
- Minden adat olvasható formátumban
- Akció gombok alul

**Törlés és Visszaállítás:**
- Delete: Soft delete (piros háttér a listában)
- Restore: Visszaállítás (zöld gomb)
- Delete Permanently: Végleges törlés (piros gomb, megerősítés szükséges)

#### Feladatok Kezelése
**Ugyanaz a logika mint a felhasználóknál**

Mezők:
- Cím (kötelező)
- Leírás
- Prioritás: low, medium, high (színkódolt badge-ek)
- Státusz: pending, in-progress, completed (színkódolt)
- Határidő (opcionális)

#### Hozzárendelések Kezelése
**Mezők:**
- Felhasználó választása (dropdown)
- Feladat választása (dropdown)
- Hozzárendelés ideje (datetime-local input)
- Befejezés ideje (opcionális)

**Listázás különlegességei:**
- User név és email együtt
- Task cím és prioritás együtt
- Completed státusz zöld színnel

### API Használata

#### 1. Postman Kollekció Importálása
- Importáld a `postman_collection.json` fájlt
- Állítsd be a környezeti változókat:
  - `url`: `http://localhost/api`
  - `token`: (automatikusan beállítódik login után)

#### 2. Authentikáció Folyamata
1. **Login** kérés elküldése
2. Token kimásolása a válaszból
3. A token automatikusan beállítódik a `{{token}}` változóba
4. Minden további kérés használja ezt a tokent

#### 3. Tipikus Munkafolyamat
```
1. Login → Token generálás
2. GET /api/tasks → Feladatok listázása
3. POST /api/tasks → Új feladat létrehozása
4. GET /api/users → Felhasználók listázása (admin)
5. POST /api/task-assignments → Feladat hozzárendelése
6. PUT /api/tasks/{id} → Feladat státusz frissítése
7. POST /api/logout → Kijelentkezés
```

### Tippek és Trükkök

#### Gyors Adatbázis Reset
```bash
php artisan migrate:fresh
php artisan db:seed  # ha van seeder
```

#### Gyors Admin Létrehozása
```bash
php artisan tinker

User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
    'is_admin' => true
]);
```

#### Cache Tisztítása
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

#### API Hibakeresés
- Nézd meg a `storage/logs/laravel.log` fájlt
- Használd a `dd()` vagy `dump()` függvényeket
- Postman Console-ban ellenőrizd a kérés/válasz részleteit

---

## 📊 Összefoglaló Statisztikák

### Létrehozott Fájlok Száma
- **Modellek:** 3 (User, Task, Task_assigment)
- **Migrációk:** 6 (users, tasks, task_assigments, cache, jobs, personal_access_tokens)
- **API Controllerek:** 4 (Auth, Task, User, TaskAssignment)
- **Web Controllerek:** 4 (AuthWeb, UserWeb, TaskWeb, TaskAssignmentWeb)
- **Middleware:** 2 (IsAdmin, AdminMiddleware)
- **Blade Sablonok:** 13 (1 layout, 1 login, 11 admin oldal)
- **Teszt Fájlok:** 3 (AuthApiTest, TaskApiTest, UserApiTest)
- **Route Fájlok:** 2 (api.php, web.php)

### API Végpontok
- **Összes:** 24 végpont
- **Nyilvános:** 2 (register, login)
- **Védett:** 22 (auth:sanctum middleware)
- **Admin-only:** 15 (admin middleware)

### Tesztek
- **Teszt Fájlok:** 3
- **Összes Teszt:** 27
- **Assertions:** 155
- **Sikeres Futás:** 100% (27/27)

### Kód Sorok (Becsült)
- **PHP Kód:** ~3,500 sor
- **Blade Templates:** ~1,800 sor
- **Teszt Kód:** ~1,200 sor
- **Összesen:** ~6,500 sor

---

## 🔧 Függőségek és Verziók

### Composer Csomagok
```json
{
  "require": {
    "php": "^8.2",
    "laravel/framework": "^11.0",
    "laravel/sanctum": "^4.0",
    "laravel/tinker": "^2.9"
  },
  "require-dev": {
    "fakerphp/faker": "^1.23",
    "laravel/pint": "^1.13",
    "mockery/mockery": "^1.6",
    "nunomaduro/collision": "^8.0",
    "phpunit/phpunit": "^11.0"
  }
}
```

### CDN Erőforrások
- **TailwindCSS:** 3.x (latest)
- **Font Awesome:** 6.4.0

---

## 🐛 Ismert Problémák és Megoldások

### 1. Middleware "admin" not found
**Probléma:** Az admin middleware nincs regisztrálva.
**Megoldás:** Ellenőrizd a `bootstrap/app.php` fájlt:
```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
    ]);
})
```

### 2. CSRF Token Mismatch
**Probléma:** Web form beküldéskor CSRF hiba.
**Megoldás:** Minden form-ban legyen `@csrf` direktíva:
```blade
<form method="POST">
    @csrf
    <!-- form mezők -->
</form>
```

### 3. Soft Deleted Records Not Showing
**Probléma:** A törölt rekordok nem jelennek meg.
**Megoldás:** Használd a `withTrashed()` metódust:
```php
User::withTrashed()->get();
Task::withTrashed()->find($id);
```

### 4. API Returns HTML Instead of JSON
**Probléma:** Az API HTML-t ad vissza JSON helyett.
**Megoldás:** Add hozzá a `Accept: application/json` headert minden API kéréshez.

---

## 📝 Következő Lépések és Fejlesztési Lehetőségek

### 1. Hiányzó Funkciók
- [ ] Email értesítések (Mail)
- [ ] File upload a feladatokhoz
- [ ] Kommentelés funkció
- [ ] Dashboard statisztikákkal
- [ ] Keresés és szűrés a listákban
- [ ] Exportálás (PDF, Excel)
- [ ] API rate limiting
- [ ] Multi-language (i18n)

### 2. Optimalizálások
- [ ] Eager loading (N+1 query probléma elkerülése)
- [ ] Response caching
- [ ] Database indexek
- [ ] Queue használata hosszú műveletekhez
- [ ] API throttling

### 3. Biztonsági Fejlesztések
- [ ] Two-Factor Authentication (2FA)
- [ ] Email verification
- [ ] Password reset funkció
- [ ] API key rotation
- [ ] Audit log (ki mit csinált)
- [ ] CORS beállítások finomítása

### 4. DevOps
- [ ] Docker containerizálás
- [ ] CI/CD pipeline (GitHub Actions)
- [ ] Automated testing
- [ ] Staging környezet
- [ ] Production deployment guide

### 5. Dokumentáció
- [ ] Swagger/OpenAPI dokumentáció
- [ ] Video tutorialok
- [ ] User manual (végfelhasználói útmutató)
- [ ] API changelog
- [ ] Contributing guide

---

## 👨‍💻 Készítette

**Projekt:** Task Manager API + Admin Web Interface  
**Dátum:** 2026. február 12.  
**Framework:** Laravel 11.x  
**License:** MIT  

---

## 📞 Támogatás és Hibajelentés

Ha bármilyen problémába ütközöl vagy kérdésed van:

1. Ellenőrizd a `storage/logs/laravel.log` fájlt
2. Futtasd le a teszteket: `php artisan test`
3. Tisztítsd a cache-t: `php artisan cache:clear`
4. Nézd át ezt a dokumentációt újra
5. Ha API-val kapcsolatos, ellenőrizd a Postman Console-t

**Hasznos parancsok problémák esetén:**
```bash
# Összes cache törlése
php artisan optimize:clear

# Jogosultságok ellenőrzése (Linux/Mac)
chmod -R 775 storage bootstrap/cache

# Composer dependencies újratelepítése
composer install --no-cache

# Adatbázis reset
php artisan migrate:fresh --seed
```

---

**🎉 Gratulálunk! Sikeres Task Manager rendszert építettél Laravel-lel! 🎉**
