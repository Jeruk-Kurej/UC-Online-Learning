# 🎓 UC Online Learning Profile Directory & Hub (UCO)
> **Comprehensive Alumni Profiles, Student Startup Catalog, and AI-Powered Testimonial Moderation Engine.**

[![Laravel Version](https://img.shields.io/badge/Laravel-11.x-red.svg?style=flat-square&logo=laravel)](https://laravel.com/)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue.svg?style=flat-square&logo=php)](https://www.php.net/)
[![Tailwind CSS](https://img.shields.io/badge/TailwindCSS-v3-blueviolet.svg?style=flat-square&logo=tailwind-css)](https://tailwindcss.com/)
[![Docker](https://img.shields.io/badge/Docker-Ready-blue.svg?style=flat-square&logo=docker)](https://www.docker.com/)

---

## 🌟 Overview
**UC Online Learning (UCO)** is an enterprise directory and networking platform engineered for university students and alumni. It showcases student-owned businesses, catalogs alumni achievements (certifications, skills), and simplifies regional analysis of university startup distribution.

The application also features an administrative panel equipped with an **AI-powered content moderation flow** that scores, flags, and helps approve student-submitted testimonies.

---

## ⚡ Key Features

### 🏢 Student & Alumni Business Directory
- Comprehensive profile pages for student and alumni entrepreneurs.
- Business catalog showcasing company details, product listings, categories, and target markets.
- Search and filtering system allowing users to explore businesses by category, location, or developer skills.

### 🤖 AI-Powered Testimony Moderation
- Streamlined administrative dashboard for monitoring student feedback.
- Automated visibility controls (approval/rejection) with in-dashboard toggles.
- Sentiment classification and moderation analysis hooks.

### 🗺️ Geographic & Regional Mapping
- Location hierarchy integration using localized entities: **Provinces**, **Regencies**, **Districts**, and **Villages**.
- Supports data mapping to identify the regional distribution of student-led ventures.

### 📥 Automated CSV Batch Importer
- Import pipeline for batch-uploading student and alumni profiles directly from survey response spreadsheets.
- Intelligent mapping matching imported strings to corresponding database skill models, certificates, and locations.

---

## 🛠️ Tech Stack & Architecture

- **Backend Framework:** Laravel (Model-View-Controller)
- **Database Engine:** MySQL with Eloquent ORM
- **Frontend Assets:** Blade Templates, Tailwind CSS, Vite
- **Storage Integrations:** Cloudinary CDN for profile pictures and product media uploads
- **Deployment:** Dockerized development and production pipelines

---

## 🚀 Setup & Installation

### Local Development
1. Clone the repository and navigate to the project directory:
   ```bash
   cd Documents/UCO_Websitee
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
6. Start the development servers:
   ```bash
   # Start PHP server
   php artisan serve
   
   # Start Vite assets compiler
   npm run dev
   ```

### Running with Docker
```bash
# Build and start container services in the background
docker compose up -d --build
```

---

*Developed for the UC Online Learning Community.*
