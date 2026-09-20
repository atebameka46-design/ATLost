<?php

namespace Tests\Feature;

use App\Models\DocumentReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageDocumentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_displays_real_available_documents(): void
    {
        $owner = User::factory()->create();
        DocumentReport::create([
            'user_id' => $owner->id,
            'document_type' => 'CNI',
            'owner_name' => 'Document Réel',
            'location' => 'Douala',
            'phone' => '677000000',
            'status' => 'approved',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Document Réel');
        $response->assertSee('Voir tout');
    }
}
