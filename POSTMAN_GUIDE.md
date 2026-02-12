# Postman Collection - Task Manager API

## Fájlok

1. **TaskManager_API.postman_collection.json** - Teljes API collection az összes végponttal
2. **TaskManager_Local.postman_environment.json** - Environment változók (local development)

---

## Importálás Postman-be

### 1. Collection importálása
1. Nyisd meg a Postman-t
2. Kattints az **Import** gombra (bal felső sarokban)
3. Válaszd ki a `TaskManager_API.postman_collection.json` fájlt
4. Kattints az **Import** gombra

### 2. Environment importálása
1. Kattints az **Import** gombra
2. Válaszd ki a `TaskManager_Local.postman_environment.json` fájlt
3. Kattints az **Import** gombra
4. Válaszd ki a **Task Manager - Local** environment-et a jobb felső sarokban

---

## Használat

### 1. Bejelentkezés

#### Admin bejelentkezés
1. Futtasd a **Public > Login** endpoint-ot
2. Body:
   ```json
   {
       "email": "admin@taskmanager.hu",
       "password": "admin123"
   }
   ```
3. A token automatikusan mentésre kerül az `auth_token` változóba

#### Normál user bejelentkezés
1. Futtasd a **Public > Login as Regular User** endpoint-ot
2. Használj egy létező user email címet (seed-elt)
3. Jelszó: `Jelszo12`

### 2. Token automatikus kezelése

A collection automatikusan kezeli a token-t:
- **Login** után automatikusan menti az `auth_token` environment változóba
- Minden authentikált endpoint automatikusan használja ezt a tokent
- Nincs szükség manuális token másolásra!

### 3. Endpoint-ok használata

#### Public endpoints (nincs auth)
- ✅ Ping Test
- ✅ Register
- ✅ Login

#### Authenticated endpoints (Bearer token szükséges)
- ✅ Logout
- ✅ Get Profile
- ✅ Get My Tasks
- ✅ Update Task Status

#### Admin endpoints (Bearer token + admin jog szükséges)
- ✅ User Management (CRUD)
- ✅ Task Management (CRUD)
- ✅ Assignment Management (CRUD)

---

## Collection struktúra

```
📁 Task Manager API
├─ 📁 Public
│  ├─ Ping Test
│  ├─ Register
│  ├─ Login
│  └─ Login as Regular User
├─ 📁 Auth
│  ├─ Logout
│  └─ Get Profile
├─ 📁 User - My Tasks
│  ├─ Get My Tasks
│  └─ Update Task Status
├─ 📁 Admin - Users
│  ├─ List All Users
│  ├─ Create User
│  ├─ Get User
│  ├─ Update User
│  ├─ Delete User (Soft)
│  └─ Get User Assignments
├─ 📁 Admin - Tasks
│  ├─ List All Tasks
│  ├─ Create Task
│  ├─ Get Task
│  ├─ Update Task
│  ├─ Delete Task (Soft)
│  └─ Get Task Assignments
└─ 📁 Admin - Assignments
   ├─ List All Assignments
   ├─ Create Assignment
   ├─ Get Assignment
   ├─ Update Assignment
   └─ Delete Assignment
```

---

## Environment változók

| Változó | Érték | Leírás |
|---------|-------|--------|
| `base_url` | `http://localhost` | API base URL |
| `auth_token` | (auto) | Bearer token (automatikusan beállítva login után) |
| `admin_email` | `admin@taskmanager.hu` | Admin email cím |
| `admin_password` | `admin123` | Admin jelszó |
| `user_password` | `Jelszo12` | Normál user-ek jelszava |

---

## Tesztelési workflow

### 1. Alapvető működés tesztelése
```
1. Ping Test → Ellenőrzi, hogy az API fut
2. Login (Admin) → Bejelentkezés admin-ként
3. Get Profile → Saját adatok lekérése
4. Logout → Kijelentkezés
```

### 2. User funkcionalitás tesztelése
```
1. Login as Regular User → Bejelentkezés user-ként
2. Get My Tasks → Saját feladatok lekérése
3. Update Task Status → Feladat státuszának módosítása
4. Get Profile → Profil megtekintése
```

### 3. Admin funkcionalitás tesztelése
```
1. Login (Admin) → Bejelentkezés admin-ként
2. List All Users → Összes user listázása
3. Create Task → Új feladat létrehozása
4. Create Assignment → Feladat hozzárendelése user-hez
5. Get Task Assignments → Feladat hozzárendeléseinek lekérése
```

---

## Gyakori hibák és megoldások

### 401 Unauthenticated
**Probléma:** Nincs érvényes token
**Megoldás:** 
1. Futtasd újra a Login endpoint-ot
2. Ellenőrizd, hogy a "Task Manager - Local" environment aktív-e

### 403 Unauthorized
**Probléma:** Nincs admin jogosultság
**Megoldás:** Jelentkezz be admin user-rel (`admin@taskmanager.hu`)

### 404 Not Found
**Probléma:** A resource ID nem létezik
**Megoldás:** Ellenőrizd az ID-t az URL-ben (pl. `/api/admin/users/1`)

### 422 Validation Error
**Probléma:** Hibás request body
**Megoldás:** Ellenőrizd a kötelező mezőket és a formátumot

---

## Tips & Tricks

### 1. ID-k dinamikus kezelése
Az endpoint URL-ekben változtasd meg az ID-kat (pl. `/users/1` → `/users/2`)

### 2. Environment váltás
Ha van production környezeted, duplázd meg az environment-et és változtasd meg a `base_url`-t

### 3. Pre-request Scripts
A Login endpoint-ok automatikusan mentik a tokent, de saját script-eket is adhatsz hozzá

### 4. Tests tab
Minden endpoint válaszát tesztelheted a Tests tab-ban

### 5. Bulk testing
Használd a Collection Runner-t az összes endpoint egyszerre való teszteléséhez

---

## Példa válaszok

### Login Success
```json
{
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "Admin",
    "email": "admin@taskmanager.hu",
    "is_admin": true
  },
  "access_token": "1|xyz...",
  "token_type": "Bearer"
}
```

### Get My Tasks
```json
[
  {
    "id": 1,
    "title": "Task Title",
    "description": "Description",
    "priority": "high",
    "due_date": "2026-03-15",
    "status": "in_progress",
    "taskAssignments": [...]
  }
]
```

### Validation Error
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password field is required."]
  }
}
```

---

## További információk

- **API Dokumentáció:** Lásd `API_DOCUMENTATION.md`
- **Test Credentials:**
  - Admin: `admin@taskmanager.hu` / `admin123`
  - Regular users: bármely seed-elt email / `Jelszo12`

---

## Support

Ha problémád van a collection-nel:
1. Ellenőrizd, hogy az API fut-e (`php artisan serve`)
2. Ellenőrizd a `base_url` környezeti változót
3. Futtasd le újra a migrációkat és seedeket: `php artisan migrate:fresh --seed`
