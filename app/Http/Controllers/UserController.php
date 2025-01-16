<?php

namespace App\Http\Controllers;

use App\Models\AdvancedInfo;
use App\Models\UserAccount;
use App\Models\UserWallet;
use App\Models\WalletType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Models\OtpVerification;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/register",
     *     tags={"Login & Register"},
     *     summary="Register a new user",
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="User's name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User's email",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="User's password",
     *         required=true,
     *         @OA\Schema(type="string", format="password")
     *     ),
     *      @OA\Parameter(
     *         name="mobile",
     *         in="query",
     *         description="User's mobile number",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="referral_code",
     *         in="query",
     *         description="Referral code ",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *      @OA\Parameter(
     *         name="eth_address",
     *         in="query",
     *         description="User's Wallet Address",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="201", description="User registered successfully"),
     *     @OA\Response(response="422", description="Validation errors")
     * )
     */
    public function register(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'string',
                'email' => 'required|string|email|unique:users|max:255',
                'password' => 'required|string|min:8',
                'mobile' => 'required|string|unique:users|max:15',
                'referral_code' => 'required|string|max:255|exists:users,username',
                'eth_address' => 'required',
            ]);
            $u_id = User::where('username', 'like', $validatedData['referral_code'])->value('id');
            if (!$u_id) {
                return response()->json(['status' => false, 'message' => 'Sponsor not found'], 200);
            }

            $userGenPrefix = AdvancedInfo::where('label', 'like', 'user_gen_prefix')->value('value');
            // Generate a unique username
            $randomNumber = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $user_name = $userGenPrefix . $randomNumber;

            $user = User::create([
                'name' => $validatedData['name']??'test',
                'email' => $validatedData['email'],
                'mobile' => $validatedData['mobile'],
                'u_sponsor' => $u_id,
                'username' => $user_name,
                'password' => Hash::make($validatedData['password']),
                'eth_address' => $validatedData['eth_address'],
            ]);

            if ($u_id) {
                $reg_type = AdvancedInfo::where('label', 'like', 'reg_type')->value('value');
                $source_1 = WalletType::where('slug', 'like', 'total_directs')
                    ->where('wallet_type', 'team')
                    ->where($reg_type, 1)
                    ->value('wallet_column');

                $source_2 = WalletType::where('slug', 'like', 'inactive_directs')
                    ->where('wallet_type', 'team')
                    ->where($reg_type, 1)
                    ->value('wallet_column');
                $source_3 = WalletType::where('slug', 'like', 'total_gen')
                    ->where('wallet_type', 'team')
                    ->where($reg_type, 1)
                    ->value('wallet_column');
                // Fetch current wallet counts
                $userWallet = UserWallet::firstOrNew(['u_code' => $u_id]); // Use firstOrNew to avoid duplicate queries
                $source_1_counts = $userWallet->$source_1 ?? 0;
                $source_2_counts = $userWallet->$source_2 ?? 0;
                $source_3_counts = $userWallet->$source_3 ?? 0;
                // Update wallet countss
                $no_count = 1;
                $userWallet->$source_1 = $source_1_counts + $no_count;
                $userWallet->$source_2 = $source_2_counts + $no_count;
                $userWallet->$source_3 = $source_3_counts + $no_count;
                // Save or update record
                $userWallet->save();
            }
            $u_pss = $validatedData['password'];
            // Prepare email data
            $emailData = [
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'username' => $user_name,
                'password' => $u_pss,
            ];

            // Send welcome email
            Mail::send('emails.welcome', $emailData, function ($message) use ($validatedData) {
                $message->to($validatedData['email'])
                    ->subject('Welcome to Our LEAT');
            });

            return response()->json(['status' => true, 'message' => "User registered successfully. Username : $user_name Password : $u_pss"], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => false, 'message' => $e->errors()], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An unexpected error occurred. Please try again later.'], 200);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/user",
     *     tags={"User"},
     *     summary="Get logged-in user details",
     *     @OA\Response(response="200", description="Success"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function getUserDetails(Request $request)
    {
        try {
            $user = $request->user();

            return response()->json(['status' => true, 'user' => $user], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => false, 'errors' => $e->errors()], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An unexpected error occurred. Please try again later.'], 200);
        }
    }

    /**
     * Change user password
     * 
     * @OA\Post(
     *     path="/api/v1/change-password",
     *     tags={"User"}, 
     *     summary="Change user password",
     *      @OA\Parameter(
     *         name="current_password",
     *         in="query",
     *         description="User's Current password",
     *         required=true,
     *         @OA\Schema(type="string", format="password")
     *      ),
     *      @OA\Parameter(
     *         name="new_password",
     *         in="query",
     *         description="User's New password",
     *         required=true,
     *         @OA\Schema(type="string", format="password")
     *      ),
     *      @OA\Parameter(
     *         name="new_password_confirmation",
     *         in="query",
     *         description="User's New password confirmation",
     *         required=true,
     *         @OA\Schema(type="string", format="password")
     *      ),
     * 
     *     @OA\Response(response="200", description="Password updated successfully"),
     *     @OA\Response(response="400", description="Bad request"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function changePassword(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string|min:8',
            'new_password' => 'required|string|min:8|confirmed', // Ensure "new_password_confirmation" exists
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'error' => $validator->errors()], 200);
        }
        if ($request->current_password == $request->new_password) {
            return response()->json(['status' => false, 'errors' => 'The new password must be different from the old password.'], 200);
        }
        // Get the authenticated user
        $user = $request->user();

        // Check if the current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['status' => false, 'error' => 'Current password is incorrect.'], 200);
        }

        // Update the password
        $user->password = Hash::make($request->new_password);
        $user->save();
        $request->user()->tokens->each(function ($token) {
            $token->delete();
        });
        return response()->json(['status' => true, 'message' => 'Password updated successfully.'], 200);
    }
    /**
     * Logout user
     * 
     * @OA\Post(
     *     path="/api/v1/logout",
     *     tags={"User"},
     *     summary="Logout user",
     *     @OA\Response(response="200", description="Successfully logged out"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function logout(Request $request)
    {
        // Revoke the user's token (the current token used for authentication)
        $request->user()->tokens->each(function ($token) {
            $token->delete();
        });

        // Return a success response
        return response()->json(['status' => true, 'message' => 'Successfully logged out.'], 200);
    }
    /**
     * @OA\Post(
     *     path="/api/v1/profile-update",
     *     tags={"User"}, 
     *     summary="Update user profile",
     *     description="Update the authenticated user's name and profile image.",
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="User's full name",
     *         required=false,
     *         @OA\Schema(type="string", maxLength=255)
     *     ),
     *     @OA\Parameter(
     *         name="pic_image",
     *         in="query",
     *         required=false,
     *         description="Uploaded image of the payment slip",
     *         @OA\Schema(type="string", format="binary")
     *     ),
     *     @OA\Response(response="200", description="Profile updated successfully"),
     *     @OA\Response(response="400", description="Bad request"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function updateUserProfile(Request $request)
    {
        try {
            // Validate the input
            $validator = Validator::make($request->all(), [
                'name' => 'string|max:255',
                'pic_image' => 'file|mimes:jpg,jpeg,png|max:2048',
            ]);


            if ($validator->fails()) {
                return response()->json(['status' => false, 'errors' => $validator->errors()], 200);
            }

            //$user = Auth::user(); // Get the authenticated user
            $user = $request->user();
            // Update name
            if ($request->filled('name')) {
                $user->name = $request->name;
            }

            if ($request->hasFile('pic_image')) {
                $file = $request->file('pic_image');
                $filePath = $file->store('public/profile');
                if ($filePath) {
                    $user->img = asset('storage/' . str_replace('public/', '', $filePath));
                }
            }

            // Save changes to the user model
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Profile updated successfully',
                'user' => $user,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => false, 'errors' => $e->errors()], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An unexpected error occurred. Please try again later.'], 200);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/v1/kyc-verification/{accountId?}",
     *     tags={"User"},
     *     summary="Create or Update KYC information for a user",
     *     description="Create or update KYC (Know Your Customer) information for the authenticated user. If `accountId` is provided, the existing account will be updated, otherwise, a new KYC record will be created.",
     *     @OA\Parameter(
     *         name="accountId",
     *         in="path",
     *         required=false,
     *         @OA\Schema(type="integer", example=1),
     *         description="The ID of the user account to update. If not provided, a new KYC record will be created."
     *     ),
     *     @OA\Parameter(
     *         name="document_type",
     *         in="query",
     *         required=true,
     *         description="Type of document for KYC verification (pan, adhaar, or both).",
     *         @OA\Schema(type="string", enum={"pan", "adhaar", "both"}, example="pan")
     *     ),
     *     @OA\Parameter(
     *         name="pan_no",
     *         in="query",
     *         required=false,
     *         description="PAN number for KYC verification.",
     *         @OA\Schema(type="string", example="ABCDE1234F")
     *     ),
     *     @OA\Parameter(
     *         name="pan_image",
     *         in="query",
     *         required=false,
     *         description="Uploaded PAN image for KYC verification.",
     *         @OA\Schema(type="string", format="binary")
     *     ),
     *     @OA\Parameter(
     *         name="adhaar_no",
     *         in="query",
     *         required=false,
     *         description="Aadhaar number for KYC verification.",
     *         @OA\Schema(type="string", example="1234-5678-9123")
     *     ),
     *     @OA\Parameter(
     *         name="adhaar_image",
     *         in="query",
     *         required=false,
     *         description="Uploaded Aadhaar front image for KYC verification.",
     *         @OA\Schema(type="string", format="binary")
     *     ),
     *     @OA\Parameter(
     *         name="adhaar_back_image",
     *         in="query",
     *         required=false,
     *         description="Uploaded Aadhaar back image for KYC verification.",
     *         @OA\Schema(type="string", format="binary")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="KYC information updated successfully.",
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Bad request, validation failed.",
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="User account not found.",
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function saveKyc(Request $request, $accountId = null)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'document_type' => 'required|in:pan,adhaar,both',
            'pan_no' => 'nullable|string',
            'pan_image' => 'nullable|image',
            'adhaar_no' => 'nullable|string',
            'adhaar_image' => 'nullable|image',
            'adhaar_back_image' => 'nullable|image',
        ]);

        $user = $request->user();
        $accountId = $accountId ?? UserAccount::where('u_code', $user->id)->value('id');
        //dd($user->id);


        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(),], 200);
        }

        // Handle file uploads
        $validated = $request->all();

        if ($request->hasFile('pan_image')) {
            $validated['pan_image'] = $request->file('pan_image')->store('uploads/kyc');
        }
        if ($request->hasFile('adhaar_image')) {
            $validated['adhaar_image'] = $request->file('adhaar_image')->store('uploads/kyc');
        }
        if ($request->hasFile('adhaar_back_image')) {
            $validated['adhaar_back_image'] = $request->file('adhaar_back_image')->store('uploads/kyc');
        }

        // Account type mapping
        $validated['account_type'] = $validated['document_type'];
        $validated['kyc_status'] = 'pending';
        $validated['u_code'] = $user->id;
        unset($validated['document_type']);

        // If accountId is provided, update existing account, otherwise create new account
        if ($accountId) {
            // Find and update the user account
            $userAccount = UserAccount::find($accountId);
            if (!$userAccount) {
                return response()->json([
                    'error' => 'User account not found.',
                ], 200);
            }

            $userAccount->update($validated);
            $message = 'User account updated successfully!';
        } else {
            // Create new user account
            $userAccount = UserAccount::create($validated);
            $message = 'User account created successfully!';
        }
        // Prepare file URLs
        if ($userAccount->pan_image) {
            $userAccount->pan_image = asset('../storage/app/' . $userAccount->pan_image);
        }
        if ($userAccount->adhaar_image) {
            $userAccount->adhaar_image = asset('../storage/app/' . $userAccount->adhaar_image);
        }
        if ($userAccount->adhaar_back_image) {
            $userAccount->adhaar_back_image = asset('../storage/app/' . $userAccount->adhaar_back_image);
        }
        // Return response
        return response()->json(['success' => true, 'message' => $message, 'data' => $userAccount,], 200);
    }
    /**
     * @OA\Get(
     *     path="/api/v1/get_kyc",
     *     tags={"User"},
     *     summary="Get logged-in user Kyc details",
     *     @OA\Response(response="200", description="Success"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function getKycDetails(Request $request)
    {
        try {
            $user = $request->user();
            $kyc_data = UserAccount::where('u_code', $user->id)->first();
            if ($kyc_data) {
                return response()->json(['status' => true, 'kyc_detail' => $kyc_data], 200);
            } else {
                return response()->json(['status' => false, 'message' => 'kyc Detail not found'], 200);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => false, 'errors' => $e->errors()], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An unexpected error occurred. Please try again later.'], 200);
        }
    }
    /** 
     * @OA\Post(
     *     path="/api/v1/email-send",
     *     tags={"Login & Register"},
     *     summary="Send an email with OTP for verification",
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User's email address to send the OTP to",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="Email sent successfully"),
     * )
     */
    public function email_send(Request $request)
    {
        try {
            // Validate the email input
            $validator = Validator::make($request->all(), [
                'email' => 'required|email', // Ensure the email exists in the users table
            ]);

            // Check if the email exists in the users table
            $user = User::where('email', $request->email)->first();

            // If the user exists and auto_register is set to 1, skip OTP sending
            if ($user && $user->auto_register == 1) {
                return response()->json(['message' => 'User already registered with email,please try with login'], 200);
            }
            // Generate a random OTP
            $otp = rand(100000, 999999);

            // Store OTP in the database
            OtpVerification::updateOrCreate(
                ['email' => $request->email],
                [
                    'otp' => $otp,
                    'expires_at' => Carbon::now()->addMinutes(5),
                ]
            );

            // Prepare email data
            $emailData = [
                'otp' => $otp,
                'email' => $request->email,
            ];

            // Send the email using a Blade template
            Mail::send('emails.otp', $emailData, function ($message) use ($request) {
                $message->to($request->email)
                    ->subject('Your OTP Code');
            });

            return response()->json([
                'status' => true,
                'message' => 'OTP sent successfully to ' . $request->email,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => false, 'message' => $e->errors()], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An unexpected error occurred. Please try again later.'], 200);
        }
    }
    /** 
     * @OA\Post(
     *     path="/api/v1/otp-verify",
     *     tags={"Login & Register"},
     *     summary="Verify the OTP sent to user's email",
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User's email address",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="otp",
     *         in="query",
     *         description="OTP received by the user on their email",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="OTP verified successfully"),
     * )
     */

    public function verify_otp(Request $request)
    {
        try {
            // Validate the input
            $request->validate([
                'email' => 'required|email',
                'otp' => 'required|numeric',
            ]);

            // Retrieve the OTP from the database
            $otpRecord = OtpVerification::where('email', $request->email)->first();

            if (!$otpRecord) {
                return response()->json([
                    'status' => false,
                    'message' => 'OTP not found.',
                ], 200);
            }

            // Check if the OTP is expired
            if (Carbon::now()->greaterThan($otpRecord->expires_at)) {
                return response()->json([
                    'status' => false,
                    'message' => 'OTP has expired.',
                ], 200);
            }

            // Check if the OTP matches
            if ($request->otp == $otpRecord->otp) {
                // Delete the OTP record after successful verification
                $otpRecord->delete();

                return response()->json([
                    'status' => true,
                    'message' => 'OTP verified successfully.',
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Invalid OTP.',
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => false, 'message' => $e->errors()], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An unexpected error occurred. Please try again later.'], 200);
        }
    }
}