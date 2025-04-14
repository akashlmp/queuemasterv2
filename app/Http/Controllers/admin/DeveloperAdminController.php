<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\admin\DeveloperScript;
use App\Models\QueuedbUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Page;


class DeveloperAdminController extends Controller
{
    // public function index()
    // {
    //     // Fetch script from database
    //     $developerScript = DeveloperScript::firstOrNew(['id' => 1]);
    //     $script = $developerScript->developer_script;

    //     return view('admin.developer', compact('script'));
    // }

    public function index()
    {
        // Fetch script from the database
        // Consider using a more descriptive variable name than $developerScript
        $script = DeveloperScript::firstOrNew(['id' => 1])->developer_script;

        return view('admin.developer', compact('script'));
    }


    // public function updateScript(Request $request)
    // {
    //     // Validate the request
    //     $request->validate([
    //         'script' => 'required|string',
    //     ]);

    //     // Update the script in the database
    //     $developerScript = DeveloperScript::firstOrNew(['id' => 1]); // Assuming you only have one record in this table
    //     $developerScript->developer_script = $request->input('script');
    //     $developerScript->save();

    //     Session::flash('success', '<i class="fa fa-check-circle"></i> Script updated successfully');

    //     // return response()->json(['success' => true, 'message' => 'Script updated successfully']);

    //     return redirect()->route('developers-index');
    // }

    public function updateScript(Request $request)
    {
        // Validate the request
        $request->validate([
            'script' => 'required|string',
        ]);

        // Update the script in the database
        $developerScript = DeveloperScript::firstOrNew(['id' => 1]); // Assuming you only have one record in this table
        $developerScript->developer_script = $request->input('script');
        $developerScript->save();

        Session::flash('success', '<i class="fa fa-check-circle"></i> Script updated successfully');

        // return response()->json(['success' => true, 'message' => 'Script updated successfully']);

        return redirect()->route('developers-index');
    }


    // public function logout(Request $request)
    // {
    //     Auth::logout();
    //     $request->session()->invalidate();
    //     $request->session()->regenerateToken();
    //     return redirect()->route('login')
    //         ->withSuccess('You have logged out successfully!');
    // }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')
            ->withSuccess('You have logged out successfully!');
    }


    // public function profileUpdate()
    // {
    //     $user = Auth::user();

    //     if ($user) {
    //         $companyName = $user->company_name;
    //         $email = $user->email;

    //         return view('admin.profile', compact('companyName', 'email'));
    //     }
    //     return redirect()->route('login');
    // }

    public function profileUpdate()
    {
        $user = Auth::user();

        if ($user) {
            $companyName = $user->company_name;
            $email = $user->email;

            return view('admin.profile', compact('companyName', 'email'));
        }

        return redirect()->route('login');
    }

    // public function adminprofileUpdate(Request $request)
    // {
    //     // Validate the input
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email|max:255',
    //         'password' => 'nullable|string|min:8|regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/',
    //     ]);

    //     // Retrieve the user ID or any unique identifier for the user
    //     $userId = auth()->user()->id; // Assuming you're using Laravel's built-in authentication system

    //     // Update the user's profile
    //     $user = QueuedbUser::find($userId);
    //     $user->company_name = $request->input('name');
    //     $user->email = $request->input('email');

    //     // Check if the password field is not empty
    //     if ($request->filled('password')) {
    //         $user->password = bcrypt($request->input('password')); // Encrypt the password before storing
    //     }

    //     $user->save();

    //     // Redirect or return a response as needed
    //     return redirect('admin/admin-index')->with('success', 'Profile updated successfully!');
    // }

    public function adminprofileUpdate(Request $request)
    {
        // Validate the input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'nullable|string|min:8|regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/',
        ]);
    
        // Retrieve the authenticated user's ID
        $userId = auth()->user()->id;
    
        // Update the user's profile
        $user = QueuedbUser::findOrFail($userId);
        $user->company_name = $request->input('name');
        $user->email = $request->input('email');
    
        // Update the password if provided
        if ($request->filled('password')) {
            $user->password = bcrypt($request->input('password'));
        }
    
        $user->save();
    
        // Redirect to the appropriate route
        return redirect()->route('admin-index')->with('success', 'Profile updated successfully!');
    }


    /** start by M */
    /** This funciton using for render termsOfUse page */
    public function addTermOfUse()
    {
        $termsOfUse = Page::where('slug', 'terms-of-use')
                      ->where('status', 1)
                      ->first();
                      

    // Pass the data to the view
    return view('admin.addTermOfUse', compact('termsOfUse'));
    }

   public function addTermOfUsesave(Request $request)
{
    // Validate the form data
    $request->validate([
        'title' => 'required|string|max:255',
        'slug' => 'required|string|max:255',
        'textData' => 'required|string',
    ]);

    // Check if the page with the same slug exists, and update it, otherwise create a new one
    Page::updateOrCreate(
        ['slug' => $request->input('slug')], // Search condition: slug must match
        [
            'name' => $request->input('title'), // Data to insert/update
            'page_data' => $request->input('textData'),
            'status' => 1, // Set default status as active (true)
        ]
    );

    // Redirect or return a response
    return redirect()->back()->with('success', 'Page saved or updated successfully!');
}

 public function addPrivacyPolicy()
    {
        $privacyPolicy = Page::where('slug', 'privacy-policy')
                      ->where('status', 1)
                      ->first();
                      

    // Pass the data to the view
    return view('admin.addPrivacyPolicy', compact('privacyPolicy'));
    }

   public function addPrivacyPolicysave(Request $request)
{
    // Validate the form data
    $request->validate([
        'title' => 'required|string|max:255',
        'slug' => 'required|string|max:255',
        'textData' => 'required|string',
    ]);

    // Check if the page with the same slug exists, and update it, otherwise create a new one
    Page::updateOrCreate(
        ['slug' => $request->input('slug')], // Search condition: slug must match
        [
            'name' => $request->input('title'), // Data to insert/update
            'page_data' => $request->input('textData'),
            'status' => 1, // Set default status as active (true)
        ]
    );

    // Redirect or return a response
    return redirect()->back()->with('success', 'Page saved or updated successfully!');
}


}
