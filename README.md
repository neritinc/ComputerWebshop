
---

# Számítógépes Webshop – Online Értékesítő Webalkalmazás

Ez a projekt egy **számítógépes alkatrészeket és kiegészítőket forgalmazó webshop** számára készült webalkalmazás, amely lehetővé teszi a vásárlók számára az online termékböngészést és rendelést, valamint külön adminisztrációs felületet biztosít az üzlet üzemeltetőinek a termékek, rendelések és felhasználók kezelésére.

A rendszer **Laravel (backend API)** és **Vue.js (frontend)** technológiákra épül.

---

## Fő funkciók

### Vásárló (User) funkciók

* Regisztráció és bejelentkezés
* Termékek böngészése kategóriák szerint
* Termékek keresése és szűrése (ár, kategória, gyártó)
* Termék részleteinek megtekintése (leírás, ár, készlet)
* Termékek kosárba helyezése
* Kosár tartalmának módosítása (mennyiség, törlés)
* Online rendelés leadása
* Termékértékelés leadása vásárlás után (szöveges vélemény)

---

### Admin funkciók

* Admin felület bejelentkezés után
* Új termékek létrehozása
* Termékek szerkesztése és törlése
* Termékkategóriák kezelése
* Készletkezelés
* Felhasználók kezelése
---

## Rendelési folyamat működése

* A felhasználó termékeket ad a kosárhoz
* A rendszer ellenőrzi a készlet elérhetőségét
* A rendelés leadásakor a rendszer:

  * elmenti a rendelés adatait
  * csökkenti a készlet mennyiségét
* Egy termék csak elérhető készlet esetén rendelhető

---

---

## Értékelési rendszer

* Az értékelések a termék adatlapján jelennek meg
* Az értékelések tartalmazzák:
  * szöveges véleményt

---

## Technológia

* **Backend:** Laravel (REST API)
* **Frontend:** Vue.js
* **Adatbázis:** MySQL
* **Hitelesítés:** Laravel Auth / Sanctum
* **Időzített feladatok:** Laravel Scheduler
* **Email küldés:** Laravel Mail

---

## Projekt célja

A projekt célja egy **modern, átlátható és könnyen használható számítógépes webshop** létrehozása, amely lehetővé teszi a gyors online vásárlást, egyszerűsíti a rendeléskezelést, és hatékony eszközt biztosít az üzlet adminisztrátorai számára a mindennapi működéshez.

---
