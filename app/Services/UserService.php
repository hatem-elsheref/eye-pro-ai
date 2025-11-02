<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserService
{
    /**
     * Update user profile
     */
    public function updateProfile(User $user, array $data): void
    {
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
        ]);
    }

    /**
     * Update user password
     */
    public function updatePassword(User $user, string $currentPassword, string $newPassword): bool
    {
        if (!Hash::check($currentPassword, $user->password)) {
            return false;
        }

        $user->update([
            'password' => Hash::make($newPassword)
        ]);

        return true;
    }

    /**
     * Delete user account and all related data
     */
    public function deleteAccount(User $user): void
    {
        // Delete all user's matches and their files
        foreach ($user->matches as $match) {
            if ($match->video_path) {
                $disk = $match->storage_disk ?? 'public';
                try {
                    if (Storage::disk($disk)->exists($match->video_path)) {
                        Storage::disk($disk)->delete($match->video_path);
                    }
                } catch (\Exception $e) {
                    // Log error but continue deletion
                    \Log::warning('Failed to delete match file', [
                        'matchId' => $match->id,
                        'path' => $match->video_path,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            $match->delete();
        }

        // Delete user
        $user->delete();
    }
}


