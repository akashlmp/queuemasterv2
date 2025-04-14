<?php

namespace App\Http\Controllers;

use App\Models\DeveloperScript;
use App\Models\PermissionAccess;
use App\Models\QueueRoom;
use App\Models\QueueRoomTemplate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use DB;

class DeveloperController extends Controller
{
    // public function index()
    // {
    //     $user = Auth::user();
    //     $user_role = Auth::user()->role;

    //     $developerScript = DeveloperScript::firstOrNew(['id' => 1]);
    //     $script = $developerScript->developer_script;
    //     $browser_url = 'http://queuing.walkingdreamz.com';

    //     $browser_domain = parse_url($browser_url, PHP_URL_HOST);

    //     $queueRoomtemps = [];

    //     if ($user_role == 1) {
    //         $queueRoomtemps = QueueRoom::where('parent_user_id', $user->pr_user_id)->get(['id','queue_room_name', 'queue_room_icon','is_ended','queue_room_template_id']);
    //         $queueRoomtemp = $queueRoomtemps->toArray();

    //         // return $queueRoomtemp;die;
    //         $queueRoomT = [];
    //         foreach ($queueRoomtemp as $room) {
    //             $template_id = $room['queue_room_template_id'];
    //             $template = QueueRoomTemplate::where('id', $template_id)->first();

    //             if (!empty($template))
    //             {
    //                 $queueRoomT[] = $room; // Append $room to $queueRoomT array

    //                 if ($template) {
    //                     $queueRoomT[count($queueRoomT) - 1]['input_url'] = $template->input_url;
    //                 } else {
    //                     $queueRoomT[count($queueRoomT) - 1]['input_url'] = null;
    //                 }
    //                 $queueRoomT[count($queueRoomT) - 1]['permission'] = '';
    //                 $queueRoomT[count($queueRoomT) - 1]['is_advance_setting'] = $template->is_advance_setting;
    //                 $queueRoomT[count($queueRoomT) - 1]['advance_setting_rules'] = $template->advance_setting_rules;
    //             }
    //         }

    //         $queueRoomtemp = $queueRoomT;
    //     } else {
    //         $queueRooms = QueueRoom::where('parent_user_id', $user->pr_user_id)->get(['id','queue_room_name', 'queue_room_icon','is_ended','queue_room_template_id']);
    //         $queueRoomtemp = $queueRooms->toArray();

    //         $queueRoomT = [];
    //         foreach ($queueRoomtemp as $room) {
    //             $template_id = $room['queue_room_template_id'];
    //             $template = QueueRoomTemplate::where('id', $template_id)->first();

    //             $queueRoomT[] = $room; // Append $room to $queueRoomT array

    //             if ($template) {
    //                 $queueRoomT[count($queueRoomT) - 1]['input_url'] = $template->input_url;
    //             } else {
    //                 $queueRoomT[count($queueRoomT) - 1]['input_url'] = null;
    //             }
    //             $queueRoomT[count($queueRoomT) - 1]['is_advance_setting'] = $template->is_advance_setting;
    //             $queueRoomT[count($queueRoomT) - 1]['advance_setting_rules'] = $template->advance_setting_rules;
    //         }

    //         $queueRoomtemp = $queueRoomT;

    //         $permissions = PermissionAccess::where('user_id', auth()->id())->value('queue_room_access');
    //         $originalArray = json_decode($permissions);

    //         $filteredArray = [];
    //         $temp_id = [];

    //         foreach ($originalArray as $key => $value) {
    //             foreach ($value as $obj) {
    //                 if ($obj->module_id === 6) {
    //                     $filteredArray[$key][] = $obj;
    //                     break;
    //                 }
    //             }
    //         }

    //         $filteredStdClass = [];
    //         foreach ($filteredArray as $key => $value) {
    //             $filteredStdClass[$key] = $value;
    //         }

    //         foreach ($queueRoomtemp as $index => $room) {
    //             $roomId = $room['id'];
    //             if (isset($filteredStdClass[$roomId]) && count($filteredStdClass[$roomId]) > 0) {
    //                 $permission = $filteredStdClass[$roomId][0]->permission;
    //                 if ($permission === 0) {
    //                     unset($queueRoomtemp[$index]);
    //                 }
    //             }
    //         }

    //         $queueRoomtemp = array_values($queueRoomtemp);

    //         foreach ($filteredStdClass as $key => $value) {
    //             if ($value[0]->permission === 0) {
    //                 unset($filteredStdClass[$key]);
    //             }
    //         }

    //         $i = 0;
    //         if (! is_null($queueRoomtemp)) {
    //             foreach ($filteredStdClass as $queueId => $permissions) {
    //                 if ($queueRoomtemp[$i]['id'] === $queueId) {
    //                     $queueRoomtemp[$i]['permission'] = $permissions[0]->permission;
    //                     $i++;
    //                 }
    //             }
    //         } else {
    //             $queueRoomtemp = [];
    //         }
    //     }

    //     return view('auth-profile.developer', ['script' => $script,'queueRoomtemps' => $queueRoomtemp,'browser_domain' => $browser_domain,'user' => $user]);
    // }

    // public function index()
    // {
    //     $user = Auth::user();
    //     $userRole = $user->role;
    //     $userId = $user->id;
    //     $prUserId = $user->pr_user_id;

    //     // Retrieve developer script
    //     $developerScript = DeveloperScript::firstOrNew(['id' => 1]);
    //     $script = $developerScript->developer_script;

    //     $browserUrl = 'http://queuing.walkingdreamz.com';
    //     $browserDomain = parse_url($browserUrl, PHP_URL_HOST);

    //     // Initialize the array for queue room templates
    //     $queueRoomTemplates = [];

    //     // Fetch queue room templates based on user role
    //     $queueRooms = QueueRoom::where('parent_user_id', $prUserId)
    //         ->get(['id', 'queue_room_name', 'queue_room_icon', 'is_ended', 'queue_room_template_id'])
    //         ->toArray();

    //     // Process queue room templates
    //     foreach ($queueRooms as $room) {
    //         $templateId = $room['queue_room_template_id'];
    //         $template = QueueRoomTemplate::find($templateId);

    //         $room['input_url'] = $template ? $template->input_url : null;
    //         $room['is_advance_setting'] = $template ? $template->is_advance_setting : null;
    //         $room['advance_setting_rules'] = $template ? $template->advance_setting_rules : null;
    //         $room['permission'] = '';

    //         $queueRoomTemplates[] = $room;
    //     }

    //     if ($userRole != 1) {
    //         // Fetch permissions and filter queue rooms based on permissions
    //         $permissions = PermissionAccess::where('user_id', $userId)->value('queue_room_access');
    //         $permissionsArray = json_decode($permissions, true);

    //         $filteredArray = array_filter($permissionsArray, function($value) {
    //             return array_filter($value, fn($obj) => $obj['module_id'] === 6);
    //         });

    //         $filteredPermissions = [];
    //         foreach ($filteredArray as $key => $value) {
    //             if (isset($value[0]['permission']) && $value[0]['permission'] === 0) {
    //                 unset($queueRoomTemplates[$key]);
    //             } else {
    //                 $filteredPermissions[$key] = $value;
    //             }
    //         }

    //         foreach ($queueRoomTemplates as $index => $room) {
    //             $roomId = $room['id'];
    //             if (isset($filteredPermissions[$roomId]) && count($filteredPermissions[$roomId]) > 0) {
    //                 $queueRoomTemplates[$index]['permission'] = $filteredPermissions[$roomId][0]['permission'];
    //             }
    //         }
    //     }

    //     return view('auth-profile.developer', [
    //         'script' => $script,
    //         'queueRoomtemps' => array_values($queueRoomTemplates),
    //         'browser_domain' => $browserDomain,
    //         'user' => $user
    //     ]);
    // }

    public function index()
    {
        $user = Auth::user();
        $userRole = $user->role;
        $userId = $user->id;
        $prUserId = $user->pr_user_id;
    
        // Retrieve developer script
        $developerScript = DeveloperScript::firstOrNew(['id' => 1]);
        $script = $developerScript->developer_script;
    
        $browserUrl = 'http://queuing.walkingdreamz.com';
        $browserDomain = parse_url($browserUrl, PHP_URL_HOST);
    
        // Initialize the array for queue room templates
        $queueRoomTemplates = [];
    
        // Fetch queue room templates based on user role with pagination
        $perPage = 10; // Number of items per page
        $queueRooms = QueueRoom::where('parent_user_id', $prUserId)->orderby('id', 'desc')
            ->paginate($perPage, ['id', 'queue_room_name', 'queue_room_icon', 'is_ended', 'queue_room_template_id', 'queue_html_page_url', 'prequeue_html_page_url', 'is_prequeue', 'session_type', 'time_input']); // , 'postqueue_html_page_url', 'priorityqueue_html_page_url'
    
        // Process queue room templates
        foreach ($queueRooms as $room) {
            /** creating a sub-domain url for the DNS implement | start */
            $templateId = $room['queue_room_template_id'];
            $getInputURL = QueueRoomTemplate::find($templateId);
            // $getInputURL = DB::table('queue_room_template')->select('input_url')->where('id', $room['queue_room_template_id'])->first();
            $exploadURL = explode('//', $getInputURL->input_url);
            $parsedUrl = parse_url($getInputURL->input_url, PHP_URL_HOST);
            // $parsedPath = parse_url($getInputURL->input_url, PHP_URL_PATH);
            // $checkData['fullURL'] = $exploadURL;
            if (str_contains($parsedUrl, 'www')) {
                $mainURL = explode('www.', $parsedUrl);
                // $mainURL = $parsedUrl[1].$parsedPath;
                $url = $mainURL[1];
            }else{
                // $mainURL = $parsedUrl.$parsedPath;
                $url = $parsedUrl;
            }
            
            /** creating a sub-domain url for the DNS implement | end */
            $room['input_url'] = $getInputURL ? $getInputURL->input_url : null;
            $room['is_advance_setting'] = $getInputURL ? $getInputURL->is_advance_setting : null;
            $room['advance_setting_rules'] = $getInputURL ? $getInputURL->advance_setting_rules : null;
            $room['cname'] = 'queue.'.$url;
            $room['is_prequeue'] = $room['is_prequeue'];
            $room['permission'] = '';
        
            $queueRoomTemplates[] = $room;
        }
    
        if ($userRole != 1) {
            // Fetch permissions and filter queue rooms based on permissions
            $permissions = PermissionAccess::where('user_id', $userId)->value('queue_room_access');
            $permissionsArray = json_decode($permissions, true);
        
            $filteredArray = array_filter($permissionsArray, function($value) {
                return array_filter($value, fn($obj) => $obj['module_id'] === 6);
            });
        
            $filteredPermissions = [];
            foreach ($filteredArray as $key => $value) {
                if (isset($value[0]['permission']) && $value[0]['permission'] === 0) {
                    unset($queueRoomTemplates[$key]);
                } else {
                    $filteredPermissions[$key] = $value;
                }
            }
        
            foreach ($queueRoomTemplates as $index => $room) {
                $roomId = $room['id'];
                if (isset($filteredPermissions[$roomId]) && count($filteredPermissions[$roomId]) > 0) {
                    $queueRoomTemplates[$index]['permission'] = $filteredPermissions[$roomId][0]['permission'];
                }
            }
        }
        
        return view('auth-profile.developer', [
            'script' => $script,
            'queueRoomtemps' => array_values($queueRoomTemplates),
            'browser_domain' => $browserDomain,
            'user' => $user,
            'queueRooms' => $queueRooms // Pass paginated results to the view
        ]);
    }


    // public function markCompleted($id)
    // {
    //     $room = QueueRoom::findOrFail($id);
    //     $room->is_ended = $room->is_ended === 1 ? 0 : 1;
    //     $room->save();

    //     if ($room->is_ended === 1) {
    //         Session::flash('success', '<i class="fa fa-check-circle"></i> Queue Room Template ended successfully!');
    //     } else {
    //         Session::flash('success', '<i class="fa fa-check-circle"></i> Queue Room Template updated successfully!');
    //     }
    //     return redirect()->back();
    // }

    public function markCompleted($id)
    {
        $room = QueueRoom::findOrFail($id);
    
        // Toggle the `is_ended` status
        $room->is_ended = !$room->is_ended;
        $room->save();
    
        // Set flash message based on the new status
        $message = $room->is_ended
            ? '<i class="fa fa-check-circle"></i> Queue Room Template ended successfully!'
            : '<i class="fa fa-check-circle"></i> Queue Room Template updated successfully!';
    
        Session::flash('success', $message);
    
        return redirect()->back();
    }

}
