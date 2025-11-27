<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RefClass;
use App\Models\RefStudent;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Hitung Statistik
        $stats = [
            'total_users' => User::count(),
            'total_students' => RefStudent::count(),
            'total_classes' => RefClass::count(),
            'active_homerooms' => User::whereNotNull('class_id')->count(),
        ];

        // Ambil 5 User Login Terakhir (Opsional, jika ada kolom last_login)
        $latestUsers = User::latest('last_login')->take(5)->get();

        return view('superadmin.dashboard', compact('stats'));
    }
}