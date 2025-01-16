<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancedInfo;
use App\Models\User;
use App\Models\UserWallet;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
           // Fetch users with server-side pagination
           $users = User::query()
           ->when($request->search, function ($query, $search) {
               $query->where('name', 'like', "%{$search}%")
                     ->orWhere('username', 'like', "%{$search}%")
                     ->orWhere('mobile', 'like', "%{$search}%")
                     ->orWhere('email', 'like', "%{$search}%");
           })
           ->paginate(10);

       return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users|max:255',
            'password' => 'required|string|min:8|confirmed',
            'mobile' => 'required|string|unique:users|max:15',
            'referral_code' => 'required|string|max:255|exists:users,username',
        ]);
        $userGenPrefix = AdvancedInfo::where('label', 'like', 'user_gen_prefix')->value('value');
        // Generate a unique username
        $randomNumber = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $user_name = $userGenPrefix . $randomNumber;
        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'mobile'=>$validated['mobile'],
            'active_status'=>$validated['active_status']??0,            
            'password' => bcrypt($validated['password']),
            'username' =>$user_name,
        ]);

        return redirect()->route('admin.users.index')
                         ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$user->id}",
            'mobile' => "required|string|max:15|unique:users,mobile,{$user->id}",
            'active_status'=>'nullable',
            'eth_address'=>'nullable',
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.index')
                         ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')
                         ->with('success', 'User deleted successfully.');
    }

    public function getUserData($username)
    {
        $user = User::where('username', $username)->first();        
        if ($user) {
            $userWallet = UserWallet::where('u_code', $user->id)->first();
            return response()->json([
                'success' => true,
                'name' => $user->name,
                'wallets' => [
                    'MainWallet' => $userWallet->c1,
                    'FundWallet' => $userWallet->c2,
                ],
            ]);
        } else {
            return response()->json(['success' => false], 200);
        }
    }
}
