# CoCaIne

> **Coin Cash Investment Engine** – Asisten Celengan Pintar Berbasis IoT & Dashboard Web Modern.

---

## Tentang CoCaIne

**CoCaIne** (Coin Cash Investment Engine) adalah ekosistem celengan pintar (smart piggy bank) berbasis IoT yang dirancang untuk mendeteksi, mencatat, dan mengelola tabungan fisik secara otomatis ke dalam dashboard digital.

Sistem ini menggabungkan perangkat keras berbasis mikrokontroler untuk validasi fisik dan antarmuka web modern untuk pelacakan transaksi secara _real-time_.

---

## Arsitektur & Teknologi

### Perangkat Keras (Hardware)

- **Mikrokontroler Utama:** ESP32 / Arduino.
- **Sensor Validasi Koin:** Sensor Warna (Color Sensor) untuk mendeteksi jenis koin.
- **Autentikasi Pengguna:** Modul RFID (MFRC522) untuk akses akun berbasis kartu/keychain.
- **Desain Casing:** Desain 3D-printed dengan pendekatan estetika **Brutalist Architecture** yang mengutamakan permukaan datar yang fungsional untuk modul RFID dan membran keypad.

### Perangkat Lunak (Software & Frontend)

- **Framework Utama:** Laravel (PHP).
- **Asset Bundler & Frontend:** Vite.
- **Desain Antarmuka:** Kombinasi estetika _Glassmorphism_ dan _Cyberpunk/Futuristic User Interface (FUI)_ untuk tampilan dashboard yang interaktif dan modern.
- **Deployment:** Netlify (Frontend/Static Assets Compilation).

---
