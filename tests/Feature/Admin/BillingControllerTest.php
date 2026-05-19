<?php

namespace Tests\Feature\Admin;

use App\Http\Controllers\Admin\BillingController;
use App\Models\BillingTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class BillingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_marking_a_pending_transaction_as_paid_sets_the_payment_date(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $member = User::factory()->create([
            'role' => 'member',
        ]);

        $billing = BillingTransaction::create([
            'user_id' => $member->id,
            'invoice_number' => 'INV-20260519-0001',
            'type' => 'membership',
            'amount' => 1500,
            'subtotal' => 1500,
            'discount_percentage' => 0,
            'discount_amount' => 0,
            'tax_rate' => 0,
            'tax_amount' => 0,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'description' => 'Membership fee',
            'payment_date' => null,
        ]);

        $request = Request::create('/admin/billing/' . $billing->id, 'PUT', [
            'payment_status' => 'paid',
            'payment_method' => 'cash',
        ]);

        $request->setUserResolver(fn () => $admin);

        $response = app(BillingController::class)->update($request, $billing);

        $this->assertSame(302, $response->getStatusCode());

        $billing->refresh();

        $this->assertSame('paid', $billing->payment_status);
        $this->assertNotNull($billing->getRawOriginal('payment_date'));
        $this->assertSame(Carbon::now()->format('Y-m-d'), $billing->getRawOriginal('payment_date'));
    }
}
