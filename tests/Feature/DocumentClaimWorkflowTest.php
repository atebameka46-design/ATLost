<?php

namespace Tests\Feature;

use App\Models\DocumentClaim;
use App\Models\DocumentReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentClaimWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_claimant_can_submit_payment_for_an_accepted_claim(): void
    {
        $owner = User::factory()->create();
        $claimant = User::factory()->create();
        $report = DocumentReport::create([
            'user_id' => $owner->id,
            'document_type' => 'CNI',
            'owner_name' => 'Jean Dupont',
            'location' => 'Douala',
            'phone' => '677000000',
            'reward_amount' => 10000,
            'status' => 'approved',
        ]);
        $claim = DocumentClaim::create([
            'document_report_id' => $report->id,
            'claimant_id' => $claimant->id,
            'status' => 'accepted',
        ]);

        $response = $this->actingAs($claimant)->post(route('claims.pay', $claim), [
            'payment_method' => 'mobile_money',
            'payment_reference' => 'MTN-123456',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('document_claims', [
            'id' => $claim->id,
            'payment_status' => 'submitted',
            'paid_amount' => 10000,
            'service_fee_amount' => 1000,
            'payment_reference' => 'MTN-123456',
        ]);
    }

    public function test_admin_can_verify_payment_and_owner_can_complete_restitution(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create();
        $claimant = User::factory()->create();
        $report = DocumentReport::create([
            'user_id' => $owner->id,
            'document_type' => 'Passeport',
            'owner_name' => 'Amina Test',
            'location' => 'Yaoundé',
            'phone' => '677000001',
            'reward_amount' => 5000,
            'status' => 'approved',
        ]);
        $claim = DocumentClaim::create([
            'document_report_id' => $report->id,
            'claimant_id' => $claimant->id,
            'status' => 'accepted',
            'payment_status' => 'submitted',
            'payment_method' => 'mobile_money',
            'payment_reference' => 'OM-123456',
            'paid_amount' => 5000,
            'service_fee_amount' => 500,
            'appointment_at' => now()->addDay(),
        ]);

        $this->actingAs($admin)->patch(route('admin.payments.verify', $claim))->assertRedirect();
        $this->assertDatabaseHas('document_claims', ['id' => $claim->id, 'payment_status' => 'verified']);

        $this->actingAs($owner)->post(route('claims.complete', $claim))->assertRedirect();
        $this->assertDatabaseHas('document_claims', ['id' => $claim->id, 'status' => 'completed']);
        $this->assertDatabaseHas('document_reports', ['id' => $report->id, 'status' => 'resolved']);
    }

    public function test_owner_receives_net_amount_after_document_is_confirmed_and_can_withdraw_it(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create();
        $claimant = User::factory()->create();
        $report = DocumentReport::create([
            'user_id' => $owner->id,
            'document_type' => 'CNI',
            'owner_name' => 'Alice Dupont',
            'location' => 'Yaoundé',
            'phone' => '677000002',
            'reward_amount' => 5000,
            'status' => 'approved',
        ]);
        $claim = DocumentClaim::create([
            'document_report_id' => $report->id,
            'claimant_id' => $claimant->id,
            'status' => 'accepted',
            'payment_status' => 'verified',
            'payment_method' => 'mobile_money',
            'payment_reference' => 'OM-654321',
            'paid_amount' => 5000,
            'service_fee_amount' => 500,
            'appointment_at' => now()->addDay(),
            'payout_status' => 'pending',
            'payout_amount' => 0,
        ]);

        $this->actingAs($owner)->post(route('claims.complete', $claim))->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $owner->id, 'wallet_balance' => 4500.00]);
        $this->assertDatabaseHas('document_claims', ['id' => $claim->id, 'payout_status' => 'available', 'payout_amount' => 4500.00]);

        $this->actingAs($owner)->post(route('claims.withdraw', $claim))->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $owner->id, 'wallet_balance' => 0.00]);
        $this->assertDatabaseHas('document_claims', ['id' => $claim->id, 'payout_status' => 'withdrawn']);
    }
}
