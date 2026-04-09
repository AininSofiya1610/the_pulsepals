<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LeadTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_can_display_leads_index()
    {
        $response = $this->get(route('leads.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_can_create_a_lead()
    {
        $response = $this->post(route('leads.store'), [
            'name'  => 'Ahmad Danial',
            'email' => 'ahmad@example.com',
            'phone' => '0123456789',
        ]);

        $response->assertRedirect(route('leads.index'));
        $this->assertDatabaseHas('leads', ['email' => 'ahmad@example.com']);
    }

    /** @test */
    public function it_requires_name_to_create_lead()
    {
        $response = $this->post(route('leads.store'), ['name' => '']);
        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function it_can_delete_a_lead()
    {
        $lead = Lead::factory()->create();

        $response = $this->delete(route('leads.destroy', $lead->id));

        $response->assertRedirect(route('leads.index'));
        $this->assertDatabaseMissing('leads', ['id' => $lead->id]);
    }

    /** @test */
    public function it_can_update_lead_status()
    {
        $lead = Lead::factory()->create(['status' => 'new_lead']);

        $response = $this->post(route('leads.updateStatus', $lead->id), [
            'status' => 'contacted',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('leads', [
            'id'     => $lead->id,
            'status' => 'contacted',
        ]);
    }
}
