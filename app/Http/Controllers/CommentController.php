<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Store a new comment for a campaign
     */
    public function store(Request $request, Campaign $campaign)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus login untuk memberikan komentar.'
            ], 401);
        }

        // Validate the request
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|min:3|max:1000',
            'is_public' => 'boolean'
        ], [
            'content.required' => 'Komentar tidak boleh kosong.',
            'content.min' => 'Komentar minimal 3 karakter.',
            'content.max' => 'Komentar maksimal 1000 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create the comment
            $comment = Comment::create([
                'campaign_id' => $campaign->id,
                'user_id' => Auth::id(),
                'content' => $request->content,
                'is_public' => $request->boolean('is_public', true),
            ]);

            // Load the user relationship
            $comment->load('user');

            return response()->json([
                'success' => true,
                'message' => 'Komentar berhasil ditambahkan.',
                'comment' => [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'is_public' => $comment->is_public,
                    'created_at' => $comment->created_at->toISOString(),
                    'user' => [
                        'id' => $comment->user->id,
                        'name' => $comment->user->name,
                        'avatar' => $comment->user->avatar,
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan komentar.'
            ], 500);
        }
    }

    /**
     * Delete a comment
     */
    public function destroy(Comment $comment)
    {
        // Check if user owns the comment or is admin
        if ($comment->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk menghapus komentar ini.'
            ], 403);
        }

        try {
            $comment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Komentar berhasil dihapus.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus komentar.'
            ], 500);
        }
    }
}
