<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WithdrawalSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get campaigns that have collected funds and are owned by creators
        $campaigns = Campaign::where('collected_amount', '>', 0)
            ->whereHas('user', function($query) {
                $query->where('role', 'creator');
            })
            ->with('user')
            ->get();

        if ($campaigns->isEmpty()) {
            $this->command->info('No campaigns with funds found. Skipping withdrawal seeder.');
            return;
        }

        // Get admin users for approval
        $adminUsers = User::where('role', 'admin')->pluck('id')->toArray();

        $withdrawalData = [];
        $createdCount = 0;

        foreach ($campaigns->take(10) as $campaign) {
            // Skip if campaign has very little funds
            if ($campaign->collected_amount < 10000000) { // Less than 100k in cents
                continue;
            }

            // Create 1-3 withdrawals per campaign with different statuses
            $withdrawalCount = rand(1, 3);
            
            for ($i = 0; $i < $withdrawalCount; $i++) {
                $amount = $this->generateWithdrawalAmount($campaign->collected_amount);
                $feeAmount = Withdrawal::calculateFee($amount);
                $netAmount = $amount - $feeAmount;
                
                $status = $this->getRandomStatus($i);
                $method = rand(0, 1) ? 'bank_transfer' : 'e_wallet';
                
                $accountInfo = $this->generateAccountInfo($method);
                
                $requestedAt = now()->subDays(rand(1, 30));
                $approvedAt = null;
                $processedAt = null;
                $completedAt = null;
                $approvedBy = null;
                $notes = null;
                $referenceNumber = null;

                // Set timestamps based on status
                if (in_array($status, ['approved', 'processing', 'completed'])) {
                    $approvedAt = $requestedAt->copy()->addHours(rand(1, 48));
                    $approvedBy = !empty($adminUsers) ? $adminUsers[array_rand($adminUsers)] : null;
                }

                if (in_array($status, ['processing', 'completed'])) {
                    $processedAt = $approvedAt->copy()->addHours(rand(1, 24));
                }

                if ($status === 'completed') {
                    $completedAt = $processedAt->copy()->addHours(rand(1, 72));
                    $referenceNumber = 'WD' . strtoupper(uniqid());
                }

                if ($status === 'rejected') {
                    $approvedAt = $requestedAt->copy()->addHours(rand(1, 48));
                    $approvedBy = !empty($adminUsers) ? $adminUsers[array_rand($adminUsers)] : null;
                    $notes = $this->getRandomRejectionReason();
                }

                $withdrawalData[] = [
                    'campaign_id' => $campaign->id,
                    'amount' => $amount,
                    'fee_amount' => $feeAmount,
                    'net_amount' => $netAmount,
                    'method' => $method,
                    'account_info' => json_encode($accountInfo),
                    'status' => $status,
                    'notes' => $notes,
                    'reference_number' => $referenceNumber,
                    'approved_by' => $approvedBy,
                    'requested_at' => $requestedAt,
                    'approved_at' => $approvedAt,
                    'processed_at' => $processedAt,
                    'completed_at' => $completedAt,
                    'created_at' => $requestedAt,
                    'updated_at' => $completedAt ?? $processedAt ?? $approvedAt ?? $requestedAt,
                ];

                $createdCount++;
            }
        }

        // Insert all withdrawal data
        if (!empty($withdrawalData)) {
            Withdrawal::insert($withdrawalData);
            $this->command->info("Created {$createdCount} withdrawal requests successfully.");
        } else {
            $this->command->info('No withdrawal data to insert.');
        }
    }

    /**
     * Generate withdrawal amount based on campaign collected amount
     */
    private function generateWithdrawalAmount(int $campaignAmount): int
    {
        $campaignAmountRupiah = $campaignAmount;
        
        // Generate amount between 10% to 80% of collected amount
        $minAmount = max(50000, (int)($campaignAmountRupiah * 0.1)); // Min 50k or 10%
        $maxAmount = (int)($campaignAmountRupiah * 0.8); // Max 80%
        
        return rand($minAmount, $maxAmount);
    }

    /**
     * Get random status with weighted distribution
     */
    private function getRandomStatus(int $index): string
    {
        $statuses = [
            'pending' => 20,
            'approved' => 15,
            'processing' => 10,
            'completed' => 40,
            'rejected' => 10,
            'cancelled' => 5,
        ];

        // First withdrawal more likely to be completed
        if ($index === 0) {
            $statuses['completed'] = 60;
            $statuses['pending'] = 10;
        }

        $rand = rand(1, 100);
        $cumulative = 0;

        foreach ($statuses as $status => $weight) {
            $cumulative += $weight;
            if ($rand <= $cumulative) {
                return $status;
            }
        }

        return 'pending';
    }

    /**
     * Generate account info based on method
     */
    private function generateAccountInfo(string $method): array
    {
        $names = [
            'Ahmad Rizki Pratama',
            'Siti Nurhaliza',
            'Budi Santoso',
            'Dewi Sartika',
            'Eko Prasetyo',
            'Fitri Handayani',
            'Gunawan Wijaya',
            'Hesti Purnamasari'
        ];

        $accountName = $names[array_rand($names)];

        if ($method === 'bank_transfer') {
            $banks = ['BCA', 'BRI', 'BNI', 'Mandiri', 'CIMB', 'Danamon', 'Permata', 'BSI'];
            
            return [
                'account_name' => $accountName,
                'bank_name' => $banks[array_rand($banks)],
                'account_number' => $this->generateAccountNumber(),
            ];
        } else {
            $walletTypes = ['gopay', 'ovo', 'dana', 'linkaja'];
            
            return [
                'account_name' => $accountName,
                'wallet_type' => $walletTypes[array_rand($walletTypes)],
                'phone_number' => $this->generatePhoneNumber(),
            ];
        }
    }

    /**
     * Generate random account number
     */
    private function generateAccountNumber(): string
    {
        return rand(1000000000, 9999999999);
    }

    /**
     * Generate random phone number
     */
    private function generatePhoneNumber(): string
    {
        $prefixes = ['0812', '0813', '0821', '0822', '0851', '0852', '0856', '0857'];
        $prefix = $prefixes[array_rand($prefixes)];
        $suffix = str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
        
        return $prefix . $suffix;
    }

    /**
     * Get random rejection reason
     */
    private function getRandomRejectionReason(): string
    {
        $reasons = [
            'Informasi rekening tidak valid atau tidak sesuai dengan identitas.',
            'Dokumen pendukung tidak lengkap atau tidak jelas.',
            'Kampanye belum mencapai target minimum untuk penarikan.',
            'Terdapat indikasi aktivitas mencurigakan pada kampanye.',
            'Informasi kontak tidak dapat diverifikasi.',
            'Rekening bank tidak aktif atau bermasalah.',
            'Tidak memenuhi syarat dan ketentuan penarikan dana.',
        ];

        return $reasons[array_rand($reasons)];
    }
}
