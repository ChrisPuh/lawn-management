# 🌱 Lawn Management System

> A Laravel-based application for managing lawn and garden care schedules, tasks, and maintenance.

## 🚀 Features

-   📅 Task scheduling for lawn maintenance (fertilizing, aerating, etc.)
-   📊 Visual lawn health tracking
-   🏡 Garden management system
-   📸 Image upload capabilities
-   👤 User authentication and profiles

## 💻 Tech Stack

-   ![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=flat&logo=laravel&logoColor=white) Laravel 10
-   ![Livewire](https://img.shields.io/badge/Livewire-4E56A6?style=flat&logo=livewire&logoColor=white) Livewire
-   ![Blade](https://img.shields.io/badge/Blade-FF2D20?style=flat&logo=laravel&logoColor=white) Blade Templates
-   ![Tailwind](https://img.shields.io/badge/Tailwind-38B2AC?style=flat&logo=tailwind-css&logoColor=white) Tailwind CSS
-   ![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat&logo=mysql&logoColor=white) MySQL/PostgreSQL

## ⚙️ Requirements

-   PHP >= 8.2
-   Composer
-   Node.js & NPM
-   MySQL/PostgreSQL
-   GitHub Personal Access Token with scopes:
    -   `repo`
    -   `project`
    -   `read:project`

## 🚀 Getting Started

### Installation

```bash
# Clone repository
git clone git@github.com:USERNAME/lawn-management.git
cd lawn-management

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate

# Build assets
npm run build

# Start server
php artisan serve
```

### GitHub Project Setup

#### 1. Create GitHub Token

1. Navigate to GitHub Settings → Developer Settings → Personal Access Tokens → Tokens (classic)
2. Click "Generate new token" → "Generate new token (classic)"
3. Enable required scopes: `repo`, `project`, `read:project`
4. Copy the generated token

#### 2. Configure Environment

```bash
# Create environment file for GitHub scripts
cp create-labels.env.example create-labels.env
```

Edit `create-labels.env`:

```env
GITHUB_TOKEN=your_token_here
GITHUB_REPO=username/lawn-management
```

#### 3. Run Setup Scripts

```bash
# Create GitHub labels
php create-labels.php

# Create milestones
php create-milestones.php

# Create project board
php create-project.php
```

This will set up:

-   🏷️ Issue labels for tracking priorities and types
-   🎯 Project milestones for development phases
-   📋 Project board with custom fields and views

## 👨‍💻 Development

### Standards

-   ✅ Follow PSR-12 coding standards
-   ✅ Write tests for new features
-   ✅ Comment complex logic
-   ✅ Keep controllers thin, models fat

### Testing

```bash
php artisan test
```

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'feat(lawn): add amazing feature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open pull request

## 📁 Project Structure

```
lawn-management/
├── app/                  # Application code
│   ├── Http/            # Controllers, Middleware
│   ├── Models/          # Eloquent models
│   └── Services/        # Business logic
├── database/
│   ├── migrations/      # Database migrations
│   └── seeders/        # Database seeders
├── resources/
│   ├── views/          # Blade templates
│   └── js/            # JavaScript files
├── routes/             # Application routes
└── tests/             # Test files
```

## 📝 Commit Convention

This project follows strict commit message conventions to ensure consistent git history and automatic generation of changelogs.

See [COMMIT_CONVENTION.md](COMMIT_CONVENTION.md) for detailed guidelines and examples.

Quick example:

```bash
git commit -m "feat(lawn): add automatic watering detection"
```

## 📄 License

MIT License

---

Built with ❤️ for lawn enthusiasts
