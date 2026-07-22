<?php

namespace Tests\Feature\FileManager;

use App\Models\File;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FileDetailsResponseTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_returns_only_safe_fields(): void
    {
        $owner = User::factory()->create();

        $file = File::create([
            'user_id' => $owner->id,
            'name' => 'secret-storage-name.pdf',
            'original_name' => 'invoice.pdf',
            'path' => 'uploads/secret-storage-name.pdf',
            'mime_type' => 'application/pdf',
            'size' => 1200,
            'extension' => 'pdf',
            'folder' => '/',
            'is_public' => false,
        ]);

        $response = $this->actingAs($owner)->getJson(route('file-manager.show', $file->id));

        $response->assertOk()
            ->assertJsonFragment(['original_name' => 'invoice.pdf'])
            // Internal columns must never be exposed to the client.
            ->assertJsonMissing(['path' => 'uploads/secret-storage-name.pdf'])
            ->assertJsonMissingPath('user_id')
            ->assertJsonMissingPath('path')
            ->assertJsonMissingPath('name');
    }

    public function test_show_is_scoped_to_owner(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $file = File::create([
            'user_id' => $owner->id,
            'name' => 'example.pdf',
            'original_name' => 'example.pdf',
            'path' => 'uploads/example.pdf',
            'mime_type' => 'application/pdf',
            'size' => 1200,
            'extension' => 'pdf',
            'folder' => '/',
            'is_public' => false,
        ]);

        $this->actingAs($otherUser)
            ->getJson(route('file-manager.show', $file->id))
            ->assertNotFound();
    }
}
