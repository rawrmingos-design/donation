<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Withdrawal;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class WithdrawalController extends Controller
{
    /**
     * Display withdrawal requests for campaign creator
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get campaigns owned by the user
        $campaigns = Campaign::where('user_id', $user->id)
            ->with(['withdrawals' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->get();

        return Inertia::render('Withdrawals/Index', [
            'campaigns' => $campaigns
        ]);
    }

    /**
     * Show withdrawal request form
     */
    public function create(Campaign $campaign)
    {
        // Check if user owns the campaign
        if ($campaign->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Check if campaign has funds to withdraw
        if ($campaign->collected_amount <= 0) {
            return redirect()->back()->with('error', 'Tidak ada dana yang dapat ditarik dari kampanye ini.');
        }

        // Check if there's already a pending withdrawal
        $pendingWithdrawal = $campaign->withdrawals()
            ->whereIn('status', [Withdrawal::STATUS_PENDING, Withdrawal::STATUS_APPROVED, Withdrawal::STATUS_PROCESSING])
            ->first();

        if ($pendingWithdrawal) {
            return redirect()->back()->with('error', 'Masih ada permintaan penarikan yang sedang diproses.');
        }

        return Inertia::render('Withdrawals/Create', [
            'campaign' => $campaign->load('withdrawals')
        ]);
    }

    /**
     * Store withdrawal request
     */
    public function store(Request $request, Campaign $campaign)
    {
        // Check if user owns the campaign
        if ($campaign->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        

        // Convert collected amount from cents to rupiah for validation
        $availableAmount = $campaign->collected_amount;
        
        // Build validation rules and messages based on method
        $rules = [
            'amount' => [
                'required',
                'numeric',
                'min:50000', // 50k in rupiah
                'max:' . $availableAmount,
            ],
            'method' => 'required|in:bank_transfer,e_wallet',
            'account_info' => 'required|array',
            'account_info.account_name' => 'required|string|max:255',
        ];

        $messages = [
            'amount.required' => 'Jumlah penarikan harus diisi.',
            'amount.numeric' => 'Jumlah penarikan harus berupa angka.',
            'amount.min' => 'Jumlah penarikan minimal Rp 50.000.',
            'amount.max' => 'Jumlah penarikan tidak boleh melebihi dana yang terkumpul.',
            'method.required' => 'Metode penarikan harus dipilih.',
            'account_info.account_name.required' => 'Nama pemilik rekening harus diisi.',
        ];

        // Add method-specific validation rules and messages
        if ($request->method === 'bank_transfer') {
            $rules['account_info.bank_name'] = 'required|string|max:255';
            $rules['account_info.account_number'] = 'required|string|max:50';
            
            $messages['account_info.bank_name.required'] = 'Nama bank harus diisi.';
            $messages['account_info.account_number.required'] = 'Nomor rekening harus diisi.';
        } elseif ($request->method === 'e_wallet') {
            $rules['account_info.wallet_type'] = 'required|string|in:gopay,ovo,dana,linkaja';
            $rules['account_info.phone_number'] = 'required|string|max:20';
            
            $messages['account_info.wallet_type.required'] = 'Jenis e-wallet harus dipilih.';
            $messages['account_info.phone_number.required'] = 'Nomor telepon harus diisi.';
        }

        // Validate the request
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check for existing pending withdrawal
        $pendingWithdrawal = $campaign->withdrawals()
            ->whereIn('status', [Withdrawal::STATUS_PENDING, Withdrawal::STATUS_APPROVED, Withdrawal::STATUS_PROCESSING])
            ->first();

        if ($pendingWithdrawal) {
            return redirect()->back()->with('error', 'Masih ada permintaan penarikan yang sedang diproses.');
        }

        try {
            $amount = (int) $request->amount; // Amount in rupiah (no conversion needed)
            $feeAmount = Withdrawal::calculateFee($amount);
            $netAmount = $amount - $feeAmount;

            // Create withdrawal request
            $withdrawal = Withdrawal::create([
                'campaign_id' => $campaign->id,
                'amount' => $amount,
                'fee_amount' => $feeAmount,
                'net_amount' => $netAmount,
                'method' => $request->method,
                'account_info' => $request->account_info,
                'status' => Withdrawal::STATUS_PENDING,
                'requested_at' => now(),
            ]);

            // Send notification to admins
            app(NotificationService::class)->sendAdminWithdrawalNotification($withdrawal);

            return redirect()->route('withdrawals.show', $withdrawal)
                ->with('success', 'Permintaan penarikan berhasil diajukan. Tim kami akan memproses dalam 1-3 hari kerja.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengajukan permintaan penarikan.')
                ->withInput();
        }
    }

    /**
     * Show withdrawal details
     */
    public function show(Withdrawal $withdrawal)
    {
        // Check if user owns the campaign
        if ($withdrawal->campaign->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return Inertia::render('Withdrawals/Show', [
            'withdrawal' => $withdrawal->load(['campaign', 'approvedBy'])
        ]);
    }

    /**
     * Cancel withdrawal request (only if pending)
     */
    public function cancel(Withdrawal $withdrawal)
    {
        // Check if user owns the campaign
        if ($withdrawal->campaign->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if (!$withdrawal->canBeCancelled()) {
            return redirect()->back()->with('error', 'Permintaan penarikan tidak dapat dibatalkan.');
        }

        try {
            $withdrawal->update([
                'status' => Withdrawal::STATUS_CANCELLED,
                'notes' => 'Dibatalkan oleh creator kampanye'
            ]);

            return redirect()->route('withdrawals.index')
                ->with('success', 'Permintaan penarikan berhasil dibatalkan.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membatalkan permintaan.');
        }
    }
}
