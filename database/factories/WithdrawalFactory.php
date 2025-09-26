<?php

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Withdrawal>
 */
class WithdrawalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = $this->faker->numberBetween(50000, 500000); // 50k to 500k in rupiah
        $feeAmount = Withdrawal::calculateFee($amount);
        $netAmount = $amount - $feeAmount;
        
        $method = $this->faker->randomElement(['bank_transfer', 'e_wallet']);
        $status = $this->faker->randomElement(['pending', 'approved', 'processing', 'completed', 'rejected', 'cancelled']);
        
        $requestedAt = $this->faker->dateTimeBetween('-30 days', 'now');
        
        return [
            'campaign_id' => Campaign::factory(),
            'amount' => $amount,
            'fee_amount' => $feeAmount,
            'net_amount' => $netAmount,
            'method' => $method,
            'account_info' => $this->generateAccountInfo($method),
            'status' => $status,
            'notes' => $status === 'rejected' ? $this->faker->sentence() : null,
            'reference_number' => $status === 'completed' ? 'WD' . strtoupper($this->faker->bothify('??##??##')) : null,
            'approved_by' => in_array($status, ['approved', 'processing', 'completed', 'rejected']) 
                ? User::factory() 
                : null,
            'requested_at' => $requestedAt,
            'approved_at' => in_array($status, ['approved', 'processing', 'completed', 'rejected']) 
                ? $this->faker->dateTimeBetween($requestedAt, '+2 days') 
                : null,
            'processed_at' => in_array($status, ['processing', 'completed']) 
                ? $this->faker->dateTimeBetween($requestedAt, '+3 days') 
                : null,
            'completed_at' => $status === 'completed' 
                ? $this->faker->dateTimeBetween($requestedAt, '+5 days') 
                : null,
        ];
    }

    /**
     * Generate account info based on withdrawal method
     */
    private function generateAccountInfo(string $method): array
    {
        if ($method === 'bank_transfer') {
            return [
                'account_name' => $this->faker->name(),
                'bank_name' => $this->faker->randomElement(['BCA', 'BRI', 'BNI', 'Mandiri', 'CIMB', 'Danamon']),
                'account_number' => $this->faker->numerify('##########'),
            ];
        } else {
            return [
                'account_name' => $this->faker->name(),
                'wallet_type' => $this->faker->randomElement(['gopay', 'ovo', 'dana', 'linkaja']),
                'phone_number' => $this->faker->phoneNumber(),
            ];
        }
    }

    /**
     * Indicate that the withdrawal is pending
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'approved_by' => null,
            'approved_at' => null,
            'processed_at' => null,
            'completed_at' => null,
            'notes' => null,
            'reference_number' => null,
        ]);
    }

    /**
     * Indicate that the withdrawal is completed
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'approved_by' => User::factory(),
            'approved_at' => $this->faker->dateTimeBetween($attributes['requested_at'], '+1 day'),
            'processed_at' => $this->faker->dateTimeBetween($attributes['requested_at'], '+2 days'),
            'completed_at' => $this->faker->dateTimeBetween($attributes['requested_at'], '+3 days'),
            'reference_number' => 'WD' . strtoupper($this->faker->bothify('??##??##')),
        ]);
    }

    /**
     * Indicate that the withdrawal is rejected
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'approved_by' => User::factory(),
            'approved_at' => $this->faker->dateTimeBetween($attributes['requested_at'], '+1 day'),
            'processed_at' => null,
            'completed_at' => null,
            'notes' => $this->faker->randomElement([
                'Informasi rekening tidak valid atau tidak sesuai dengan identitas.',
                'Dokumen pendukung tidak lengkap atau tidak jelas.',
                'Kampanye belum mencapai target minimum untuk penarikan.',
                'Terdapat indikasi aktivitas mencurigakan pada kampanye.',
            ]),
            'reference_number' => null,
        ]);
    }

    /**
     * Indicate that the withdrawal uses bank transfer
     */
    public function bankTransfer(): static
    {
        return $this->state(fn (array $attributes) => [
            'method' => 'bank_transfer',
            'account_info' => [
                'account_name' => $this->faker->name(),
                'bank_name' => $this->faker->randomElement(['BCA', 'BRI', 'BNI', 'Mandiri', 'CIMB', 'Danamon']),
                'account_number' => $this->faker->numerify('##########'),
            ],
        ]);
    }

    /**
     * Indicate that the withdrawal uses e-wallet
     */
    public function eWallet(): static
    {
        return $this->state(fn (array $attributes) => [
            'method' => 'e_wallet',
            'account_info' => [
                'account_name' => $this->faker->name(),
                'wallet_type' => $this->faker->randomElement(['gopay', 'ovo', 'dana', 'linkaja']),
                'phone_number' => $this->faker->phoneNumber(),
            ],
        ]);
    }
}
