<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Lawn;
use App\Models\LawnCare;
use App\Models\LawnImage;
use App\Models\User;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Log;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Clear existing lawn images before seeding
        $this->clearLawnImages();

        // Benutzer-Erstellung basierend auf der Umgebung
        $user = User::firstOrCreate(
            ['email' => 'chrisganzert@lawn.com'],
            [
                'name' => 'Chris Ganzert',
                'password' => Hash::make(config('app.admin_initial_password')),
                'email_verified_at' => now(), // Immer verifiziert
            ]
        );

        // Unterschiedliches Seeding basierend auf der Umgebung
        if (app()->environment('production')) {
            // Nur Benutzer in Produktion
            Log::info('Seeding only admin user in production');
        } else {
            // Entwicklungsumgebung: Benutzer mit Lawns und Pflegemaßnahmen
            $this->createDevelopmentData($user);
        }

        //TODO if (!app()->environment('production') || config('app.debug')) {
        $this->call(DiagnosticSeeder::class);
        
    }

    private function createDevelopmentData(User $user): void
    {
        // Erstelle 2 initiale Lawns für den Benutzer
        $lawns = Lawn::factory(2)->for($user)->create();

        // Füge Pflegemaßnahmen hinzu
        $lawns->each(function ($lawn) {
            // Erstelle verschiedene Pflegemaßnahmen
            LawnCare::factory()->mowing()->for($lawn)->create();
            LawnCare::factory()->fertilizing()->for($lawn)->create();
            LawnCare::factory()->watering()->for($lawn)->create();
        });

        Log::info('Seeding development data with lawns and maintenance');
    }

    private function clearLawnImages(): void
    {
        try {
            // Lösche alle Lawn Images aus der Datenbank
            LawnImage::query()->delete();

            // Lösche physische Dateien
            $lawnsImagePath = 'lawns';

            if (Storage::disk('public')->exists($lawnsImagePath)) {
                $files = Storage::disk('public')->allFiles($lawnsImagePath);

                foreach ($files as $file) {
                    Storage::disk('public')->delete($file);
                }

                // Lösche leere Unterverzeichnisse
                $directories = Storage::disk('public')->directories($lawnsImagePath);
                foreach ($directories as $dir) {
                    Storage::disk('public')->deleteDirectory($dir);
                }
            }

            Log::info('DatabaseSeeder: Alle Lawn Images gelöscht');
        } catch (Exception $e) {
            Log::error('DatabaseSeeder: Fehler beim Löschen der Lawn Images: ' . $e->getMessage());
        }
    }
}
