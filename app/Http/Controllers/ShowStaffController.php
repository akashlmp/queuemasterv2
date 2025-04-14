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

class ShowStaffController extends Controller
{
    public function index()
    {
        $loggedInUserId = Auth::id();
        $prUserId = Auth::user()->pr_user_id;
        $users = QueuedbUser::leftJoin('queuetb_roles', 'queuetb_users.role', '=', 'queuetb_roles.id')
            ->select('queuetb_users.*', 'queuetb_roles.name as role_name')
            ->where('queuetb_users.pr_user_id', $loggedInUserId)
            // ->orWhere('queuetb_users.pr_user_id', '!=', $prUserId)
            ->orWhere('queuetb_users.id', $loggedInUserId)
            ->get();

        $modules = ModulePermission::all();
        // Pass the retrieved data to the view
        return view('auth-profile/staffaccess', compact('users'));
    }

    public function edit($id)
    {
        $user = QueuedbUser::findOrFail($id);
        $permissions = DB::table('queuetb_permission_access')
            ->where('user_id', $user->id)
            ->get();
        // Fetch modules if needed
        $modules = ModulePermission::all();
        $commonModules = CommonModules::all();
        // $rooms = QueueRoom::all();
        $rooms = [];
        if ($user->role === 1) {
            $rooms = QueueRoom::where(function ($query) use ($user) {
                $query->where('parent_user_id', $user->id);
            })->get();
        } else {
            $rooms = QueueRoom::where(function ($query) use ($user) {
                $query->where('parent_user_id', $user->pr_user_id);
            })->get();
        }
        $roles = Role::where('status', 1)->get();

        return view('auth-profile/editStaff', compact('user', 'modules', 'permissions', 'roles', 'commonModules', 'rooms'));
    }

    public function updatePermissions(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
        ]);
        // Retrieve the user by ID
        $user = QueuedbUser::findOrFail($id);
        $user->company_person_name = $request->input('name');
        $user->role = 4;
        $user->save();
        $rooms = [];
        if ($user->role === 1) {
            $rooms = QueueRoom::where(function ($query) use ($user) {
                $query->where('parent_user_id', $user->id);
            })->get();
        } else {
            $rooms = QueueRoom::where(function ($query) use ($user) {
                $query->where('parent_user_id', $user->pr_user_id);
            })->get();
        }
        $modules = ModulePermission::all();
        $roomAccessData = [];
        foreach ($rooms as $room) {
            $modulePermissions = [];
            foreach ($modules as $module) {
                $permissionKey = 'permission_' . $room->id . $module->id;
                if ($request->has($permissionKey)) {
                    $permissionValue = $this->getPermissionValue($request->input($permissionKey));
                } else {
                    $permissionValue = 0;
                }
                $modulePermissions[] = [
                    'module_id' => $module->id,
                    'permission' => $permissionValue,
                ];
            }
            $roomAccessData[$room->id] = $modulePermissions;
        }

        $commonmodulePermissions = '[{"module_id":1,"permission":2},{"module_id":2,"permission":0},{"module_id":3,"permission":0},{"module_id":4,"permission":0}]';
        DB::table('queuetb_permission_access')
            ->where('user_id', $user->id)
            ->update([
                'role_id' => $user->role,
                'queue_room_access' => json_encode($roomAccessData),
                'common_module_id' => $commonmodulePermissions,
                'updated_at' => now(), // Update the timestamp
            ]);
        Session::flash('success', '<i class="fa fa-check-circle"></i> Staff details updated successfully');
        Session::flash('error', '<i class="fa fa-exclamation-circle"></i> Staff details not updated');
        return redirect()->route('staff-access-manage');
    }

    public function delete($id)
    {
        $user = QueuedbUser::findOrFail($id);

        DB::beginTransaction();

        try {
            $user->delete();

            DB::table('queuetb_permission_access')
                ->where('user_id', $user->id)
                ->delete();

            DB::commit();
            Session::flash('success', '<i class="fa fa-check-circle"></i> Staff member and their permissions deleted successfully!');
            return redirect()->route('showStaff');
        } catch (\Exception $e) {
            DB::rollback();

            Session::flash('error', '<i class="fa fa-exclamation-circle"></i> Failed to delete staff member and their permissions');
            return redirect()->back();
        }
    }

    public function activateDeactivate($id)
    {
        // Find the user by ID
        $user = QueuedbUser::findOrFail($id);

        // Toggle the user's status
        $user->status = $user->status === 1 ? 0 : 1;
        $user->save();

        // Redirect back to the previous page or any other desired route
        //return redirect()->back()->withSuccess('User status updated successfully!');
        Session::flash('success', '<i class="fa fa-check-circle"></i> User status updated successfully!');

        // For error message with an icon
        Session::flash('error', '<i class="fa fa-exclamation-circle"></i> User status not updated!');

        return redirect()->back();
        //return redirect()->route('staff-access-manage');
    }

    private function getPermissionValue($input)
    {
        switch ($input) {
            case 'no_access':
                return 0;
            case 'read_only':
                return 1;
            case 'full_access':
                return 2;
            default:
                return 0;
        }
    }
}
