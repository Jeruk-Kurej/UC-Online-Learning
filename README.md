# 🎓 UC Online Learning Profile Directory & Hub (UCO)
> **Comprehensive Alumni Profiles, Student Startup Catalog, and AI-Powered Testimonial Moderation Engine.**

[![Laravel Version](https://img.shields.io/badge/Laravel-12.x-red.svg?style=flat-square&logo=laravel)](https://laravel.com/)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue.svg?style=flat-square&logo=php)](https://www.php.net/)
[![Tailwind CSS](https://img.shields.io/badge/TailwindCSS-v3-blueviolet.svg?style=flat-square&logo=tailwind-css)](https://tailwindcss.com/)

---

## 🌟 Overview
**UC Online Learning (UCO)** is an enterprise directory and networking platform engineered for university students and alumni. It showcases student-owned businesses, catalogs alumni achievements (certifications, skills), and simplifies regional analysis of university startup distribution.

Administrators curate what is public: they approve businesses and work profiles and choose which people, businesses and testimonies are featured on the home page.

---

## ⚡ Key Features

### 🏢 Student & Alumni Business Directory
- Comprehensive profile pages for student and alumni entrepreneurs.
- Business catalog showcasing company details, product listings, categories, and target markets.
- Search and filtering system allowing users to explore businesses by category, location, or developer skills.

### ⭐ Admin Curation
- Approval flow for businesses and work profiles (pending, approved, rejected, need revision).
- Featured toggles for users, businesses and testimonies.
- A Gemini-based `AiModerationService` exists for scoring testimonies from the command line (`php artisan ai:analyze-testimonies`); it is not part of the submit flow.

### 🗺️ Geographic & Regional Mapping
- Location hierarchy integration using localized entities: **Provinces**, **Regencies**, **Districts**, and **Villages**.
- Supports data mapping to identify the regional distribution of student-led ventures.

### 📥 Automated CSV Batch Importer
- Import pipeline for batch-uploading student and alumni profiles directly from survey response spreadsheets.
- Intelligent mapping matching imported strings to corresponding database skill models, certificates, and locations.

---

## 🛠️ Tech Stack & Architecture

- **Backend Framework:** Laravel 12 (Model-View-Controller), PHP 8.2+
- **Database Engine:** MySQL with Eloquent ORM
- **Frontend Assets:** Blade Templates, Tailwind CSS, Alpine.js, Vite
- **Storage Integrations:** Cloudinary CDN for profile pictures and product media uploads
- **Deployment:** Vercel (`vercel.json`)

---

## 🚀 Setup & Installation

### Local Development
1. Clone the repository and navigate to the project directory:
   ```bash
   cd UC-Online-Learning
   ```
2. Install Composer dependencies:
   ```bash
   composer install
   ```
3. Install frontend dependencies:
   ```bash
   npm install
   ```
4. Copy the environment configuration and generate the application key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
5. Set up database credentials in your `.env` and run migrations:
   ```bash
   php artisan migrate --seed
   ```
6. Start the PHP server, the queue worker (needed for imports) and Vite together:
   ```bash
   composer run dev
   ```

### Tests
```bash
npm run build
composer run test
```

---

*Developed for the UC Online Learning Community.*
