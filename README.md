# LankaPOS Enterprise 🇱🇰

> **A Modern, Robust Point of Sale (POS) & Retail Management System built with Laravel 11, Tailwind CSS, Alpine.js, and MySQL.**

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green.svg?style=for-the-badge)

---

## 🌟 Key Features

- **⚡ Fast POS Terminal:** Interactive cashier interface with real-time barcode scanning, item search, instant tax/discount calculations, and thermal receipt printing.
- **📦 Inventory & Stock Management:** Track multi-store branch inventory, stock alerts, SKU/barcode generation, category classification, and batch tracking.
- **🏢 Multi-Store & Branch Support:** Manage distinct branch locations, cashier terminal assignments, and store-specific transactions.
- **👥 Role-Based Access Control (RBAC):** Granular permissions using `spatie/laravel-permission` (Super Admin, Branch Managers, Cashiers).
- **📊 Business Analytics & Reports:** Visual sales reports, daily profit/loss breakdowns, top-selling products, and exportable financial reports (Excel/PDF via DomPDF & Maatwebsite).
- **📑 Invoice & Receipt Printing:** Formatted thermal receipts (80mm/58mm) and standard A4 PDF invoices with dynamic QR codes and barcodes.
- **🛡️ Audit Logs & System Backups:** Full activity logging (`spatie/laravel-activitylog`) and automated database backup management (`spatie/laravel-backup`).

---

## 📋 System Requirements

- **PHP:** `^8.2` (with `pdo`, `mbstring`, `openssl`, `gd`, `bcmath`, `curl` extensions enabled)
- **Database:** MySQL `^8.0` or MariaDB `^10.4`
- **Dependency Managers:** [Composer](https://getcomposer.org/) & [Node.js / NPM](https://nodejs.org/)

---

## 🚀 Quick Setup Guide

### 1. Clone the Repository
```bash
git clone https://github.com/Aathil-mohamed/LankaPOS-Enterprise.git
cd LankaPOS-Enterprise
```

### 2. Install Dependencies
```bash
composer install
npm install && npm run build
```

### 3. Environment Configuration
Copy the sample `.env.example` file:
```bash
cp .env.example .env
php artisan key:generate
```

Configure your database connection in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_enterprise
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Run Migrations & Seed Database
```bash
php artisan migrate:fresh --seed
```

### 5. Start the Development Server
```bash
php artisan serve
```
Access the application in your browser at `http://localhost:8000`.

---

## 🔑 Default Login Credentials

| Role | Name | Email | Password | Access Level |
| :--- | :--- | :--- | :--- | :--- |
| **Super Admin** | Super Administrator | `admin@pos.com` | `Admin123!@#` | Full System Access & Settings |
| **Cashier** | Default Cashier | `cashier@pos.com` | `Cashier123!@#` | POS Terminal & Daily Billing |

---

## 🧪 Testing

Run the automated test suite:
```bash
php artisan test
```

---

## 📄 License

This project is licensed under the [MIT License](LICENSE).
