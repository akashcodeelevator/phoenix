<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/login",
     *     tags={"Login & Register"},
     *     summary="Authenticate user and generate JWT token",
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User's email Or UserName",
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
     *     @OA\Response(response="200", description="Login successful"),
     *     @OA\Response(response="401", description="Invalid credentials")
     * )
     */
    public function login(Request $request)
    {
        // Validate the incoming request to ensure we have either a username or email and password
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',  // Accept either username or email
            'password' => 'required|string',

        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'error' => $validator->errors()], 200);
        }

        // Check if the input is an email or username and adjust the authentication process
        $credentials = [
            'password' => $request->password,
        ];
        // Check if the input is an email or username
        if (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $credentials['email'] = $request->email; // Email input
        } else {
            $credentials['username'] = $request->email; // Username input
        }
        if (Auth::attempt($credentials)) {
            $token = Auth::user()->createToken('api_token')->plainTextToken;
            return response()->json(['status' => true, 'token' => $token], 200);
        }

        return response()->json(['status' => false, 'error' => 'Invalid credentials'], 200);
    }
    /**
     * @OA\Get(
     *     path="/api/v1/verify_wallet",
     *     tags={"Login & Register"},
     *     summary="Validate user's wallet address for a specific network",
     *     @OA\Parameter(
     *         name="wallet_address",
     *         in="query",
     *         description="User's wallet address",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Address validation result",
     *         @OA\JsonContent(
     *             @OA\Property(property="valid", type="boolean", description="Validation status"),
     *             @OA\Property(property="message", type="string", description="Validation message")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function validateWallet(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'wallet_address' => 'required|string',
        ]);

        // Retrieve the wallet address
        $walletAddress = $request->input('wallet_address');

        // Validate the wallet address using the correct regex
        $isValid = preg_match('/^0x[a-fA-F0-9]{40}$/', $walletAddress);

        if ($isValid) {
            return response()->json([
                'valid' => true,
                'message' => 'Address is valid.',
            ], 200);
        } else {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid wallet address.',
            ], 200);
        }
    }
}
