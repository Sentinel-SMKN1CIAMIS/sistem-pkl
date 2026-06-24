<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        // Filter by Search (Username, IP, Description, Action, Location)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQ) use ($search) {
                      $userQ->where('username', 'like', "%{$search}%")
                           ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by Action Type
        if ($request->filled('action_type')) {
            $query->where('action', $request->action_type);
        }

        // Filter by Role
        if ($request->filled('role')) {
            $role = $request->role;
            if ($role === 'system') {
                $query->whereNull('user_id');
            } else {
                $query->whereHas('user', function($q) use ($role) {
                    $q->where('role', $role);
                });
            }
        }

        // Filter by Start Date
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        // Filter by End Date
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $logs = $query->latest()->paginate(20)->withQueryString();

        return view('admin.logs.index', compact('logs'));
    }
}
