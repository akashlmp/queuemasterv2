<?php

namespace App\Http\Controllers\Auth;

// use App\Http\Controllers\Auth\View; // Import the correct View class

use App\Http\Controllers\Controller;
use App\Models\QueuedbUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use DB;
use Illuminate\Support\Facades\Route;
use Log;

class LoginRegisterController extends Controller
{
    /**
     * Instantiate a new LoginRegisterController instance.
     */
    public function __construct()
    {
        $this->middleware('guest')->except([
            'logout', 'dashboard',
        ]);
        $this->middleware('auth.user')->except([
            'register',
            'store',
            'login',
            'authenticate',
        ]);

        if (Auth::check())  
        { // role == 99 is Admin role id
            if (Auth::user()->role == 99)
            {
            return redirect()->route('admin-index');
            }
            else
            {
               return redirect()->route('dashboard');  
            }
        }
       
        
    }

    /**
     * Display a registration form.
     *
     * @return \Illuminate\Http\Response
     */

    /** This function checks if the email is registered. | start */
    public function checkEmail(Request $request)
    {
        $email = $request->email;
        $exists = QueuedbUser::where('email', $email)->count();
        if ($exists > 0) {
            return true;
        } else {
            return false;
        }
        
        // return response()->json(['exists' => $exists]);
    }
    /** This function checks if the email is registered. | end */

    public function register()
    {
        return view('auth-profile.register');
    }

    public function resend($user_id)
    {
        return view('auth-profile.resend', ['user_id' => $user_id]);
    }

    /**
     * Store a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */

    // public function store(Request $request)
    // {
    //     try {
    //         $passwordRule = [
    //             'required',
    //             'min:8',
    //             'regex:/^(?=.*[a-zA-Z0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]+$/',
    //         ];

    //         $data = $request->validate([
    //             'email' => 'required|email|max:250|unique:queuetb_users',
    //             'password' => $passwordRule,
    //             'accept_terms' => 'accepted',
    //         ], [
    //             'email.required' => 'The email field is required.',
    //             'email.email' => 'Please enter a valid email address.',
    //             'email.max' => 'The email must not exceed :max characters.',
    //             'email.unique' => 'The email has already been taken.',
    //             'password.required' => 'The password field is required.',
    //             'password.min' => 'The password must be at least :min characters long.',
    //             'password.regex' => 'The password must contain at least one letter, one number, and one special character.',
    //             'accept_terms.accepted' => 'You must accept the terms and conditions.',
    //         ]);
    //     } catch (\Illuminate\Validation\ValidationException $e) {
    //         // Handle validation errors here
    //         return back()->withErrors($e->errors())->withInput();
    //     }

    //     DB::beginTransaction();
    //     try {
    //         // Create new user
    //         $user = QueuedbUser::create([
    //             'email' => $data['email'],
    //             'password' => Hash::make($data['password']),
    //             't_c_check' => $request->has('accept_terms') ? 1 : 0,
    //             'role' => 1,
    //         ]);

    //         $user->update(['pr_user_id' => $user->id]);

    //         // Log in the newly registered user
    //         $user->sendEmailVerificationNotification();

    //         // Redirect to the dashboard or any desired route
    //         Session::flash('success', '<i class="fa fa-check-circle"></i> Please verify your email address to activate your account. We have sent you a verification email..');
    //         // return redirect()->route('verification.resend', ['user_id' => $user->id])->withSuccess('Visit your email and verify your account.');
    //         DB::commit();
    //         return redirect()->route('login');
    //     } catch (\Throwable $error) {
    //         DB::rollback();
    //         // Redirect to the dashboard or any desired route
    //         Session::flash('warning', '<i class="fa-solid fa-triangle-exclamation"></i> Something want wrong, Please try again later!');
    //         // return redirect()->route('verification.resend', ['user_id' => $user->id])->withSuccess('Visit your email and verify your account.');
    //         return redirect()->route('login');
    //     }
    // }

    public function store(Request $request)
    {
        try {
            $passwordRule = [
                'required',
                'min:8',
                'regex:/^(?=.*[a-zA-Z0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]+$/',
            ];

            $data = $request->validate([
                'email' => 'required|email|max:250|unique:queuetb_users',
                'password' => $passwordRule,
                'accept_terms' => 'accepted',
            ], [
                'email.required' => 'The email field is required.',
                'email.email' => 'Please enter a valid email address.',
                'email.max' => 'The email must not exceed :max characters.',
                'email.unique' => 'The email has already been taken.',
                'password.required' => 'The password field is required.',
                'password.min' => 'The password must be at least :min characters long.',
                'password.regex' => 'The password must contain at least one letter, one number, and one special character.',
                'accept_terms.accepted' => 'You must accept the terms and conditions.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors here
            return back()->withErrors($e->errors())->withInput();
        }

        DB::beginTransaction();
        try {
            // Create new user
            $user = QueuedbUser::create([
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                't_c_check' => $request->has('accept_terms') ? 1 : 0,
                'role' => 1,
            ]);

            $user->update(['pr_user_id' => $user->id]);

            // Log in the newly registered user
            $user->sendEmailVerificationNotification();

            // Redirect to the dashboard or any desired route
            Session::flash('success', '<i class="fa fa-check-circle"></i> Please verify your email address to activate your account. We have sent you a verification email..');
            // return redirect()->route('verification.resend', ['user_id' => $user->id])->withSuccess('Visit your email and verify your account.');
            DB::commit();
            return redirect()->route('login');
        } catch (\Throwable $error) {
            DB::rollback();
            return response()->json(['status' => false, "message" => $error->getMessage()]);
        }
    }

    /**
     * Display a login form.
     *
     * @return \Illuminate\Http\Response
     */
    // public function login()
    // {
    //     return view('auth-profile.login');
    // }
    public function login()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth-profile.login');
    }

    /**
     * Authenticate the user.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function authenticate(Request $request)
    {
        $passwordRule = [
            'required',
            'min:8',
            'regex:/^(?=.*[a-zA-Z0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]+$/',
        ];

        $credentials = $request->validate(
            [
            'email' => 'required|email|max:250',
            'password' => $passwordRule,
        ],
            [
            'email.required' => 'The email field is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'The email must not exceed :max characters.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least :min characters long.',
            'password.regex' => 'The password must contain at least one letter, one number, and one special character.',
        ]
        );
         $remember = $request->has('remember'); 
         Log::info('Attempting login', ['credentials' => $credentials, 'remember' => $remember]);
          if(isset($remember) && !empty($remember))
          {
            setcookie("email", $request->email, time()+3600);
            setcookie("password", $request->password, time()+3600);
           // Log::info('Attempting login', [$credentials->email, $credentials->password, 'remember' => $remember]);
          }    
          else {
            setcookie("email", '');
            setcookie("password", '');
          } 

        if (Auth::attempt($credentials, $remember)) {

            $user = Auth::user(); // Retrieve the authenticated user
            // return $user;die;
            if ($user->status == 1) { // Check if user status is 1 (active)
               
                if ($user->verify == 0) {
                    $user->sendEmailVerificationNotification();
                    Auth::logout();
                    Session::flash('error', '<i class="fa  fa-exclamation-circle"></i> Your account is not activated');
                    return back()->withErrors([
                        'email' => 'Your account has not been verified yet.go to gmail and verifiy it',
                    ])->onlyInput('email');
                }

                $request->session()->regenerate();

                if ($user->role == 99) { // role == 99 is Admin role id
                    return redirect()->route('admin-index');
                }
                // return redirect()->route('dashboard')->withSuccess('You have successfully logged in!');
                Session::flash('success', '<i class="fa  fa-check-circle"></i> You have successfully logged in');

                // For error message with an icon
                Session::flash('error', '<i class="fa  fa-exclamation-circle"></i> You have not logged in');

                //return redirect()->back();
                return redirect()->route('dashboard');
            }
           
            Auth::logout();
            Session::flash('error', '<i class="fa  fa-exclamation-circle"></i> Your account is not activated');
            return back()->withErrors([
                'status' => 'Your account is not active. Please contact support.',
            ])->onlyInput('email');
        }
        else
        {
             Session::flash('error', '<i class="fa  fa-exclamation-circle"></i> incorrect password');
        }

        return back()->withErrors([
            'password' => 'incorrect password',
        ])->onlyInput('password');
    }

    /**
     * Display a dashboard to authenticated users.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {

        if (Auth::check()) {
            
            if (Auth::user()->role == 99)
            {
                
                return view('admin.adminindex');
            }
            else
            {
              
                return view('dashboard');
            }
        }

        return redirect()->route('login')
            ->withErrors([
                'email' => 'Please login to access the dashboard.',
            ])->onlyInput('email');
    }

    /**
     * Log out the user from application.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')
            ->withSuccess('You have logged out successfully!');
    }
}
