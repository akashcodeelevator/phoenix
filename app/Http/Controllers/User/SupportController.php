<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Support;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class SupportController extends Controller
{
    public function supportform()
    {
        $user = Auth::user();
        return view('user.support', compact('user'));
    }
    public function support(Request $request)
    {
        try {
            // Validate the request data
            $request->validate([
                'username' => 'required',
                'email_address' => 'required',
                'description' => 'required',
            ]);

            // Retrieve user data from the session
            $user = auth()->user(); // Assuming Laravel's built-in authentication

            // Prepare support data
            $support = [
                'message' => $request->description,
                'first_name' => $request->username,
                'u_code' => $user->id,
                'email' => $request->email_address,
                'contactno' => $user->mobile,
                'ticket' => 'TK-' . strtoupper(Str::random(8)), // Generate ticket
                'status' => 0,
                'reply_status' => 0,
            ];

            // Insert into the database
            $supportModel = new Support(); // Assuming `Support` is your model
            if ($supportModel->create($support)) {
                return redirect()->back()->with('success', "{$support['ticket']} Ticket Generated successfully.");
            } else {
                return redirect()->back()->withErrors(['error' => 'Something went wrong.']);
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'An unexpected error occurred.']);
        }
    }
    public function supporthistory()
    {
        $user = auth()->user();
        // Single query to get all counts
        $count_support = Support::selectRaw('
                COUNT(CASE WHEN reply_status = 0 THEN 1 END) as notRepliedCount,
                COUNT(CASE WHEN reply_status = 1 THEN 1 END) as repliedCount,
                COUNT(*) as totalCount
            ')
            ->where('u_code', $user->id)
            ->first(); // Use `first()` to get a single result
        return view('user.supporthistory', compact('count_support'));
    }
    public function getSupportHistory(Request $request)
    {
        $user = Auth::user();

        // Define the base query for the support table
        $query = Support::where('support.u_code', $user->id) // Filter by the logged-in user's ID
            ->join('users', 'support.u_code', '=', 'users.id') // Join with the users table
            ->select('support.*', 'support.reply as replymessage', 'users.username', 'users.name', 'users.email'); // Select necessary columns

        // Apply filtering based on the status, if provided
        if ($request->status) {
            switch ($request->status) {
                case 'approve':
                    $query->where('support.status', 1); // Approved
                    break;
                case 'pending':
                    $query->where('support.status', 0); // Pending
                    break;
                case 'cancel':
                    $query->where('support.status', 2); // Cancelled
                    break;
            }
        }

        // Return the DataTable response with the query results
        return DataTables::of($query)
            ->addColumn('status', function ($row) {
                // Return the human-readable status based on the numeric value
                return $row->status == 1 ? 'Approved' : ($row->status == 2 ? 'Cancelled' : 'Pending');
            })
            ->addColumn('reply', function ($row) {
                // You can customize the reply column as needed. Here we just show 'Replied' or 'Not Replied'
                return $row->reply ? 'Replied' : 'Not Replied';
            })
            ->addColumn('created_at', function ($row) {
                // Format the created_at date to a more readable format
                return $row->created_at->format('d-m-Y H:i:s');
            })
            ->make(true);
    }
}