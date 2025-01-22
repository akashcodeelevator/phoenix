<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class GenealogyController extends Controller
{
    public function generationform()
    {
        $user = Auth::user();
        return view('user.generation', compact('user'));
    }
    public function generation(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        // Retrieve users grouped by levels
        $userGenerations = $this->myGeneration($userId);

        // Flatten and preserve levels
        $userLevels = collect($userGenerations)->flatMap(function ($users, $level) {
            return collect($users)->map(function ($userId) use ($level) {
                return ['id' => $userId, 'level' => "Level $level"]; // Add string to level
            });
        });

        // Get user details along with levels
        $users = User::query()
            ->leftJoin('orders', 'users.id', '=', 'orders.u_code') // Correct column name
            ->leftJoin('pin_details', function ($join) {
                $join->on('orders.package_type', '=', 'pin_details.id')
                    ->orWhere('orders.u_code', '=', 'users.id')
                    ->orWhere('pin_details.id', '=', 1); // Default pin_type if no order record exists
            })
            ->whereIn('users.id', $userLevels->pluck('id')) // Filter by generated user IDs
            ->select(
                'users.*',
                'pin_details.pin_type as package_type' // Fetch pin_type
            )
            ->get();

        // Map levels to users and concatenate levels
        $users = $users->map(function ($user) use ($userLevels) {
            // Get all levels for the user
            $levels = $userLevels->where('id', $user->id)->pluck('level')->toArray();

            // Concatenate levels into a comma-separated string
            $user->level = implode(', ', $levels);
            return $user;
        });

        return DataTables::of($users)
            ->addColumn('status', function ($user) {
                return $user->status ? 'Active' : 'Inactive'; // Format the status
            })
            ->addColumn('level', function ($user) {
                return $user->level; // Return the formatted level string
            })
            ->make(true); // Return JSON for DataTables
    }

    public function myGeneration($userId)
    {
        $currentLevelUsers = [$userId]; // Initialize with the given user ID
        $allUsers = []; // Store the result grouped by levels

        $level = 1;

        while (!empty($currentLevelUsers)) {
            // Fetch all users sponsored by the current level's users
            $usersAtCurrentLevel = User::whereIn('u_sponsor', $currentLevelUsers)
                ->pluck('id')
                ->toArray();

            if (!empty($usersAtCurrentLevel)) {
                $allUsers[$level] = $usersAtCurrentLevel; // Add to the result
                $currentLevelUsers = $usersAtCurrentLevel; // Update for the next level
                $level++;
            } else {
                $currentLevelUsers = []; // Stop if no more users found
            }
        }

        // Flatten the result array into a single array
        return $allUsers;
    }

    public function teamdirectsform()
    {
        $user = Auth::user();
        return view('user.directteam', compact('user'));
    }
    public function teamdirects(Request $request)
    {
        $user = Auth::user();

        // Build the base query
        $query = User::query()
            ->leftJoin('orders', 'users.id', '=', 'orders.u_code') // Correct column name
            ->leftJoin('pin_details', function ($join) {
                $join->on('orders.package_type', '=', 'pin_details.id')
                    ->orWhere('orders.u_code', '=', 'users . id')
                    ->orWhere('pin_details.id', '=', 1); // Default pin_type if no order record exists
            })
            ->where('users.u_sponsor', $user->id) // Direct team members
            ->select(
                'users.*',
                'pin_details.pin_type as package_type' // Fetch pin_type
            );

        // Apply filters if provided
        if ($request->has('status')) {
            $query->where('users.status', $request->status);
        }

        if ($request->has('startdate') && $request->has('enddate')) {
            $query->whereBetween('users.join_date', [$request->startdate, $request->enddate]);
        }

        // Return DataTables response
        return DataTables::of($query)
            ->addColumn('status', function ($row) {
                return $row->status ? 'Active' : 'Inactive';
            })
            ->editColumn('package_type', function ($row) {
                return $row->package_type ?? 'Not Found';
            })
            ->make(true);
    }
}