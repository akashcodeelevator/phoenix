<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserAccount;
use Illuminate\Http\Request;

class UserAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userAccounts = UserAccount::all();
        return view('admin.user_accounts.index', compact('userAccounts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users= User::all();
        return view('admin.user_accounts.create',compact('users'));
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
            'u_code' => 'required|unique:users,id',
            'kyc_status' => 'required',
            'document_type' => 'required',
            'front_image_pan' => 'nullable|image',
            'pan_no' => 'nullable|string',
            'pan_image' => 'nullable|image',
            'adhaar_no' => 'nullable|string',
            'adhaar_image' => 'nullable|image',
            'adhaar_back_image' => 'nullable|image',
        ]);

        // Handle file uploads
        if ($request->hasFile('front_image_pan')) {
            $validated['front_image_pan'] = $request->file('front_image_pan')->store('uploads/kyc');
        }
        if ($request->hasFile('pan_image')) {
            $validated['pan_image'] = $request->file('pan_image')->store('uploads/kyc');
        }
        if ($request->hasFile('adhaar_image')) {
            $validated['adhaar_image'] = $request->file('adhaar_image')->store('uploads/kyc');
        }
        if ($request->hasFile('adhaar_back_image')) {
            $validated['adhaar_back_image'] = $request->file('adhaar_back_image')->store('uploads/kyc');
        }
        $validated['account_type']=$validated['document_type'];
        unset($validated['document_type']);
        UserAccount::create($validated);
        return redirect()->route('admin.user_accounts.index')->with('success', 'User account created successfully!');
    
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Retrieve the user account by its ID
        $userAccount = UserAccount::findOrFail($id);

        // Fetch all users for the dropdown
        $users = User::select('id', 'name', 'username')->get();

        // Return the edit view with the user account and users
        return view('admin.user_accounts.edit', compact('userAccount', 'users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserAccount $userAccount)
    {
        $validated = $request->validate([
            'kyc_status' => 'required',
            'document_type' => 'required',
            'front_image_pan' => 'nullable|image',
            'pan_no' => 'nullable|string',
            'pan_image' => 'nullable|image',
            'adhaar_no' => 'nullable|string',
            'adhaar_image' => 'nullable|image',
            'adhaar_back_image' => 'nullable|image',
        ]);

        // Handle file uploads and keep existing files if no new files are uploaded
        if ($request->hasFile('front_image_pan')) {
            $validated['front_image_pan'] = $request->file('front_image_pan')->store('uploads/kyc');
        } else {
            $validated['front_image_pan'] = $userAccount->front_image_pan;
        }

        if ($request->hasFile('pan_image')) {
            $validated['pan_image'] = $request->file('pan_image')->store('uploads/kyc');
        } else {
            $validated['pan_image'] = $userAccount->pan_image;
        }

        if ($request->hasFile('adhaar_image')) {
            $validated['adhaar_image'] = $request->file('adhaar_image')->store('uploads/kyc');
        } else {
            $validated['adhaar_image'] = $userAccount->adhaar_image;
        }

        if ($request->hasFile('adhaar_back_image')) {
            $validated['adhaar_back_image'] = $request->file('adhaar_back_image')->store('uploads/kyc');
        } else {
            $validated['adhaar_back_image'] = $userAccount->adhaar_back_image;
        }

        // Map document_type to account_type
        $validated['account_type'] = $validated['document_type'];
        $validated['kyc_remark'] =$request->kyc_remark;
        unset($validated['document_type']);

        $userAccount->update($validated);

        return redirect()->route('admin.user_accounts.index')->with('success', 'User account updated successfully!');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserAccount $userAccount)
    {
        $userAccount->delete();
        return redirect()->route('admin.user_accounts.index')->with('success', 'User account deleted successfully!');
    }
}
