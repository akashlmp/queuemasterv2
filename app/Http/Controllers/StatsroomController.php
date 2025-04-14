<?php

namespace App\Http\Controllers;

use App\Models\PermissionAccess;
use App\Models\QueueRoom;
use Illuminate\Support\Facades\Auth;

class StatsroomController extends Controller
{
//     public function index()
// {
//     $prUserId = Auth::user()->pr_user_id;
//     $user_role = Auth::user()->role;

//     if ($user_role == 1) {
//         // Admin role (role = 1)
//         $queuestatsRooms = QueueRoom::where('parent_user_id', $prUserId)->paginate(5);
        
//         // Initialize 'permission' field for all rooms for the admin
//         foreach ($queuestatsRooms as $room) {
//             $room['permission'] = '';
//         }
//     } else {
//         // Non-admin role
//         $queueRoomSetups = QueueRoom::where('parent_user_id', $prUserId)->paginate(5);
        
//         $permissions = PermissionAccess::where('user_id', auth()->id())->value('queue_room_access');
//         $originalArray = json_decode($permissions);
//         $filteredArray = [];

//         // Filter permissions for module_id = 1
//         foreach ($originalArray as $key => $value) {
//             foreach ($value as $obj) {
//                 if ($obj->module_id === 1) {
//                     $filteredArray[$key][] = $obj;
//                     break;
//                 }
//             }
//         }

//         // Convert to stdClass array
//         $filteredStdClass = [];
//         foreach ($filteredArray as $key => $value) {
//             $filteredStdClass[$key] = $value;
//         }

//         // Filter out rooms based on permissions
//         foreach ($queueRoomSetups as $index => $room) {
//             $roomId = $room['id'];
//             if (isset($filteredStdClass[$roomId]) && count($filteredStdClass[$roomId]) > 0) {
//                 $permission = $filteredStdClass[$roomId][0]->permission;
//                 if ($permission === 0) {
//                     unset($queueRoomSetups[$index]);
//                 }
//             }
//         }

//         // Re-index the array to reset keys after unsetting
//         $queuestatsRooms = array_values($queueRoomSetups->items());

//         // Filter permissions to only keep those with non-zero values
//         foreach ($filteredStdClass as $key => $value) {
//             if ($value[0]->permission === 0) {
//                 unset($filteredStdClass[$key]);
//             }
//         }

//         // Set permissions in the filtered rooms
//         $i = 0;
//         if (!is_null($queuestatsRooms)) {
//             foreach ($filteredStdClass as $queueId => $permissions) {
//                 if ($queuestatsRooms[$i]['id'] === $queueId) {
//                     $queuestatsRooms[$i]['permission'] = $permissions[0]->permission;
//                     $i++;
//                 }
//             }
//         } else {
//             $queuestatsRooms = [];
//         }

//         // Create a new paginator instance for the filtered results
//         $currentPage = $queueRoomSetups->currentPage();
//         $perPage = $queueRoomSetups->perPage();
//         $total = count($queuestatsRooms);
//         $queuestatsRooms = new \Illuminate\Pagination\LengthAwarePaginator(
//             $queuestatsRooms,
//             $total,
//             $perPage,
//             $currentPage,
//             ['path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath()]
//         );
//     }

//     return view('stats-room.statsRoom', ['queuestatsRooms' => $queuestatsRooms]);
// }
// public function index()
// {
//     $prUserId = Auth::user()->pr_user_id;
//     $user_role = Auth::user()->role;

//     if ($user_role == 1) {
//         // Admin role (role = 1)
//         $queuestatsRooms = QueueRoom::where('parent_user_id', $prUserId)->paginate(5);
        
//         // Initialize 'permission' field for all rooms for the admin
//         foreach ($queuestatsRooms as $room) {
//             $room['permission'] = '';
//         }
//     } else {
//         // Non-admin role
//         $queueRoomSetups = QueueRoom::where('parent_user_id', $prUserId)->paginate(5);
        
//         $permissions = PermissionAccess::where('user_id', auth()->id())->value('queue_room_access');
//         $originalArray = json_decode($permissions, true); // Decode as associative array
//         $filteredArray = [];

//         // Filter permissions for module_id = 1
//         foreach ($originalArray as $key => $value) {
//             foreach ($value as $obj) {
//                 if (isset($obj['module_id']) && $obj['module_id'] === 1) {
//                     $filteredArray[$key][] = $obj;
//                     break;
//                 }
//             }
//         }

//         // Convert to stdClass array
//         $filteredStdClass = [];
//         foreach ($filteredArray as $key => $value) {
//             $filteredStdClass[$key] = $value;
//         }

//         // Filter out rooms based on permissions
//         $filteredRooms = [];
//         foreach ($queueRoomSetups as $index => $room) {
//             $roomId = $room['id'];
//             if (isset($filteredStdClass[$roomId]) && count($filteredStdClass[$roomId]) > 0) {
//                 $permission = $filteredStdClass[$roomId][0]['permission'] ?? null;
//                 if ($permission === 0) {
//                     continue; // Skip rooms with zero permission
//                 }
//             }
//             $filteredRooms[] = $room;
//         }

//         // Re-index the array to reset keys after filtering
//         $queuestatsRooms = $filteredRooms;

//         // Filter permissions to only keep those with non-zero values
//         foreach ($filteredStdClass as $key => $value) {
//             if ($value[0]['permission'] === 0) {
//                 unset($filteredStdClass[$key]);
//             }
//         }

//         // Set permissions in the filtered rooms
//         $i = 0;
//         foreach ($filteredStdClass as $queueId => $permissions) {
//             if (isset($queuestatsRooms[$i]) && $queuestatsRooms[$i]['id'] === $queueId) {
//                 $queuestatsRooms[$i]['permission'] = $permissions[0]['permission'];
//                 $i++;
//             }
//         }

//         // Create a new paginator instance for the filtered results
//         $currentPage = $queueRoomSetups->currentPage();
//         $perPage = $queueRoomSetups->perPage();
//         $total = count($queuestatsRooms);
//         $queuestatsRooms = new \Illuminate\Pagination\LengthAwarePaginator(
//             $queuestatsRooms,
//             $total,
//             $perPage,
//             $currentPage,
//             ['path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath()]
//         );
//     }

//     return view('stats-room.statsRoom', ['queuestatsRooms' => $queuestatsRooms]);
// }

    public function index()
{
    $user = Auth::user();
    $prUserId = $user->pr_user_id;
    $user_role = $user->role;

    // Base query for rooms
    $query = QueueRoom::where('parent_user_id', $prUserId)->orderby('id', 'desc');

    if ($user_role == 1) {
        // Admin role (role = 1)
        $queueRooms = $query->get(); // Fetch all rooms

        // Initialize 'permission' field for all rooms for the admin
        $queueroomwithpermission = $queueRooms->map(function($room) {
            $room->permission = ''; // Use property assignment
            return $room;
        });

        // Create a paginator instance with the filtered results
        $currentPage = request()->input('page', 1); // Get current page from request
        $perPage = 5; // Set the number of items per page
        $total = $queueRooms->count(); // Total count based on all rooms

        // Slice the collection for the current page
        $currentItems = $queueroomwithpermission->forPage($currentPage, $perPage);

        $queueroomwithpermission = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems, // Items for the current page
            $total,
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath()]
        );
    } else {
        // Non-admin role
        $permissions = PermissionAccess::where('user_id', auth()->id())
            ->value('queue_room_access');
        $originalArray = json_decode($permissions, true); // Decode as associative array

        // Filter permissions for module_id = 2 and permission not equal to 0
        $filteredArray = array_filter($originalArray, function($value) {
            return array_filter($value, function($obj) {
                return isset($obj['module_id']) && $obj['module_id'] == 2 && $obj['permission'] != 0;
            });
        });

        // Convert filteredArray to stdClass format
        $filteredStdClass = array_map(function($value) {
            return array_values($value); // Re-index filtered array
        }, $filteredArray);

        // Get all rooms and filter based on permissions
        $queueRooms = $query->get(); // Fetch all rooms
        $filteredRooms = $queueRooms->map(function($room) use ($filteredStdClass) {
            $roomId = $room->id;
            if (isset($filteredStdClass[$roomId]) && count($filteredStdClass[$roomId]) > 0) {
                $room->permission = $filteredStdClass[$roomId][0]['permission'];
            } else {
                $room->permission = null;
            }
            return $room;
        })->filter(function($room) {
            return $room->permission !== null;
        })->values();

        // Create a paginator instance with the filtered results
        $currentPage = request()->input('page', 1); // Get current page from request
        $perPage = 5; // Set the number of items per page
        $total = $filteredRooms->count(); // Total count based on filtered results

        // Slice the collection for the current page
        $currentItems = $filteredRooms->forPage($currentPage, $perPage);

        $queueroomwithpermission = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems, // Items for the current page
            $total,
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath()]
        );
    }

    // Pass the paginated results to the view
    return view('stats-room.statsRoom', ['queuestatsRooms' => $queueroomwithpermission]);
}


}
