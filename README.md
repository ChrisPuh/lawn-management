# 🌱 Lawn Management System

> A Laravel-based application for managing lawn and garden care schedules, tasks, and maintenance.

## 🚀 Features

- 📅 Task scheduling for lawn maintenance (fertilizing, aerating, etc.)
- 📊 Visual lawn health tracking
- 🏡 Garden management system
- 📸 Image upload capabilities
- 👤 User authentication and profiles

## 💻 Tech Stack

- ![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=flat&logo=laravel&logoColor=white) Laravel 11
- ![Livewire](https://img.shields.io/badge/Livewire-4E56A6?style=flat&logo=livewire&logoColor=white) Livewire
- ![Blade](https://img.shields.io/badge/Blade-FF2D20?style=flat&logo=laravel&logoColor=white) Blade Templates
- ![Tailwind](https://img.shields.io/badge/Tailwind-38B2AC?style=flat&logo=tailwind-css&logoColor=white) Tailwind CSS
- ![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat&logo=mysql&logoColor=white) MySQL/PostgreSQL

## ⚙️ Requirements

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL/PostgreSQL

## 🕰️ Scheduled Tasks

### Temporary File Cleanup

The application includes an automated task to clean up temporary files:

- **Command:** `app:cleanup-temp-files`
- **Frequency:** Daily at midnight
- **Configuration:**
  - Retention period configurable in `.env`
  - Default: 24 hours
  - Can be forced with `--force` flag

#### Running the Scheduler

- **Local Development:**

    ```bash
    php artisan schedule:work
    ```

- **Production (Crontab):**

    ```bash
    * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
    ```

### Configuring Scheduler

Create `app/Providers/ScheduleServiceProvider.php`:

```php
<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ScheduleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->booted(function () {
            $schedule = $this->app->make(\Illuminate\Console\Scheduling\Schedule::class);

            $schedule->command('app:cleanup-temp-files')
                ->daily()
                ->appendOutputTo(storage_path('logs/temp-cleanup.log'));
        });
    }
}
```

Add to `config/app.php` providers:

```php
'providers' => [
    // Other providers...
    App\Providers\ScheduleServiceProvider::class,
],
```

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

# Testing Setup
cp .env.testing.example .env.testing
./setup-testing-env.sh

# Database setup
php artisan migrate

# Build assets
npm run build

# Start server
php artisan serve
```

### Environment Configuration

Key scheduling-related environment variables:

```env
# Temporary file cleanup settings
LAWN_TEMP_PATH=private/livewire-tmp
LAWN_TEMP_RETENTION_HOURS=24
LAWN_TEMP_CLEANUP_ENABLED=true
```

## 👨‍💻 Development

### Standards

- ✅ Follow PSR-12 coding standards
- ✅ Write tests for new features
- ✅ Comment complex logic
- ✅ Keep controllers thin, models fat

### Testing

```bash
# Run tests
php artisan test

# Static analysis
composer analyse
```

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'feat(lawn): add amazing feature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open pull request

## 🧹 Development Cleanup Commands

### Clear Lawn Images

During local development, you may want to clear all lawn images:

```bash
# Clear images with confirmation prompt
php artisan lawn:clear-images

# Force clear without confirmation
php artisan lawn:clear-images --force
```

**Warning:**

- This command is ONLY available in non-production environments
- It deletes ALL lawn images from storage and database
- Use with caution

## 📝 Commit Convention

This project follows strict commit message conventions to ensure consistent git history and automatic generation of changelogs.

See [COMMIT_CONVENTION.md](COMMIT_CONVENTION.md) for detailed guidelines.

## 📄 License

MIT License

---

Built with ❤️ for lawn enthusiasts
