<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        return match ($user->role) {
            User::ROLE_ADMIN => redirect()->route('admin.dashboard'),
            User::ROLE_DOSEN => redirect()->route('dosen.dashboard'),
            User::ROLE_KEUANGAN => redirect()->route('keuangan.dashboard'),
            default => redirect()->route('mahasiswa.dashboard'),
        };
    }
}
