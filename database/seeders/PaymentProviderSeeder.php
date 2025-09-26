<?php

namespace Database\Seeders;

use App\Models\PaymentProvider;
use App\Models\PaymentChannel;
use Illuminate\Database\Seeder;

class PaymentProviderSeeder extends Seeder
{
    public function run(): void
    {
        // Create tokopay provider
        $tokopay = PaymentProvider::create([
            'name' => 'Tokopay',
            'code' => 'tokopay',
            'active' => true,
        ]);

        // Create midtrans provider
        $midtrans = PaymentProvider::create([
            'name' => 'Midtrans',
            'code' => 'midtrans',
            'active' => true,
        ]);

        // tokopay payment channels based on their documentation
        $tokopayChannels = [
            // Virtual Account
            ['code' => 'BCAVA', 'name' => 'BCA Virtual Account', 'fee_fixed' => '4200', 'fee_percentage' => '0'],
            ['code' => 'BNIVA', 'name' => 'BNI Virtual Account', 'fee_fixed' => '3500', 'fee_percentage' => '0'],
            ['code' => 'BRIVA', 'name' => 'BRI Virtual Account', 'fee_fixed' => '3000', 'fee_percentage' => '0'],
            ['code' => 'MANDIRIVA', 'name' => 'Mandiri Virtual Account', 'fee_fixed' => '3500', 'fee_percentage' => '0'],
            ['code' => 'PERMATAVA', 'name' => 'Permata Virtual Account', 'fee_fixed' => '2000', 'fee_percentage' => '0'],
            ['code' => 'CIMBVA', 'name' => 'CIMB Virtual Account', 'fee_fixed' => '2500', 'fee_percentage' => '0'],
            ['code' => 'DANAMONVA', 'name' => 'DANAMON Virtual Account', 'fee_fixed' => '2500', 'fee_percentage' => '0'],
            ['code' => 'BSIVA', 'name' => 'BSI Virtual Account', 'fee_fixed' => '3.500', 'fee_percentage' => '0'],
            ['code' => 'BNCVA', 'name' => 'BNC VA (NEO)', 'fee_fixed' => '3.000', 'fee_percentage' => '0'],
            ['code' => 'PERMATAVAA', 'name' => 'Permata Virtual Account', 'fee_fixed' => '3.000', 'fee_percentage' => '0'],
            
            // E-Wallet
            ['code' => 'GOPAY', 'name' => 'GoPay', 'fee_fixed' => '3', 'fee_percentage' => '0'],
            ['code' => 'OVO', 'name' => 'OVO', 'fee_fixed' => '2.5', 'fee_percentage' => '0'],
            ['code' => 'DANA', 'name' => 'DANA', 'fee_fixed' => '2.5', 'fee_percentage' => '0'],
            ['code' => 'LINKAJA', 'name' => 'LinkAja', 'fee_fixed' => '3', 'fee_percentage' => '0'],
            ['code' => 'SHOPEEPAY', 'name' => 'ShopeePay', 'fee_fixed' => '2.5', 'fee_percentage' => '0'],
            
            // Retail
            ['code' => 'ALFAMART', 'name' => 'Alfamart', 'fee_fixed' => '3000', 'fee_percentage' => '0'],
            ['code' => 'INDOMARET', 'name' => 'Indomaret', 'fee_fixed' => '3000', 'fee_percentage' => '0'],
            
            // QRIS
            ['code' => 'QRIS', 'name' => 'QRIS', 'fee_fixed' => '100', 'fee_percentage' => '0.70'],
            ['code' => 'QRISREALTIME', 'name' => 'QRIS Realtime', 'fee_fixed' => '0', 'fee_percentage' => '1.70'],
        ];

        foreach ($tokopayChannels as $channel) {
            PaymentChannel::create([
                'provider_id' => $tokopay->id,
                'code' => $channel['code'],
                'name' => $channel['name'],
                'fee_fixed' => $channel['fee_fixed'],
                'fee_percentage' => $channel['fee_percentage'],
                'active' => true,
            ]);
        }
    }
}
