<?php

namespace App\Http\Controllers;

use App\Models\CommonModules;
use App\Models\ModulePermission;
use App\Models\QueuedbUser;
use App\Models\QueueRoom;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AddStaffController extends Controller
{
    // public function index()
    // {
    //     $modules = ModulePermission::all();
    //     $user = Auth::user();
    //     $roles = Role::where('status', 1)->get();
    //     $rooms = [];
    //     if ($user->role === 1) {
    //         //$rooms = QueueRoom::all();
    //         $rooms = QueueRoom::where(function ($query) use ($user) {
    //             $query->where('parent_user_id', $user->id);
    //         })->get();
    //     } else {
    //         $rooms = QueueRoom::where(function ($query) use ($user) {
    //             $query->where('parent_user_id', $user->pr_user_id);
    //         })->get();
    //     }
    //     // Fetch common modules
    //     $commonModules = CommonModules::all();
    //     // Fetch data from queuetb_roles table
    //     $roles = Role::where('status', 1)->get();
    //     // Pass data to the view
    //     return view('auth-profile/addStaff', [
    //         'modules' => $modules,
    //         'roles' => $roles,
    //         'rooms' => $rooms,
    //         'commonModules' => $commonModules,
    //         'user' => $user,
    //     ]);
    // }

    public function index()
    {
        $modules = ModulePermission::all();
        $user = Auth::user();

        // Get roles with status 1
        $roles = Role::where('status', 1)->get();

        // Fetch rooms based on user's role
        $rooms = QueueRoom::where('parent_user_id', $user->role === 1 ? $user->id : $user->pr_user_id)->get();

        // Fetch common modules
        $commonModules = CommonModules::all();

        // Pass data to the view
        return view('auth-profile.addStaff', [
            'modules' => $modules,
            'roles' => $roles,
            'rooms' => $rooms,
            'commonModules' => $commonModules,
            'user' => $user,
        ]);
    }


    // public function save(Request $request)
    // {
    //     $passwordRule = [
    //         'required',
    //         'min:8',
    //         'regex:/^(?=.*[a-zA-Z0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]+$/',
    //     ];
    //     // Define custom error messages for validation
    //     $customMessages = [
    //         'staff_name.required' => 'The staff name field is required.',
    //         'staff_email.required' => 'The staff email field is required.',
    //         'staff_email.email' => 'Please enter a valid email address for the staff email.',
    //         'staff_email.max' => 'The staff email must not be greater than 250 characters.',
    //         'staff_password.required' => 'The staff password field is required.',
    //         'staff_password.min' => 'The staff password must be at least 8 characters long.',
    //         'staff_password.regex' => 'The staff password must contain at least one letter, one number, and one special character.',

    //     ];
    //     // Validate input data with custom error messages
    //     $request->validate([
    //         'staff_name' => 'required',
    //         'staff_email' => 'required|email|max:250',
    //         'staff_password' => $passwordRule,
    //     ], $customMessages);

    //     if (QueuedbUser::where('email', $request->input('staff_email'))->exists()) {
    //         Session::flash('error', '<i class="fa fa-exclamation-circle"></i> Email is already registered');
    //         return redirect()->back();
    //     }

    //     $employee = new QueuedbUser();
    //     $employee->company_person_name = $request->input('staff_name');
    //     $employee->email = $request->input('staff_email');
    //     $employee->password = bcrypt($request->input('staff_password'));
    //     $employee->status = $request->input('staff_status');
    //     $employee->pr_user_id = auth()->id();
    //     $employee->role = 4;
    //     $employee->verify = 1;
    //     $employee->save();
    //     $roomAccessData = [];
    //     $user = Auth::user();
    //     //$rooms = QueueRoom::all();
    //     $rooms = [];
    //     if ($user->role === 1) {
    //         //$rooms = QueueRoom::all();
    //         $rooms = QueueRoom::where(function ($query) use ($user) {
    //             $query->where('parent_user_id', $user->id);
    //         })->get();
    //     } else {
    //         $rooms = QueueRoom::where(function ($query) use ($user) {
    //             $query->where('parent_user_id', $user->pr_user_id);
    //         })->get();
    //     }
    //     $modules = ModulePermission::all();
    //     $roomAccessData = [];
    //     foreach ($rooms as $room) {
    //         $modulePermissions = [];
    //         foreach ($modules as $module) {
    //             $permissionKey = 'permission_' . $room->id . $module->id;
    //             if ($request->has($permissionKey)) {
    //                 $permissionValue = $this->getPermissionValue($request->input($permissionKey));
    //             } else {
    //                 $permissionValue = 0;
    //             }
    //             $modulePermissions[] = [
    //                 'module_id' => $module->id,
    //                 'permission' => $permissionValue,
    //             ];
    //         }
    //         $roomAccessData[$room->id] = $modulePermissions;
    //     }

    //     $commonmodulePermissions = '[{"module_id":1,"permission":2},{"module_id":2,"permission":0},{"module_id":3,"permission":0},{"module_id":4,"permission":0}]';
    //     $roomAccessDataJson = json_encode($roomAccessData);
    //     DB::table('queuetb_permission_access')->insert([
    //         'user_id' => $employee->id,
    //         'role_id' => $employee->role,
    //         'queue_room_access' => $roomAccessDataJson,
    //         'common_module_id' => $commonmodulePermissions,
    //         'created_at' => now(),
    //         'updated_at' => now(),
    //     ]);
    //     Session::flash('success', '<i class="fa fa-check-circle"></i> Add staff successfully');
    //     Session::flash('error', '<i class="fa fa-exclamation-circle"></i> Staff not added');
    //     return redirect()->route('staff-access-manage');
    // }

    public function save(Request $request)
    {
        $passwordRule = [
            'required',
            'min:8',
            'regex:/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[!@#$%^&*]).+$/',
        ];

        // Define custom error messages for validation
        $customMessages = [
            'staff_name.required' => 'The staff name field is required.',
            'staff_email.required' => 'The staff email field is required.',
            'staff_email.email' => 'Please enter a valid email address for the staff email.',
            'staff_email.max' => 'The staff email must not be greater than 250 characters.',
            'staff_password.required' => 'The staff password field is required.',
            'staff_password.min' => 'The staff password must be at least 8 characters long.',
            'staff_password.regex' => 'The staff password must contain at least one letter, one number, and one special character.',
        ];

        // Validate input data with custom error messages
        $request->validate([
            'staff_name' => 'required',
            'staff_email' => 'required|email|max:250',
            'staff_password' => $passwordRule,
        ], $customMessages);

        if (QueuedbUser::where('email', $request->input('staff_email'))->exists()) {
            Session::flash('error', '<i class="fa fa-exclamation-circle"></i> Email is already registered');
            return redirect()->back();
        }

        try {
            DB::beginTransaction();

            $employee = new QueuedbUser();
            $employee->company_person_name = $request->input('staff_name');
            $employee->email = $request->input('staff_email');
            $employee->password = bcrypt($request->input('staff_password'));
            $employee->status = $request->input('staff_status');
            $employee->pr_user_id = auth()->id();
            $employee->role = 4;
            $employee->verify = 1;
            $employee->save();

            $user = Auth::user();
            $rooms = QueueRoom::where('parent_user_id', $user->role === 1 ? $user->id : $user->pr_user_id)->get();
            $modules = ModulePermission::all();

            $roomAccessData = [];
            foreach ($rooms as $room) {
                $modulePermissions = [];
                foreach ($modules as $module) {
                    $permissionKey = 'permission_' . $room->id . $module->id;
                    $permissionValue = $request->has($permissionKey) ? $this->getPermissionValue($request->input($permissionKey)) : 0;
                    $modulePermissions[] = [
                        'module_id' => $module->id,
                        'permission' => $permissionValue,
                    ];
                }
                $roomAccessData[$room->id] = $modulePermissions;
            }

            $commonmodulePermissions = '[{"module_id":1,"permission":2},{"module_id":2,"permission":0},{"module_id":3,"permission":0},{"module_id":4,"permission":0}]';
            $roomAccessDataJson = json_encode($roomAccessData);

            DB::table('queuetb_permission_access')->insert([
                'user_id' => $employee->id,
                'role_id' => $employee->role,
                'queue_room_access' => $roomAccessDataJson,
                'common_module_id' => $commonmodulePermissions,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            Session::flash('success', '<i class="fa fa-check-circle"></i> Add staff successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to add staff: " . $e->getMessage());
            Session::flash('error', '<i class="fa fa-exclamation-circle"></i> Staff not added');
        }

        return redirect()->route('staff-access-manage');
    }


    // private function getPermissionValue($input)
    // {
    //     // Check if input is null or empty, set default value to 0 (no access)
    //     if (is_null($input) || $input === '') {
    //         return 0;
    //     }

    //     switch ($input) {
    //         case 'no_access':
    //             return 0;
    //         case 'read_only':
    //             return 1;
    //         case 'full_access':
    //             return 2;
    //         default:
    //             return 0; // Default to no access if input is invalid
    //     }
    // }

    private function getPermissionValue($input)
    {
        // Check if input is null or empty, set default value to 0 (no access)
        if (is_null($input) || $input === '') {
            return 0;
        }
    
        // Normalize input to lowercase for case-insensitive comparison
        $input = strtolower($input);
    
        switch ($input) {
            case 'no_access':
                return 0;
            case 'read_only':
                return 1;
            case 'full_access':
                return 2;
            default:
                return 0; // Default to no access if input is invalid
        }
    }

}
