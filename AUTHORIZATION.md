# Jogosultságok (Authorization) Dokumentáció

## Szerepkörök (Roles)

### 1. Admin (role = 1)
- **Összes jogra hozzáfér**
- Abilities: `admin`, `customer`
- Képes:
  - Az összes felhasználót kezelni (lista, mutatás, módosítás, törlés)
  - Az összes terméket kezelni (létrehozás, módosítás, törlés)
  - Az összes kategóriát kezelni
  - Az összes céget kezelni
  - Az összes paramétert kezelni
  - Az összes képet kezelni
  - Az összes egységet kezelni
  - Az összes termék paramétert kezelni
  - Az összes kommentet kezelni (módosítás, törlés)
  - Az összes kosarat kezelni

### 2. Vásárló (role = 2 vagy más)
- **Limitált jogok**
- Abilities: `customer`
- Képes:
  - Termékeket megtekinteni (lista, részletek)
  - Kategóriákat megtekinteni
  - Cégeket megtekinteni
  - Paramétereket megtekinteni
  - Képeket megtekinteni
  - Egységeket megtekinteni
  - Kommenteket írni a termékekhez
  - Saját kommentjeit szerkeszteni és törölni
  - Kosarakat létrehozni
  - Saját kosarát kezelni (módosítás, törlés)
  - Kosár tételeket hozzáadni és módosítani
  - Saját profilt megtekinteni és módosítani

## Routes és Jogosultságok

### Nyilvános (Auth nélkül elérhető)
- `POST /users/login` - Bejelentkezés
- `POST /users` - Regisztráció
- `GET /products` - Összes termék
- `GET /products/{id}` - Egy termék
- `GET /comments` - Összes komment

### Admin jogok (ability:admin middleware)
```
GET    /users - Felhasználó lista
GET    /users/{id} - Egy felhasználó
PATCH  /users/{id} - Felhasználó módosítás
DELETE /users/{id} - Felhasználó törlés

POST   /products - Termék létrehozás
PATCH  /products/{id} - Termék módosítás
DELETE /products/{id} - Termék törlés

POST   /categories - Kategória létrehozás
PATCH  /categories/{category} - Kategória módosítás
DELETE /categories/{category} - Kategória törlés

POST   /companies - Cég létrehozás
PATCH  /companies/{company} - Cég módosítás
DELETE /companies/{company} - Cég törlés

POST   /parameters - Paraméter létrehozás
PATCH  /parameters/{parameter} - Paraméter módosítás
DELETE /parameters/{parameter} - Paraméter törlés

POST   /pics - Kép feltöltés
PATCH  /pics/{pic} - Kép módosítás
DELETE /pics/{pic} - Kép törlés

POST   /units - Egység létrehozás
PATCH  /units/{unit} - Egység módosítás
DELETE /units/{unit} - Egység törlés

POST   /product-parameters - Termék paraméter létrehozás
PATCH  /product-parameters/{product_parameter} - Módosítás
DELETE /product-parameters/{product_parameter} - Törlés

GET    /comments/{comment} - Komment megtekintés
PATCH  /comments/{comment} - Komment módosítás
DELETE /comments/{comment} - Komment törlés
```

### Vásárló jogok (ability:customer middleware)
```
GET    /usersme - Saját profil
PATCH  /usersme - Saját profil módosítás
PATCH  /usersmeupdatepassword - Jelszó módosítás
DELETE /usersme - Saját fiók törlés

GET    /carts - Kosarak lista
POST   /carts - Kosár létrehozás
GET    /carts/{cart} - Egy kosár
PATCH  /carts/{cart} - Kosár módosítás
DELETE /carts/{cart} - Kosár törlés

GET    /cart-items - Kosár tételek lista
POST   /cart-items - Tétel hozzáadás
GET    /cart-items/{cart_item} - Egy tétel
PATCH  /cart-items/{cart_item} - Tétel módosítás
DELETE /cart-items/{cart_item} - Tétel törlés

POST   /comments - Komment létrehozás
```

## Policy Szabályok

### UserPolicy
- **view**: Csak a saját profilt lehet megtekinteni
- **update**: Csak a saját profilt lehet módosítani
- **updateAdmin**: Admin módosíthatja más felhasználókat, de nem a saját role-jét
- **delete**: Csak a saját fiók törölhető
- **deleteAdmin**: Admin más felhasználókat törölhet, de nem önmagát

### CartPolicy
- **view**: Admin vagy a kosár tulajdonosa
- **create**: Bármelyik felhasználó létrehozhat kosarat
- **update**: Admin vagy a kosár tulajdonosa
- **delete**: Admin vagy a kosár tulajdonosa

### CartItemPolicy
- **view**: Admin vagy az adott kosár tulajdonosa
- **create**: Bármelyik felhasználó hozzáadhat tételt
- **update**: Admin vagy az adott kosár tulajdonosa
- **delete**: Admin vagy az adott kosár tulajdonosa

### CommentPolicy
- **create**: Bármelyik felhasználó kommentelhet
- **update**: Admin vagy a komment szerzője
- **delete**: Admin vagy a komment szerzője

### ProductPolicy, CategoryPolicy, CompanyPolicy, ParameterPolicy, PicPolicy, UnitPolicy, ProductParameterPolicy
- **viewAny/view**: Mindenki megtekinthet
- **create**: Csak admin
- **update**: Csak admin
- **delete**: Csak admin

## Token és Abilities

A bejelentkezéskor kapott token tartalmazza az abilities-t (jogosultságokat). Az abilities alapján működnek a middleware-ek:

```php
// Admin
'ability:admin'

// Vásárló
'ability:customer'
```

Az API a `Authorization: Bearer {token}` header-ből olvassa a token-t.

## Hibakezelés

- **401 Unauthorized**: Nincs bejelentkezve vagy lejárt a token
- **403 Forbidden**: Nincs megfelelő jogosultság a művelethez

## Megvalósított validációk

1. **Database szinten**: Az adatbázis modelleken keresztül előírja az user_id mezőt
2. **Policy szinten**: Laravel Policy-k szabályozzák a hozzáférést
3. **Controller szinten**: Az `authorize()` metódussal ellenőriz a kontrollerek
4. **Route szinten**: Az `ability:admin` és `ability:customer` middleware-ek szűrnek
