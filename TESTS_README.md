# API Tests

Ez a dokumentum bemutatja a Task Manager API automatizált tesztjeit.

## Tesztek futtatása

```bash
# Összes teszt futtatása
php artisan test

# Csak az API tesztek futtatása
php artisan test --filter="TaskApiTest|UserApiTest|AuthApiTest"

# Egy konkrét teszt osztály futtatása
php artisan test --filter=TaskApiTest
php artisan test --filter=UserApiTest
php artisan test --filter=AuthApiTest
```

## Teszt lefedettség

### 1. Authentication Tests (`AuthApiTest`)

**✅ user_can_register**
- Teszt: Új felhasználó regisztrálása
- Ellenőrzi: Sikeres regisztráció, access token generálás, adatbázis rekord

**✅ registration_fails_with_duplicate_email**
- Teszt: Duplikált email címmel való regisztráció
- Ellenőrzi: 422 Validation Error, megfelelő hibaüzenet

**✅ registration_fails_with_password_mismatch**
- Teszt: Eltérő jelszavak regisztrációnál
- Ellenőrzi: 422 Validation Error a password mezőn

**✅ user_can_login_with_valid_credentials**
- Teszt: Bejelentkezés helyes adatokkal
- Ellenőrzi: 200 OK, user adatok, access token

**✅ login_fails_with_invalid_credentials**
- Teszt: Bejelentkezés hibás jelszóval
- Ellenőrzi: 422 Error, hibaüzenet

**✅ login_deletes_old_tokens**
- Teszt: Bejelentkezéskor törlődnek a régi tokenek
- Ellenőrzi: Csak 1 aktív token marad a bejelentkezés után

**✅ authenticated_user_can_logout**
- Teszt: Kijelentkezés működik
- Ellenőrzi: Token törlődik az adatbázisból

**✅ authenticated_user_can_get_their_info**
- Teszt: Saját profil lekérése
- Ellenőrzi: GET /api/profile visszaadja a user adatokat

**✅ ping_endpoint_works_without_authentication**
- Teszt: Ping endpoint elérhető autentikáció nélkül
- Ellenőrzi: API működik, válasz struktúra helyes

---

### 2. Task Management Tests (`TaskApiTest`)

**Admin jogosultságok:**

**✅ admin_can_view_all_tasks**
- Teszt: Admin listázhatja az összes task-ot
- Ellenőrzi: GET /api/admin/tasks, pagination, task struktúra

**✅ admin_can_create_task**
- Teszt: Admin létrehozhat új task-ot
- Ellenőrzi: POST /api/admin/tasks, adatbázis rekord, válasz adatok

**✅ admin_can_delete_task**
- Teszt: Admin törölhet task-ot (soft delete)
- Ellenőrzi: DELETE /api/admin/tasks/{id}, soft delete működik

**Nem-admin felhasználók:**

**✅ non_admin_cannot_view_all_tasks**
- Teszt: Nem-admin NEM érheti el az admin endpointot
- Ellenőrzi: 403 Forbidden, megfelelő hibaüzenet

**✅ non_admin_cannot_create_task**
- Teszt: Nem-admin NEM hozhat létre task-ot
- Ellenőrzi: 403 Forbidden, adatbázis nem változik

**Felhasználói funkciók:**

**✅ user_can_view_their_own_tasks**
- Teszt: User látja a saját hozzárendelt task-jait
- Ellenőrzi: GET /api/my-tasks, csak hozzárendelt task-ok jelennek meg

**✅ user_can_update_their_assigned_task_status**
- Teszt: User frissítheti a hozzárendelt task státuszát
- Ellenőrzi: PATCH /api/tasks/{id}/status, státusz változás

**✅ user_cannot_update_unassigned_task_status**
- Teszt: User NEM frissíthet nem hozzárendelt task-ot
- Ellenőrzi: 403 Forbidden, státusz nem változik

**Autentikáció:**

**✅ unauthenticated_user_cannot_access_tasks**
- Teszt: Nem bejelentkezett user nem érheti el a task-okat
- Ellenőrzi: 401 Unauthenticated

**Validáció:**

**✅ task_validation_fails_with_invalid_data**
- Teszt: Érvénytelen adatok esetén validációs hiba
- Ellenőrzi: 422 Validation Error, megfelelő mezők hibásak

---

### 3. User Management Tests (`UserApiTest`)

**Admin jogosultságok:**

**✅ admin_can_view_all_users**
- Teszt: Admin listázhatja az összes felhasználót
- Ellenőrzi: GET /api/admin/users, pagination, user struktúra

**✅ admin_can_create_new_user**
- Teszt: Admin létrehozhat új felhasználót
- Ellenőrzi: POST /api/admin/users, adatbázis rekord

**✅ admin_can_update_user**
- Teszt: Admin frissíthet felhasználót
- Ellenőrzi: PUT /api/admin/users/{id}, adatok változnak

**✅ admin_can_delete_user**
- Teszt: Admin törölhet felhasználót (soft delete)
- Ellenőrzi: DELETE /api/admin/users/{id}, soft delete

**Nem-admin felhasználók:**

**✅ non_admin_cannot_view_all_users**
- Teszt: Nem-admin NEM listázhatja a felhasználókat
- Ellenőrzi: 403 Forbidden

**✅ non_admin_cannot_update_other_users**
- Teszt: Nem-admin NEM módosíthat más felhasználókat
- Ellenőrzi: 403 Forbidden, adatok nem változnak

**Profil kezelés:**

**✅ authenticated_user_can_view_own_profile**
- Teszt: User lekérheti a saját profilját
- Ellenőrzi: GET /api/profile, saját adatok

**✅ user_can_update_own_profile**
- Teszt: User frissítheti a saját profilját
- Ellenőrzi: PUT /api/profile, adatok változnak

---

## Teszt eredmények összefoglalása

```
✅ AuthApiTest:     9 teszt - mind átment
✅ TaskApiTest:    10 teszt - mind átment
✅ UserApiTest:     8 teszt - mind átment

Összesen: 27 teszt, 155 assertion
```

## Teszt struktúra

Minden teszt a következő mintát követi:

1. **Arrange** (Előkészítés): Teszt adatok létrehozása (users, tasks, stb.)
2. **Act** (Végrehajtás): API endpoint meghívása
3. **Assert** (Ellenőrzés): Válasz és adatbázis állapot ellenőrzése

## Használt technológiák

- **PHPUnit**: PHP test framework
- **Laravel Testing**: Laravel beépített teszt eszközei
- **RefreshDatabase**: Minden teszt előtt tiszta adatbázis
- **Laravel Sanctum**: API token authentication tesztelése
- **Factory Pattern**: Teszt adatok generálása

## További tesztek írása

Ha új tesztet szeretnél írni, kövesd ezt a mintát:

```php
/** @test */
public function test_name_in_snake_case()
{
    // Arrange - Előkészítés
    $user = User::factory()->create(['is_admin' => true]);
    Sanctum::actingAs($user);

    // Act - Végrehajtás
    $response = $this->getJson('/api/endpoint');

    // Assert - Ellenőrzés
    $response->assertStatus(200)
        ->assertJson(['key' => 'value']);
        
    $this->assertDatabaseHas('table', ['field' => 'value']);
}
```

## Gyakori assert-ek

```php
// HTTP státusz
$response->assertStatus(200);
$response->assertStatus(201); // Created
$response->assertStatus(401); // Unauthorized
$response->assertStatus(403); // Forbidden
$response->assertStatus(422); // Validation Error

// JSON válasz
$response->assertJson(['key' => 'value']);
$response->assertJsonPath('user.name', 'John');
$response->assertJsonStructure(['data' => ['*' => ['id', 'name']]]);
$response->assertJsonFragment(['name' => 'John']);
$response->assertJsonMissing(['secret' => 'data']);
$response->assertJsonCount(5, 'data');

// Validációs hibák
$response->assertJsonValidationErrors(['email', 'password']);

// Adatbázis
$this->assertDatabaseHas('users', ['email' => 'test@example.com']);
$this->assertDatabaseMissing('users', ['email' => 'fake@example.com']);
$this->assertSoftDeleted('users', ['id' => 1]);
```

## CI/CD integráció

Ezek a tesztek könnyen integrálhatók CI/CD pipeline-okba:

```yaml
# GitHub Actions példa
- name: Run tests
  run: php artisan test
```

## Teszt adatbázis

A tesztek automatikusan SQLite memória adatbázist használnak, így nem befolyásolják a fejlesztői adatbázist.

Konfiguráció: `phpunit.xml`
```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```
