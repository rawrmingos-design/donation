<?php

namespace App\Http\Controllers;

use App\Models\FundraiserApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class FundraiserApplicationController extends Controller
{
    public function index()
    {
        $application = FundraiserApplication::where('user_id', Auth::id())->first();
        
        return Inertia::render('fundraiser/application', [
            'application' => $application,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'id_card_number' => 'required|string|max:20',
            'id_card_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'motivation' => 'required|string|max:1000',
            'experience' => 'nullable|string|max:1000',
            'social_media_links' => 'nullable|string|max:500',
        ]);

        // Check if user already has an application
        $existingApplication = FundraiserApplication::where('user_id', Auth::id())->first();
        
        if ($existingApplication) {
            return back()->withErrors(['general' => 'Anda sudah memiliki pengajuan yang sedang diproses.']);
        }

        $data = $request->all();
        $data['user_id'] = Auth::id();

        // Handle file upload
        if ($request->hasFile('id_card_photo')) {
            $data['id_card_photo'] = $request->file('id_card_photo')->store('id-cards', 'public');
        }

        FundraiserApplication::create($data);

        return redirect()->route('fundraiser.application')->with('success', 'Pengajuan Anda telah berhasil dikirim dan sedang dalam proses review.');
    }

    public function update(Request $request, FundraiserApplication $application)
    {
        // Only allow user to update their own pending application
        if ($application->user_id !== Auth::id() || !$application->isPending()) {
            abort(403);
        }

        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'id_card_number' => 'required|string|max:20',
            'id_card_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'motivation' => 'required|string|max:1000',
            'experience' => 'nullable|string|max:1000',
            'social_media_links' => 'nullable|string|max:500',
        ]);

        $data = $request->all();

        // Handle file upload
        if ($request->hasFile('id_card_photo')) {
            // Delete old file if exists
            if ($application->id_card_photo) {
                Storage::disk('public')->delete($application->id_card_photo);
            }
            $data['id_card_photo'] = $request->file('id_card_photo')->store('id-cards', 'public');
        }

        $application->update($data);

        return redirect()->route('fundraiser.application')->with('success', 'Pengajuan Anda telah berhasil diperbarui.');
    }
}
