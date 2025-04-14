<?php

namespace App\Http\Controllers;

use App\Models\EmailNotice;
use App\Models\EmailSmsTemplate;
use App\Models\PermissionAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class EmailNoticeController extends Controller
{
    // public function index()
    // {
    //     $prUserId = Auth::user()->pr_user_id;
    //     $user_role = Auth::user()->role;

    //     if ($user_role == 1) {
    //         $filteredStdClass = [];
    //         $emailTemplates = DB::table('queuetb_queue_room AS qr')
    //             ->select('qr.id AS queue_id', 'qr.queue_room_name', 'qr.queue_room_icon', 'qr.parent_user_id', 'est_email.*', 'users.company_person_name')
    //             ->join('queuetb_email_sms AS est_email', 'qr.email_notice_tempid', '=', 'est_email.id')
    //             ->leftJoin('queuetb_users AS users', function ($join) {
    //                 $join->on('est_email.last_modified_by', '=', 'users.id');
    //             })
    //             ->where('qr.parent_user_id', $prUserId)
    //             ->get();

    //         $smsTemplates = DB::table('queuetb_queue_room AS qr')
    //             ->select('qr.id AS queue_id', 'qr.queue_room_name', 'qr.queue_room_icon', 'qr.parent_user_id', 'est_sms.*', 'users.company_person_name')
    //             ->join('queuetb_email_sms AS est_sms', 'qr.sms_notice_tempid', '=', 'est_sms.id')
    //             ->leftJoin('queuetb_users AS users', function ($join) {
    //                 $join->on('est_sms.last_modified_by', '=', 'users.id');
    //             })
    //             ->where('qr.parent_user_id', $prUserId)
    //             ->get();
    //         $smsTemplates = $smsTemplates->toArray();
    //         $emailTemplates = $emailTemplates->toArray();
    //         foreach ($smsTemplates as &$room) {
    //             $room->permission = '';
    //         }
    //         foreach ($emailTemplates as &$rooms) {
    //             $rooms->permission = '';
    //         }
    //     } else {
    //         $emailTemplates = DB::table('queuetb_queue_room AS qr')
    //             ->select('qr.id AS queue_id', 'qr.queue_room_name', 'qr.queue_room_icon', 'qr.parent_user_id', 'est_email.*', 'users.company_person_name')
    //             ->join('queuetb_email_sms AS est_email', 'qr.email_notice_tempid', '=', 'est_email.id')
    //             ->leftJoin('queuetb_users AS users', function ($join) {
    //                 $join->on('est_email.last_modified_by', '=', 'users.id');
    //             })
    //             ->where('qr.parent_user_id', $prUserId)
    //             ->get();

    //         $smsTemplates = DB::table('queuetb_queue_room AS qr')
    //             ->select('qr.id AS queue_id', 'qr.queue_room_name', 'qr.queue_room_icon', 'qr.parent_user_id', 'est_sms.*', 'users.company_person_name')
    //             ->join('queuetb_email_sms AS est_sms', 'qr.sms_notice_tempid', '=', 'est_sms.id')
    //             ->leftJoin('queuetb_users AS users', function ($join) {
    //                 $join->on('est_sms.last_modified_by', '=', 'users.id');
    //             })
    //             ->where('qr.parent_user_id', $prUserId)
    //             ->get();
    //         $smsTemplates = $smsTemplates->toArray();
    //         $emailTemplates = $emailTemplates->toArray();

    //         $permissions = PermissionAccess::where('user_id', auth()->id())->value('queue_room_access');
    //         $originalArray = json_decode($permissions);
    //         $filteredArray = [];

    //         foreach ($originalArray as $key => $value) {
    //             foreach ($value as $obj) {
    //                 if ($obj->module_id === 5) {
    //                     $filteredArray[$key][] = $obj;
    //                     break;
    //                 }
    //             }
    //         }

    //         $filteredStdClass = [];
    //         foreach ($filteredArray as $key => $value) {
    //             $filteredStdClass[$key] = $value;
    //         }

    //         foreach ($smsTemplates as $index => $room) {
    //             $roomId = $room->queue_id;
    //             if (isset($filteredStdClass[$roomId]) && count($filteredStdClass[$roomId]) > 0) {
    //                 $permission = $filteredStdClass[$roomId][0]->permission;
    //                 if ($permission === 0) {
    //                     unset($smsTemplates[$index]);
    //                 }
    //             }
    //         }
    //         $smsTemplates = array_values($smsTemplates);

    //         foreach ($emailTemplates as $index => $room) {
    //             $roomId = $room->queue_id;
    //             if (isset($filteredStdClass[$roomId]) && count($filteredStdClass[$roomId]) > 0) {
    //                 $permission = $filteredStdClass[$roomId][0]->permission;
    //                 if ($permission === 0) {
    //                     unset($emailTemplates[$index]);
    //                 }
    //             }
    //         }
    //         $emailTemplates = array_values($emailTemplates);

    //         foreach ($filteredStdClass as $key => $value) {
    //             if ($value[0]->permission === 0) {
    //                 unset($filteredStdClass[$key]);
    //             }
    //         }

    //         $i = 0;
    //         $j = 0;

    //         if (! is_null($smsTemplates)) {
    //             foreach ($filteredStdClass as $queueId => $permissions) {
    //                 if ($smsTemplates[$i]->queue_id === $queueId) {
    //                     $smsTemplates[$i]->permission = $permissions[0]->permission;
    //                     $i++;
    //                 }
    //             }
    //         } else {
    //             $smsTemplates = [];
    //         }

    //         if (! is_null($emailTemplates)) {
    //             foreach ($filteredStdClass as $queueId => $permissions) {
    //                 if ($emailTemplates[$j]->queue_id === $queueId) {
    //                     $emailTemplates[$j]->permission = $permissions[0]->permission;
    //                     $j++;
    //                 }
    //             }
    //         } else {
    //             $emailTemplates = [];
    //         }
    //     }

    //     return view('auth-profile.email-notice', compact('smsTemplates', 'emailTemplates'));
    // }

   public function index()
{
    $prUserId = Auth::user()->pr_user_id;
    $user_role = Auth::user()->role;

    // Number of items per page
    $perPage = 2;

    if ($user_role == 1) {
        $filteredStdClass = [];

        $emailTemplates = DB::table('queuetb_queue_room AS qr')
            ->select('qr.id AS queue_id', 'qr.queue_room_name', 'qr.queue_room_icon', 'qr.parent_user_id', 'est_email.*', 'users.company_person_name')
            ->join('queuetb_email_sms AS est_email', 'qr.email_notice_tempid', '=', 'est_email.id')
            ->leftJoin('queuetb_users AS users', function ($join) {
                $join->on('est_email.last_modified_by', '=', 'users.id');
            })
            ->where('qr.parent_user_id', $prUserId)
            ->paginate($perPage);

        $smsTemplates = DB::table('queuetb_queue_room AS qr')
            ->select('qr.id AS queue_id', 'qr.queue_room_name', 'qr.queue_room_icon', 'qr.parent_user_id', 'est_sms.*', 'users.company_person_name')
            ->join('queuetb_email_sms AS est_sms', 'qr.sms_notice_tempid', '=', 'est_sms.id')
            ->leftJoin('queuetb_users AS users', function ($join) {
                $join->on('est_sms.last_modified_by', '=', 'users.id');
            })
            ->where('qr.parent_user_id', $prUserId)
            ->paginate($perPage);

        foreach ($smsTemplates as $room) {
            $room->permission = '';
        }
        foreach ($emailTemplates as $rooms) {
            $rooms->permission = '';
        }
    } else {

        $emailTemplates = DB::table('queuetb_queue_room AS qr')
            ->select('qr.id AS queue_id', 'qr.queue_room_name', 'qr.queue_room_icon', 'qr.parent_user_id', 'est_email.*', 'users.company_person_name')
            ->join('queuetb_email_sms AS est_email', 'qr.email_notice_tempid', '=', 'est_email.id')
            ->leftJoin('queuetb_users AS users', function ($join) {
                $join->on('est_email.last_modified_by', '=', 'users.id');
            })
            ->where('qr.parent_user_id', $prUserId)
            ->paginate($perPage);



        $smsTemplates = DB::table('queuetb_queue_room AS qr')
            ->select('qr.id AS queue_id', 'qr.queue_room_name', 'qr.queue_room_icon', 'qr.parent_user_id', 'est_sms.*', 'users.company_person_name')
            ->join('queuetb_email_sms AS est_sms', 'qr.sms_notice_tempid', '=', 'est_sms.id')
            ->leftJoin('queuetb_users AS users', function ($join) {
                $join->on('est_sms.last_modified_by', '=', 'users.id');
            })
            ->where('qr.parent_user_id', $prUserId)
            ->paginate($perPage);

        $permissions = PermissionAccess::where('user_id', auth()->id())->value('queue_room_access');

        $originalArray = json_decode($permissions);
        $filteredArray = [];

        foreach ($originalArray as $key => $value) {
            foreach ($value as $obj) {
                if ($obj->module_id === 5) {
                    $filteredArray[$key][] = $obj;
                    break;
                }
            }
        }

        $filteredStdClass = [];
        foreach ($filteredArray as $key => $value) {
            $filteredStdClass[$key] = $value;
        }

        foreach ($smsTemplates as $index => $room) {
            $roomId = $room->queue_id;
            if (isset($filteredStdClass[$roomId]) && count($filteredStdClass[$roomId]) > 0) {
                $permission = $filteredStdClass[$roomId][0]->permission;
                if ($permission === 0) {
                    $smsTemplates->forget($index);
                }
            }
        }
        $smsTemplates = $smsTemplates->values();

        foreach ($emailTemplates as $index => $room) {
            $roomId = $room->queue_id;
            if (isset($filteredStdClass[$roomId]) && count($filteredStdClass[$roomId]) > 0) {
                $permission = $filteredStdClass[$roomId][0]->permission;
                if ($permission === 0) {
                    $emailTemplates->forget($index);
                }
            }
        }
        $emailTemplates = $emailTemplates->values();

        foreach ($filteredStdClass as $key => $value) {
            if ($value[0]->permission === 0) {
                $filteredStdClass = array_diff_key($filteredStdClass, [$key => '']);
            }
        }

        $i = 0;
        $j = 0;

        if ($smsTemplates->isNotEmpty()) {
            foreach ($filteredStdClass as $queueId => $permissions) {
                if ($smsTemplates[$i]->queue_id === $queueId) {
                    $smsTemplates[$i]->permission = $permissions[0]->permission;
                    $i++;
                }
            }
        }

        if ($emailTemplates->isNotEmpty()) {
            foreach ($filteredStdClass as $queueId => $permissions) {
                if ($emailTemplates[$j]->queue_id === $queueId) {
                    $emailTemplates[$j]->permission = $permissions[0]->permission;
                    $j++;
                }
            }
        }
    }
 
    return view('auth-profile.email-notice', compact('smsTemplates', 'emailTemplates'));
}


    // public function index()
    // {
    //     $prUserId = Auth::user()->pr_user_id;
    //     $user_role = Auth::user()->role;

    //     $emailTemplates = DB::table('queuetb_queue_room AS qr')
    //         ->select('qr.id AS queue_id', 'qr.queue_room_name', 'qr.queue_room_icon', 'qr.parent_user_id', 'est_email.*', 'users.company_person_name')
    //         ->join('queuetb_email_sms AS est_email', 'qr.email_notice_tempid', '=', 'est_email.id')
    //         ->leftJoin('queuetb_users AS users', 'est_email.last_modified_by', '=', 'users.id')
    //         ->where('qr.parent_user_id', $prUserId)
    //         ->get();

    //     $smsTemplates = DB::table('queuetb_queue_room AS qr')
    //         ->select('qr.id AS queue_id', 'qr.queue_room_name', 'qr.queue_room_icon', 'qr.parent_user_id', 'est_sms.*', 'users.company_person_name')
    //         ->join('queuetb_email_sms AS est_sms', 'qr.sms_notice_tempid', '=', 'est_sms.id')
    //         ->leftJoin('queuetb_users AS users', 'est_sms.last_modified_by', '=', 'users.id')
    //         ->where('qr.parent_user_id', $prUserId)
    //         ->get();

    //     $smsTemplates = $smsTemplates->toArray();
    //     $emailTemplates = $emailTemplates->toArray();

    //     if ($user_role !== 1) {
    //         $permissions = PermissionAccess::where('user_id', auth()->id())->value('queue_room_access');
    //         $originalArray = json_decode($permissions, true);

    //         foreach ($smsTemplates as $index => $room) {
    //             $roomId = $room->queue_id;
    //             if (isset($originalArray[$roomId]) && count($originalArray[$roomId]) > 0) {
    //                 $permission = $originalArray[$roomId][0]['permission'];
    //                 if ($permission === 0) {
    //                     unset($smsTemplates[$index]);
    //                 }
    //             }
    //         }
    //     }

    //     // Now you have $smsTemplates and $emailTemplates ready for further processing
    // }

    public function editSms($id)
    {
        $template = EmailSmsTemplate::find($id);
        return view('queue-room.edit_sms', ['template' => $template]);
    }

    public function updateSmsTemp(Request $request, $id)
    {
        $template = EmailSmsTemplate::find($id);
        $template->sms_template_name = $request->input('SMSTemplate');
        $template->html_content = $request->input('html_content');
        $template->save();

        return redirect('email-notice')->with('success', 'SMS template updated successfully');
    }

    public function editEmail($id)
    {
        $template = EmailSmsTemplate::find($id);
        return view('queue-room.edit_email', ['template' => $template]);
    }

    public function updateEmailTemp(Request $request, $id)
    {
        $template = EmailSmsTemplate::find($id);
        $template->email_template_name = $request->input('SMSTemplate');
        $template->html_content = $request->input('html_content');
        $template->save();

        return redirect('email-notice')->with('success', 'Email template updated successfully');
    }
    // public function addEmail()
    // {
    //     // echo "Add Email in Email Notice Controller ";
    //     return view('auth-profile.add-email-notice');
    // }

    // public function saveEmail(Request $request)
    // {
    //     $request->validate([
    //         'email_template' => 'required',
    //         'email_template_name' => 'required',
    //         'emailNotice_status' => 'required',
    //     ]);

    //     $emNotice = new EmailNotice();
    //     $emNotice->email_template = $request->input('email_template');
    //     $emNotice->email_template_name = $request->input('email_template_name');
    //     $emNotice->status = $request->input('emailNotice_status');
    //     $emNotice->user_id = auth()->id(); // Assuming you are using Laravel's built-in authentication
    //     $emNotice->save();

    //     Session::flash('success', '<i class="fa fa-check-circle"></i> Email Notice details saved successfully');

    //     return redirect('email-notice');
    // }

    public function deleteEmail($id)
    {
        $emailNotice = EmailSmsTemplate::findOrFail($id);
        $emailNotice->delete();
        Session::flash('success', '<i class="fa fa-check-circle"></i> Staff member and their permissions deleted successfully!');
        return redirect()->back()->with('success', 'Email notice deleted successfully.');
    }

    //deleteSms

    // public function editEmail($id)
    // {
    //     $email_notice = EmailNotice::findOrFail($id);
    //     return view('auth-profile.edit-email-notice', compact('email_notice'));
    // }

    public function updateEmail(Request $request, $id)
    {
        $email_notice = EmailNotice::findOrFail($id);

        // Update each field individually
        $email_notice->email_template = $request->input('email_template');
        $email_notice->email_template_name = $request->input('email_template_name');
        $email_notice->status = $request->input('emailNotice_status');
        $email_notice->save();

        Session::flash('success', '<i class="fa fa-check-circle"></i> Email Notice details updated successfully');

        return redirect('email-notice');
    }
}
