<?php

declare(strict_types=1);

use App\Livewire\Components\LawnImageUpload;
use App\Models\Lawn;
use App\Models\LawnImage;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

describe('LawnImageUpload Component', function (): void {

    beforeEach(function (): void {
        $this->user = User::factory()->create();
        $this->lawn = Lawn::factory()->create(['user_id' => $this->user->id]);
        Storage::fake('public');
    });

    it('renders without an existing image', function (): void {
        Livewire::actingAs($this->user)
            ->test(LawnImageUpload::class, ['lawn' => $this->lawn])
            ->assertSee('Bild auswählen')
            ->assertDontSee('Bild ändern');
    });

    it('allows uploading an image', function (): void {
        $image = UploadedFile::fake()->image('lawn.jpg', 1200, 800)->size(100);

        Livewire::actingAs($this->user)
            ->test(LawnImageUpload::class, ['lawn' => $this->lawn])
            ->set('image', $image)
            ->assertSet('showConfirmation', true);
    });

    it('saves an uploaded image', function (): void {
        $image = UploadedFile::fake()->image('lawn.jpg', 1200, 800)->size(100);

        Livewire::actingAs($this->user)
            ->test(LawnImageUpload::class, ['lawn' => $this->lawn])
            ->set('image', $image)
            ->call('save')
            ->assertSet('showSuccessMessage', true)
            ->assertDispatched('image-uploaded'); // Change this line
    });

    it('replaces existing image when uploading a new one', function (): void {

        // First, create an existing image
        $existingImage = LawnImage::factory()->create([
            'lawn_id' => $this->lawn->id,
            'type' => 'general',
        ]);

        $newImage = UploadedFile::fake()->image('new_lawn.jpg', 1200, 800)->size(100);

        Livewire::actingAs($this->user)
            ->test(LawnImageUpload::class, ['lawn' => $this->lawn])
            ->set('image', $newImage)
            ->call('save');

        // Refresh the existing image from the database
        $existingImage->refresh();

        // Assert old image is archived
        expect($existingImage->archived_at)->not->toBeNull();

        // Assert new image exists
        $this->assertDatabaseHas('lawn_images', [
            'lawn_id' => $this->lawn->id,
            'type' => 'general',
            'archived_at' => null,
        ]);
    });

    it('validates image upload', function (): void {
        $invalidImage = UploadedFile::fake()->create('document.pdf', 500);

        Livewire::actingAs($this->user)
            ->test(LawnImageUpload::class, ['lawn' => $this->lawn])
            ->set('image', $invalidImage)
            ->call('save')
            ->assertHasErrors('image');
    });

    it('cannot upload image for unauthorized lawn', function (): void {
        $this->withoutExceptionHandling();
        $otherUser = User::factory()->create();

        $this->expectException(Illuminate\Auth\Access\AuthorizationException::class);

        Livewire::actingAs($otherUser)
            ->test(LawnImageUpload::class, ['lawn' => $this->lawn])
            ->call('save');
    });

    it('allows cancelling image upload', function (): void {
        $image = UploadedFile::fake()->image('lawn.jpg', 1200, 800)->size(100);

        Livewire::actingAs($this->user)
            ->test(LawnImageUpload::class, ['lawn' => $this->lawn])
            ->set('image', $image)
            ->call('cancel')
            ->assertSet('image', null)
            ->assertSet('showConfirmation', false);
    });

    it('shows success message after deleting an image', function (): void {
        // Create an existing image
        $image = LawnImage::factory()->create([
            'lawn_id' => $this->lawn->id,
            'type' => 'general',
        ]);

        Livewire::actingAs($this->user)
            ->test(LawnImageUpload::class, ['lawn' => $this->lawn])
            ->call('delete', $image->id)
            ->assertDispatched('show-success-message')
            ->assertDispatched('image-deleted');

        // Verify the image is archived
        $image->refresh();
        expect($image->archived_at)->not->toBeNull();
    });

    it('can delete an existing image', function (): void {
        // Create an existing image
        $image = LawnImage::factory()->create([
            'lawn_id' => $this->lawn->id,
            'type' => 'general',
        ]);

        Livewire::actingAs($this->user)
            ->test(LawnImageUpload::class, ['lawn' => $this->lawn])
            ->call('delete', $image->id)
            ->assertDispatched('image-deleted')
            ->assertDispatched('show-success-message');

        // Assert image is archived
        $image->refresh();
        expect($image->archived_at)->not->toBeNull();
    });
    it('cannot delete image for unauthorized lawn', function (): void {
        $this->withoutExceptionHandling();
        $otherUser = User::factory()->create();

        // Create an existing image
        $image = LawnImage::factory()->create([
            'lawn_id' => $this->lawn->id,
            'type' => 'general',
        ]);

        $this->expectException(Illuminate\Auth\Access\AuthorizationException::class);

        Livewire::actingAs($otherUser)
            ->test(LawnImageUpload::class, ['lawn' => $this->lawn])
            ->call('delete', $image->id);
    });
});
