<?php

namespace App\Http\Controllers;

use App\Models\PermissionAccess;
use App\Models\QueueRoom;
use App\Models\QueueRoomSetup;
use App\Models\QueueRoomTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class InOutRulesController extends Controller
{
    // public function index()
    // {
    //     $user = Auth::user();
    //     $user_role = Auth::user()->role;

    //     if ($user_role == 1) {
    //         $queueRoomtemps = QueueRoom::where('parent_user_id', $user->pr_user_id)->paginate(50);
    //         // $queueRoomtemp = $queueRoomtemps->toArray();

    //         // foreach ($queueRoomtemp as $room) {
    //         //     $room['permission'] = '';
    //         // }
    //         $queueRoomtemp = $queueRoomtemps->map(function($room) {
    //             $room['permission'] = '';
    //             return $room;
    //         });
    //     } else {
    //         $queueRoom = QueueRoom::where('parent_user_id', $user->pr_user_id)->get();
    //         $queueRoomtemp = $queueRoom->toArray();
    //         $permissions = PermissionAccess::where('user_id', auth()->id())->value('queue_room_access');
    //         $originalArray = json_decode($permissions);
    //         $filteredArray = [];
    //         $temp_id = [];

    //         foreach ($originalArray as $key => $value) {
    //             foreach ($value as $obj) {
    //                 if ($obj->module_id === 4) {
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
    //         $queueRoomtemps = array_values($queueRoomtemp);

    //         foreach ($filteredStdClass as $key => $value) {
    //             if ($value[0]->permission === 0) {
    //                 unset($filteredStdClass[$key]);
    //             }
    //         }

    //         $i = 0;
    //         if (! is_null($queueRoomtemps)) {
    //             foreach ($filteredStdClass as $queueId => $permissions) {
    //                 if ($queueRoomtemps[$i]['id'] === $queueId) {
    //                     $queueRoomtemps[$i]['permission'] = $permissions[0]->permission;
    //                     $i++;
    //                 }
    //             }
    //         } else {
    //             $queueRoomtemps = [];
    //         }
    //     }

    //     return view('queue-room.in_out_rules', ['queueRoomtemps' => $queueRoomtemps]);
    // }

    public function index(Request $request)
    {
        $user = Auth::user();
        $user_role = $user->role;
        $prUserId = $user->pr_user_id;

        // Base query for rooms
        $query = QueueRoom::where('parent_user_id', $prUserId)->orderby('id', 'desc');
        
        if ($user_role == 1) {
            // Admin: Use pagination
            $queueRooms = $query->paginate(10);
            $queueRoomtemps = $queueRooms;
        } else {
            // Non-admin: Filter based on permissions
            $queueRooms = $query->get();
            $permissions = PermissionAccess::where('user_id', auth()->id())->value('queue_room_access');
            $originalArray = json_decode($permissions, true);
            $filteredArray = [];

            foreach ($originalArray as $key => $value) {
                foreach ($value as $obj) {
                    if ($obj['module_id'] === 4) {
                        $filteredArray[$key][] = $obj;
                        break;
                    }
                }
            }

            $filteredStdClass = [];
            foreach ($filteredArray as $key => $value) {
                $filteredStdClass[$key] = $value;
            }

            $queueRoomtemp = $queueRooms->toArray();

            foreach ($queueRoomtemp as $index => $room) {
                $roomId = $room['id'];
                if (isset($filteredStdClass[$roomId]) && count($filteredStdClass[$roomId]) > 0) {
                    $permission = $filteredStdClass[$roomId][0]['permission'];
                    if ($permission === 0) {
                        unset($queueRoomtemp[$index]);
                    }
                }
            }

            $queueRoomtemp = array_values($queueRoomtemp);

            // Paginate filtered results
            $currentPage = $request->input('page', 1);
            $perPage = 10;
            $total = count($queueRoomtemp);
            $currentItems = array_slice($queueRoomtemp, ($currentPage - 1) * $perPage, $perPage);

            $queueRoomtemps = new \Illuminate\Pagination\LengthAwarePaginator(
                $currentItems,
                $total,
                $perPage,
                $currentPage,
                ['path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath()]
            );
        }

        return view('queue-room.in_out_rules', compact('queueRoomtemps', 'queueRooms'));
    }



    public function deleteInOut($id)
    {
        // Find the email notice record by ID
        $inOut = QueueRoomSetup::findOrFail($id);

        // Delete the record
        $inOut->delete();
        Session::flash('success', '<i class="fa fa-check-circle"></i> Queue Room deleted successfully!');
        // Optionally, you can redirect back with a success message
        return redirect()->back()->with('success', 'Queue Room deleted successfully.');
    }

    public function inOutRuleEdit($id)
    {
        $user = Auth::user();
        $user_role = Auth::user()->role;

        if ($user_role == 1) {
            $queueRoomTemplate = QueueRoomTemplate::find($id)->toArray();
            $queueRoomTemplate['permission'] = '';
        } else {

            $queueRoomTemplate = QueueRoomTemplate::find($id)->get();
            $permissions = PermissionAccess::where('user_id', auth()->id())->value('queue_room_access');
            $originalArray = json_decode($permissions, true); // Convert to associative array

            $filteredArray = [];
            foreach ($originalArray as $key => $value) {
                foreach ($value as $obj) {
                    if ($obj['module_id'] === 4) {
                        $filteredArray[$key][] = $obj;
                        break;
                    }
                }
            }

            $filteredStdClass = [];
            foreach ($filteredArray as $key => $value) {
                if ($key === $id) {
                    $filteredStdClass[$key] = $value;
                    break; // Once the desired key is found, exit the loop
                }
            }

            // Assuming $filteredStdClass[$id] is always set
            $queueRoomTemplate['permission'] = $filteredStdClass[$id][0]['permission'];
        }

        return view('queue-room.in_out_edit', compact('queueRoomTemplate'));
    }

    public function update(Request $request, $id)
    {
        $queueRoomTemplate = QueueRoomTemplate::find($id);
        $queueRoomTemplate->id = $request->input('temp_id');
        $queueRoomTemplate->template_name = $request->input('template_name');
        $queueRoomTemplate->input_url = $request->input('input_url');
        $queueRoomTemplate->is_advance_setting = $request->has('AdvanceSettingCheckBox') ? 1 : 0;

        $result = [];
        foreach ($_POST['advance'] as $key => $values) {
            foreach ($values as $index => $value) {
                // Add a new array for each index if it doesn't exist
                if (! isset($result[$index])) {
                    $result[$index] = [];
                }
                // Add the value to the corresponding index

                $result[$index][$key] = $value;
            }
        }

        if (count($result[0])) {
            $result[0]['operator'] = null;
        }

        $advancedata = json_encode($result);

        // Update the advance_setting_rules field
        $queueRoomTemplate->advance_setting_rules = $advancedata;

        // Save the updated data
        $queueRoomTemplate->save();
        Session::flash('success', '<i class="fa fa-check-circle"></i> Queue Room Template updated successfully!');
        // Redirect back or wherever you want after the update
        return redirect()->route('in-out-rule')->with('success', 'Queue Room Template updated successfully');
    }
}
