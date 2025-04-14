<?php

namespace App\Http\Controllers;

use \Log;
use App\Exports\PasscodesExport;
use App\Imports\PasscodesImport;
use App\Models\BypassTemplate;
use App\Models\EmailSmsTemplate;
use App\Models\Language;
use App\Models\PermissionAccess;
use App\Models\QueuedbUser;
use App\Models\QueueRoom;
use App\Models\QueueRoomTemplate;
use App\Models\QueuetbDesignTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Redis;

use App\Helpers\Fileupload;
use App\Helpers\Logics;
use Aws\S3\S3Client;
use Carbon\Carbon;

class QueueroomController extends Controller
{
    public function __construct()
    {
        $this->this_epoch_time = time();
        $this->default_language = "en";
    }

    public function setup(Request $request, QueueRoom $new)
    {
        // if ($request->input('saveasdraft')) {
        //     Session::put('queue_draft', $request->post());
        //     if ($request->hasFile('queue_icon')) {
        //         //$filePath = $request->file('queue_icon')->store();
        //         $image = $request->file('queue_icon');
        //         $imageName = time() . '.' . $image->getClientOriginalExtension();
        //         $image->move(public_path('images'), $imageName);
        //         // Store file paths in the session
        //         Session::put('file_paths', $imageName);
        //     }
        //     return redirect()->route('createtemplate.inline.room');
        // }
        if (Session::has('file_paths')) {
            $session_file_data['file_paths'] = Session::get('file_paths');
        }

        //dd( $request->post());
        $isUploaded = false;
        $backendTimeZone = date_default_timezone_get();
        $tomezone = $request->input('timezone');
        $timezone1 = explode('|', $tomezone);

        $started = $request->input('startTime');
        if ($started == 1) {
            $start_date1 = $request->input('startDateValue');
            $start_time1 = $request->input('startTimeValue');
        } else {
            $start_date1 = $request->input('custom_start_date');
            $start_time1 = $request->input('custom_start_time');
        }

        $server_time_epoch = $this->convertToTimeZone($start_date1, $start_time1, $timezone1[0]);

        $ended = $request->input('endTime');
        if ($ended == 1) {
            $end_date1 = $request->input('endDateValue');
            $end_time1 = $request->input('endTimeValue');
        } else {
            $end_date1 = $request->input('custom_end_date');
            $end_time1 = $request->input('custom_end_time');
        }
        $end_server_time_epoch = $this->convertToTimeZone($end_date1, $end_time1, $timezone1[0]);

        $user = Auth::user();
        $prUserId = $user->pr_user_id;
        $userId = $user->id;
        $userIds = QueuedbUser::where('pr_user_id', '=', $userId)->pluck('id')->toArray();
        $permissionArray = PermissionAccess::whereIn('user_id', $userIds)->get(['queue_room_access', 'id'])->toArray();

        $data = $request->input('advancedata');

        if ($data['value'][0] == '') {
            $arr = [];
        } else {
            $arr = [];
            $i = 0;
            foreach ($data['condition_place'] as $key => $place) {
                $row = [
                    'operator' => $i == 0 ? null : $data['operator'][$i - 1],
                    'condition_place' => $place,
                    'condition' => $data['condition'][$i],
                    'value' => $data['value'][$i],
                ];
                $i++;
                $arr[] = $row;
            }
        }

        try {
            $data = $request->validate([
                'roomname' => 'required',
                'timezone' => 'required',
                'startTime' => 'required',
                'endTime' => 'required',
                'template_id' => 'required|numeric',
                'session_type' => 'required',
                'QueueRoomDesign_id' => 'required|numeric',
                'queue_icon' => 'required|image|max:1024',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            dd($e);

            return back()->withErrors($e->errors())->withInput();
        }

        if ($request->input('template_id') == 0) {

            $queueRoomTemplate = new QueueRoomTemplate();
            $queueRoomTemplate->template_name = strip_tags($request->input('template_name'));
            $queueRoomTemplate->input_url = strip_tags($request->input('input_url'));
            $queueRoomTemplate->protection_level = strip_tags($request->input('protection') ?? '0');
            $queueRoomTemplate->is_advance_setting = strip_tags($request->has('AdvanceSettingCheckBox') ? 1 : 0);
            $queueRoomTemplate->parent_user_id = $prUserId;
            if ($queueRoomTemplate->is_advance_setting == 1) {
                $queueRoomTemplate->advance_setting_rules = json_encode($arr);
            }
            $userId = Auth::id();
            $queueRoomTemplate->last_modified_by = $userId;
            $queueRoomTemplate->save();
            $savedtemplateId = $queueRoomTemplate->id;
        } else {
            $savedtemplateId = $request->input('template_id');
        }

        if ($request->hasFile('queue_icon')) {
            $image = $request->file('queue_icon');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
        } elseif (! empty($session_file_data['file_paths'])) {
            $imageName = $session_file_data['file_paths'];
            Session::forget('file_paths');
        } else {
            $imageName = '';
        }

        if ($request->input('QueueRoomDesign_id') == 0) {
            $designtempdata = [
                'template_name' => strip_tags($request->input('QueueRoomDesignTemplate_name')),
                'languages' => json_encode(strip_tags($request->input('queue_language'))),
                'default_language' => strip_tags($request->input('design-temp-setDefault')),
                'last_modified_by' => $userId,
                'parent_user_id' => $prUserId,
                'updated_at' => date('d-M-Y H:i:s', time()),
            ];

            $designtemplateId = DB::table('queuetb_design_template')->insertGetId($designtempdata);

            /** Checking bucket is exist or not */
            $bucket = 'static-template-queue-' . $designtemplateId . '-' . $prUserId;
            $bucketExists = Logics::checkBucket($bucket);
            // return $bucketExists;die;
            if (!empty($bucketExists)) {
                $htmlqueue = Logics::getDummyCode('queue_page_tab');
                $htmlpostqueue = Logics::getDummyCode('pre_queue_page_tab');
                $htmlpriorityaccess = Logics::getDummyCode('postqueue_page_tab');
                $htmlprequeue = Logics::getDummyCode('priority_access_page_tab');
                Fileupload::uploadTemplateInS3($htmlqueue, 'queue', $prUserId, $designtemplateId);
                Fileupload::uploadTemplateInS3($htmlpostqueue, 'postqueue', $prUserId, $designtemplateId);
                Fileupload::uploadTemplateInS3($htmlpriorityaccess, 'priorityaccess', $prUserId, $designtemplateId);
                Fileupload::uploadTemplateInS3($htmlprequeue, 'prequeue', $prUserId, $designtemplateId);
            }
        } else {
            $designtemplateId = $request->input('QueueRoomDesign_id');
            /** Checking bucket is exist or not */
            $bucket = 'static-template-queue-' . $designtemplateId . '-' . $prUserId;
            $bucketExists = Logics::checkBucket($bucket);
            if (!empty($bucketExists)) {
                $htmlqueue = Logics::getDummyCode('queue_page_tab');
                $htmlpostqueue = Logics::getDummyCode('pre_queue_page_tab');
                $htmlpriorityaccess = Logics::getDummyCode('postqueue_page_tab');
                $htmlprequeue = Logics::getDummyCode('priority_access_page_tab');
                Fileupload::uploadTemplateInS3($htmlqueue, 'queue', $prUserId, $designtemplateId);
                Fileupload::uploadTemplateInS3($htmlpostqueue, 'postqueue', $prUserId, $designtemplateId);
                Fileupload::uploadTemplateInS3($htmlpriorityaccess, 'priorityaccess', $prUserId, $designtemplateId);
                Fileupload::uploadTemplateInS3($htmlprequeue, 'prequeue', $prUserId, $designtemplateId);
            }
        }

        if ($request->input('SetupBypass') == 1) {
            try {
                $request->validate([
                    'byPassSelectTemplateid' => 'required|numeric',
                    'Bypassurl' => 'required|url',
                ]);
                if ($request->input('byPassSelectTemplateid') == 0) {
                    $bypassTemplate = new BypassTemplate();
                    $bypassTemplate->template_name = strip_tags($request->input('byPassSelectTemplate_name'));
                    $bypassTemplate->bypass_url = strip_tags($request->input('Bypassurl'));
                    $bypassTemplate->parent_user_id = $prUserId;
                    $bypassTemplate->save();
                    $bypassTemplateId = $bypassTemplate->id;
                } else {
                    $bypassTemplateId = $request->input('byPassSelectTemplateid');
                }
            } catch (\Illuminate\Validation\ValidationException $e) {
                dd($e);
                return back()->withErrors($e->errors())->withInput();
            }
        } else {
            $bypassTemplateId = null;
        }

        if ($bypassTemplateId !== null && $request->hasFile('filebyPass')) {
            $bypass_tamp_id = $bypassTemplateId;
            Excel::import(new PasscodesImport($bypass_tamp_id), $request->file('filebyPass'));
        }

        $SMSCreateTemplate = $request->input('SMSCreateTemplate');
        $SMSTemplate = $request->input('SMSTemplate');
        $EmailCreateTemplate = $request->input('EmailCreateTemplate');
        $EmailTemplate = $request->input('EmailTemplate');
        if (! empty($SMSTemplate) || ! empty($SMSCreateTemplate)) {
            if ($SMSCreateTemplate == 0) {
                $smsTemplate = new EmailSmsTemplate();
                $smsTemplate->sms_template_name = $SMSTemplate;
                $smsTemplate->status = 1;
                $smsTemplate->parent_user_id = $prUserId;
                $smsTemplate->last_modified_by = $userId;
                $smsTemplate->html_content = $request->input('editorsmsContent');
                $smsTemplate->save();

                $smstempId = $smsTemplate->id;
            } else {
                $smstempId = $SMSCreateTemplate;
            }
        }
        //dd( $smstempId) ;

        if (! empty($EmailTemplate) || ! empty($EmailCreateTemplate)) {
            if ($EmailCreateTemplate == 0) {
                $emailTemplate = new EmailSmsTemplate();
                $emailTemplate->email_template_name = $EmailTemplate;
                $emailTemplate->status = 2;
                $emailTemplate->parent_user_id = $prUserId;
                $emailTemplate->last_modified_by = $userId;
                $emailTemplate->html_content = strip_tags($request->input('editoremailContent'));
                $emailTemplate->save();

                $emailtempId = $emailTemplate->id;
            } else {
                $emailtempId = $EmailCreateTemplate;
            }
        }

        if ($request->input('CustomURl') == 1) {
            if ($request->input('template_id') == 0) {
                $target_url = $request->input('input_url');
            } else {
                $target_url = QueueRoomTemplate::select('input_url')
                    ->findOrFail($request->input('template_id'))
                    ->input_url;
            }
        } else {
            $target_url = $request->input('custom_url');
        }

        $startDateTime = $request->input('custom_start_date') . ' ' . $request->input('custom_start_time');
        $startEpoch = strtotime($startDateTime);
        $endDateTime = $request->input('custom_end_date') . ' ' . $request->input('custom_end_time');
        $endEpoch = strtotime($endDateTime);

        $queueRoom = new QueueRoom();
        $queueRoom->queue_room_name = strip_tags($request->input('roomname'));
        $queueRoom->queue_room_type = strip_tags($request->input('queuetype'));
        $queueRoom->queue_room_icon = $imageName;
        $queueRoom->queue_timezone = $timezone1[0];
        $queueRoom->queue_timezone_name = $timezone1[1];
        $queueRoom->is_started = strip_tags($request->input('startTime'));
        $queueRoom->start_date = $start_date1;
        $queueRoom->start_time = $start_time1;
        if ($end_server_time_epoch > $this->this_epoch_time) {
            $queueRoom->is_ended = strip_tags(0);
        } else {
            $queueRoom->is_ended = strip_tags($request->input('endTime'));
        }
        $queueRoom->end_date = $end_date1;
        $queueRoom->end_time = $end_time1;

        $queueRoom->timezone_based_start_datetime = strip_tags($request->input('convertedstartDateTime'));
        $queueRoom->timezone_based_start_datetime_epoch = $startEpoch;
        $queueRoom->timezone_based_end_datetime = strip_tags($request->input('convertedEndDateTime'));
        $queueRoom->timezone_based_end_datetime_epoch = $endEpoch;

        $queueRoom->start_time_epoch = $server_time_epoch;
        $queueRoom->end_time_epoch = $end_server_time_epoch;

        $queueRoom->sms_notice_tempid = $smstempId ?? null;
        $queueRoom->email_notice_tempid = $emailtempId ?? null;
        $queueRoom->queue_room_template_id = $savedtemplateId;
        $queueRoom->is_customurl = strip_tags($request->input('CustomURl'));
        $queueRoom->target_url = $target_url;
        $queueRoom->max_traffic_visitor = strip_tags($request->input('max_traffic'));
        $queueRoom->enable_bypass = strip_tags($request->input('SetupBypass') ?? 0);
        $queueRoom->bypass_template_id = $bypassTemplateId ?? null;
        $queueRoom->is_prequeue = strip_tags($request->input('preQueueSetup'));
        $queueRoom->prequeue_starttime = strip_tags($request->input('BeforeTimeforPrequeue'));
        $queueRoom->queue_room_design_tempid = $designtemplateId;
        $queueRoom->last_modified_by = $user->id;
        $queueRoom->parent_user_id = $prUserId;
        $queueRoom->session_type = $request->input('session_type');
        $queueRoom->current_space = strip_tags($request->input('max_traffic'));
        $queueRoom->time_input = $request->input('time_input') ? strip_tags($request->input('time_input')) : null;

        if ($request->input('saveasdraft')) {
            $queueRoom->is_draft = 1;
        }

        $queueRoom->save();
        // print_r($queueRoom);die();
        $insertedQueueRoomId = $queueRoom->id;

        /** upload and update S3 uploaded file URL in table | start */
        if (!empty($request->file('queueHtmlFile'))) {
            $isUploaded = true;
            $uploadqueueHtmlFile = Fileupload::uploadFileInS3($request->file('queueHtmlFile'), 'queue', $prUserId, $insertedQueueRoomId);
            if (!$uploadqueueHtmlFile) {
                $uploadqueueHtmlFile = $uploadqueueHtmlFile;
            } else {
                $uploadqueueHtmlFile = null;
            }
        }

        if (!empty($request->file('preQueueHtmlFile'))) {
            $isUploaded = true;
            $uploadpreQueueHtmlFile = Fileupload::uploadFileInS3($request->file('preQueueHtmlFile'), 'prequeue', $prUserId, $insertedQueueRoomId);
            if (!$uploadpreQueueHtmlFile) {
                $uploadpreQueueHtmlFile = $uploadpreQueueHtmlFile;
            } else {
                $uploadpreQueueHtmlFile = null;
            }
        }

        if (!empty($request->file('postQueueHtmlFile'))) {
            $isUploaded = true;
            $uploadpostQueueHtmlFile = Fileupload::uploadFileInS3($request->file('postQueueHtmlFile'), 'postqueue', $prUserId, $insertedQueueRoomId);
            if (!$uploadpostQueueHtmlFile) {
                $uploadpostQueueHtmlFile = $uploadpostQueueHtmlFile;
            } else {
                $uploadpostQueueHtmlFile = null;
            }
        }

        if (!empty($request->file('priorityAccessPageHtmlFile'))) {
            $isUploaded = true;
            $uploadpriorityAccessPageHtmlFile = Fileupload::uploadFileInS3($request->file('priorityAccessPageHtmlFile'), 'priorityaccess', $prUserId, $insertedQueueRoomId);
            if (!$uploadpriorityAccessPageHtmlFile) {
                $uploadpriorityAccessPageHtmlFile = $uploadpriorityAccessPageHtmlFile;
            } else {
                $uploadpriorityAccessPageHtmlFile = null;
            }
        }

        if ($request->input('QueueRoomDesign_id') == 0 && !empty($request->input('QueueRoomDesignTemplate_name')) && empty($request->file('queueHtmlFile')) && empty($request->file('preQueueHtmlFile')) && empty($request->file('postQueueHtmlFile')) && empty($request->file('priorityAccessPageHtmlFile'))) {
            $htmlqueue = Logics::getDummyCode('queue_page_tab');
            $htmlpostqueue = Logics::getDummyCode('pre_queue_page_tab');
            $htmlpriorityaccess = Logics::getDummyCode('postqueue_page_tab');
            $htmlprequeue = Logics::getDummyCode('priority_access_page_tab');

            $uploadqueueHtmlFile = Fileupload::uploadFileInS3($htmlqueue, 'queue', $prUserId, $insertedQueueRoomId, 1);
            $uploadpostQueueHtmlFile = Fileupload::uploadFileInS3($htmlpostqueue, 'postqueue', $prUserId, $insertedQueueRoomId, 1);
            $uploadpriorityAccessPageHtmlFile = Fileupload::uploadFileInS3($htmlpriorityaccess, 'priorityaccess', $prUserId, $insertedQueueRoomId, 1);
            $uploadpreQueueHtmlFile = Fileupload::uploadFileInS3($htmlprequeue, 'prequeue', $prUserId, $insertedQueueRoomId, 1);
            $isUploaded = false;
        } else {
            $htmlqueue = Logics::getDummyCode('queue_page_tab');
            $htmlpostqueue = Logics::getDummyCode('pre_queue_page_tab');
            $htmlpriorityaccess = Logics::getDummyCode('postqueue_page_tab');
            $htmlprequeue = Logics::getDummyCode('priority_access_page_tab');

            $uploadqueueHtmlFile = Fileupload::uploadFileInS3($htmlqueue, 'queue', $prUserId, $insertedQueueRoomId, 1);
            $uploadpostQueueHtmlFile = Fileupload::uploadFileInS3($htmlpostqueue, 'postqueue', $prUserId, $insertedQueueRoomId, 1);
            $uploadpriorityAccessPageHtmlFile = Fileupload::uploadFileInS3($htmlpriorityaccess, 'priorityaccess', $prUserId, $insertedQueueRoomId, 1);
            $uploadpreQueueHtmlFile = Fileupload::uploadFileInS3($htmlprequeue, 'prequeue', $prUserId, $insertedQueueRoomId, 1);
            $isUploaded = false;
        }

        $updateQueue = QueueRoom::where('id', $insertedQueueRoomId)->update([
            'is_uploaded' => $isUploaded,
            'queue_html_page_url' => !empty($uploadqueueHtmlFile) ? $uploadqueueHtmlFile : null,
            'postqueue_html_page_url' => !empty($uploadpreQueueHtmlFile) ? $uploadpreQueueHtmlFile : null,
            'priorityqueue_html_page_url' => !empty($uploadpostQueueHtmlFile) ? $uploadpostQueueHtmlFile : null,
            'prequeue_html_page_url' => !empty($uploadpriorityAccessPageHtmlFile) ? $uploadpriorityAccessPageHtmlFile : null,
        ]);
        /** update S3 uploaded file URL in table | end */
        if (!empty($designtemplateId)) {
            $query = "UPDATE in_line_tamplates SET room_id = '{$insertedQueueRoomId}' WHERE template_id =" . $designtemplateId;
            DB::select($query);
        }
        foreach ($permissionArray as &$permission) {
            $access = json_decode($permission['queue_room_access'], true);
            $access[$insertedQueueRoomId] = [
                ['module_id' => 1, 'permission' => 0],
                ['module_id' => 2, 'permission' => 0],
                ['module_id' => 3, 'permission' => 0],
                ['module_id' => 4, 'permission' => 0],
                ['module_id' => 5, 'permission' => 0],
                ['module_id' => 6, 'permission' => 0],
            ];
            $permission['queue_room_access'] = json_encode($access);
            PermissionAccess::where('id', $permission['id'])->update(['queue_room_access' => $permission['queue_room_access']]);
        }
        if (Session::has('queue_draft')) {
            Session::forget('queue_draft');
        }
        return redirect()->route('queue-room-view', ['id' => $insertedQueueRoomId])->with('success', 'Queue room created successfully.');
        /* if ($request->input('saveasdraft')) {
            return redirect()->route('stats.edit', ['id' => $insertedQueueRoomId]);
        } else {
            return redirect()->route('queue-room-view', ['id' => $insertedQueueRoomId])->with('success', 'Queue room created successfully.');
        } */
    }

    public function convertToTimeZone($start_date1, $start_time1, $inputTimeZoneOffset)
    {
        preg_match('/([+\-])(\d{2}):(\d{2})/', $inputTimeZoneOffset, $matches);
        $sign = $matches[1];
        $hours = (int) $matches[2];
        $minutes = (int) $matches[3];

        $dateTimeString = $start_date1 . ' ' . $start_time1;
        $startepochTime = strtotime($dateTimeString);
        $offset_seconds = ($hours * 3600) + ($minutes * 60);
        if ($sign == '+') {
            $new_time = $startepochTime - $offset_seconds;
        } elseif ($sign == '-') {
            $new_time = $startepochTime + $offset_seconds;
        }
        return $new_time;
    }

    public function dateandtime($date, $time)
    {
        $customStartDate = $date;
        $customStartTime = $time;

        // Convert custom start date to DateTime object
        $dateTime = \DateTime::createFromFormat('j F Y H:i', $customStartDate . ' ' . $customStartTime);

        // Check if DateTime object was created successfully
        if ($dateTime == false) {
            // Handle the error, e.g., by returning null or throwing an exception

            return null;
        }

        // Convert DateTime object to MySQL datetime format
        return $dateTime->format('Y-m-d H:i:s');
    }

    // public function viewpage(Request $request)
    // {
    //     $prUserId = Auth::user()->pr_user_id;
    //     $user_role = Auth::user()->role;

    //     if ($user_role == 1) {
    //         $queueroomwithpermission = QueueRoom::where('parent_user_id', $prUserId)->get();
    //         $queueroomwithpermission = $queueroomwithpermission->toArray();
    //         foreach ($queueroomwithpermission as &$room) {
    //             $room['permission'] = '';
    //         }
    //     } else {
    //         $queueRoomSetups = QueueRoom::where('parent_user_id', $prUserId)->get();
    //         $queueroomwithpermission = $queueRoomSetups->toArray();
    //         $permissions = PermissionAccess::where('user_id', auth()->id())->value('queue_room_access');
    //         $originalArray = json_decode($permissions);
    //         $filteredArray = [];

    //         foreach ($originalArray as $key => $value) {
    //             foreach ($value as $obj) {
    //                 if ($obj->module_id == 2) {
    //                     $filteredArray[$key][] = $obj;
    //                     break;
    //                 }
    //             }
    //         }

    //         $filteredStdClass = [];
    //         foreach ($filteredArray as $key => $value) {
    //             $filteredStdClass[$key] = $value;
    //         }

    //         foreach ($queueroomwithpermission as $index => $room) {
    //             $roomId = $room['id'];
    //             if (isset($filteredStdClass[$roomId]) && count($filteredStdClass[$roomId]) > 0) {
    //                 $permission = $filteredStdClass[$roomId][0]->permission;
    //                 if ($permission == 0) {
    //                     unset($queueroomwithpermission[$index]);
    //                 }
    //             }
    //         }
    //         $queueroomwithpermission = array_values($queueroomwithpermission);

    //         foreach ($filteredStdClass as $key => $value) {
    //             if ($value[0]->permission == 0) {
    //                 unset($filteredStdClass[$key]);
    //             }
    //         }

    //         $i = 0;
    //         if (! is_null($queueroomwithpermission)) {
    //             foreach ($filteredStdClass as $queueId => $permissions) {
    //                 if ($queueroomwithpermission[$i]['id'] == $queueId) {
    //                     $queueroomwithpermission[$i]['permission'] = $permissions[0]->permission;
    //                     $i++;
    //                 }
    //             }
    //         } else {
    //             $queueroomwithpermission = [];
    //         }
    //     }

    //     return view('queue-room.queueRoom', compact('queueroomwithpermission'));
    // }

    public function viewpage(Request $request)
    {
        $user = Auth::user();
        $prUserId = $user->pr_user_id;
        $user_role = $user->role;

        /** This code for update the room start and end time | start */
        $updateQueueRoom = QueueRoom::select('id', 'is_ended', 'start_time_epoch', 'end_time_epoch', 'end_date', 'end_time', 'queue_timezone')->where('parent_user_id', $prUserId)->get();
        foreach ($updateQueueRoom as $room_data) {
            $this_start_time_epoch = (int)$room_data->start_time_epoch;
            $this_id = $room_data->id;

            if ($this_start_time_epoch < $this->this_epoch_time) {
                DB::table('queuetb_queue_room')
                    ->where('id', $this_id)
                    ->update(['is_started' => 1]);
            }

            if ($room_data->end_time_epoch > $this->this_epoch_time && $room_data->is_ended == 0 && $room_data->end_date == NULL) {
                DB::table('queuetb_queue_room')
                    ->where('id', $this_id)
                    ->update(['is_ended' => 1]);
            }
            if ($room_data->end_date != NULL) {
                $room_data->end_date = date('Y-m-d', strtotime($room_data->end_date));
                $room_data->end_time = date('H:i:s', strtotime($room_data->end_time));

                $end_server_time_epoch = $this->convertToTimeZone($room_data->end_date, $room_data->end_time, $room_data->queue_timezone);

                DB::table('queuetb_queue_room')
                    ->where('id', $this_id)
                    ->update(['end_time_epoch' => $end_server_time_epoch]);


                $updateQueueRoomNew = QueueRoom::select('id', 'is_ended', 'start_time_epoch', 'end_time_epoch', 'end_date', 'end_time', 'queue_timezone')->where('parent_user_id', $prUserId)->where('id', $this_id)->first();

                // dump($end_server_time_epoch);
                // dump($this->this_epoch_time);
                // dump($this->this_epoch_time);


                if ($updateQueueRoomNew->end_time_epoch < $this->this_epoch_time && $updateQueueRoomNew->is_ended != 2) {
                    DB::table('queuetb_queue_room')
                        ->where('id', $this_id)
                        ->update(['is_ended' => 1]);
                }
            }
        }
        /** This code for update the room start and end time | end */

        // Base query for rooms
        $query = QueueRoom::where('parent_user_id', $prUserId);

// Prioritize Live rooms first, then Ended, then others
            $query = $query->orderByRaw("
                CASE 
                    WHEN is_started = 1 AND (is_ended != 1 OR (is_ended = 1 AND end_date = ? AND end_time > ?)) THEN 1  -- Live Rooms
                    WHEN is_ended = 1 THEN 2  -- Ended Rooms
                    ELSE 3  -- Others (Upcoming, Draft)
                END, 
                end_date DESC,  -- Highest priority to today’s date, then previous dates
                created_at DESC, -- Newest rooms first within the same date
                id DESC -- Maintain order if dates and created_at are the same
            ", [date('Y-m-d'), date('H:i:s')]);

            
            if ($request->status === "live") {
                $query = $query->where('is_started', 1)
                    ->where(function ($query) {
                        $query->where('is_ended', '!=', 1)
                            ->orWhere(function ($query) {
                                $query->where('is_ended', 1)
                                    ->where('end_date', '=', date('Y-m-d'))
                                    ->where('end_time', '>', date('H:i:s'));
                            });
                    })
                    ->where('is_draft', '!=', 1);
            } else if ($request->status === "ended") {
                $query = $query->where(function ($query) {
                    $query->where('is_ended', 1)
                        ->where(function ($query) {
                            $query->where('end_date', '<', date('Y-m-d'))
                                ->orWhere(function ($query) {
                                    $query->where('end_date', '=', date('Y-m-d'))
                                        ->where('end_time', '<=', date('H:i:s'));
                                });
                        });
                });
            } else if ($request->status === "upcoming") {
                $query = $query->where('is_started', 0);
            } else if ($request->status === "draft") {
                $query = $query->where('is_draft', 1);
            }
            

        if ($user_role == 1) {
            // If user role is admin (role = 1), set permission to empty for all rooms
            $queueRooms = $query->get(); // Use paginate to get LengthAwarePaginator

            $queueroomwithpermission = $queueRooms->map(function ($room) {
                $room['permission'] = '';
                return $room;
            });
            // echo "<pre>";
            // print_r($queueroomwithpermission);

            // Create a paginator instance with the filtered results
            $currentPage = $request->input('page', 1); // Get current page from request
            $perPage = 5; // Set the number of items per page
            $total = $queueRooms->count(); // Updated total count based on filtered results

            // Slice the filtered collection for current page
            $currentItems = $queueRooms->forPage($currentPage, $perPage);

            $queueroomwithpermission = new \Illuminate\Pagination\LengthAwarePaginator(
                $currentItems, // Items for current page
                $total,
                $perPage,
                $currentPage,
                ['path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath()]
            );
        } else {
            // Fetch permissions for the user
            $permissions = PermissionAccess::where('user_id', auth()->id())
                ->value('queue_room_access');
            $originalArray = json_decode($permissions, true);

            // Filter permissions where module_id is 2 and permission is not 0
            $filteredArray = array_filter($originalArray, function ($value) {
                return array_filter($value, fn($obj) => $obj['module_id'] == 2 && $obj['permission'] != 0);
            });

            // Convert filteredArray to stdClass format
            $filteredStdClass = array_map(function ($value) {
                return array_values($value); // Re-index filtered array
            }, $filteredArray);

            // Get all rooms and filter based on permissions
            $queueRooms = $query->get(); // Fetch all rooms
            $filteredRooms = $queueRooms->map(function ($room) use ($filteredStdClass) {
                $roomId = $room->id;
                if (isset($filteredStdClass[$roomId]) && count($filteredStdClass[$roomId]) > 0) {
                    $room['permission'] = $filteredStdClass[$roomId][0]['permission'];
                } else {
                    $room['permission'] = null;
                }
                return $room;
            })->filter(fn($room) => $room['permission'] !== null)->values();

            // Create a paginator instance with the filtered results
            $currentPage = $request->input('page', 1); // Get current page from request
            $perPage = 5; // Set the number of items per page
            $total = $filteredRooms->count(); // Updated total count based on filtered results

            // Slice the filtered collection for current page
            $currentItems = $filteredRooms->forPage($currentPage, $perPage);

            $queueroomwithpermission = new \Illuminate\Pagination\LengthAwarePaginator(
                $currentItems, // Items for current page
                $total,
                $perPage,
                $currentPage,
                ['path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath()]
            );
        }

        // Pass both the paginated and filtered results to the view
        return view('queue-room.queueRoom', compact('queueroomwithpermission', 'queueRooms'));
    }

    // public function viewpage(Request $request)
    // {
    //     $user = Auth::user();
    //     $prUserId = $user->pr_user_id;
    //     $user_role = $user->role;

    //     $queueRooms = QueueRoom::where('parent_user_id', $prUserId)->paginate(5);
    //     // $queueRooms = QueueRoom::where('parent_user_id', $prUserId)->get();

    //     if ($user_role == 1) {
    //         $queueroomwithpermission = $queueRooms->map(function($room) {
    //             $room['permission'] = '';
    //             return $room;
    //         });
    //     } else {
    //         $permissions = PermissionAccess::where('user_id', auth()->id())
    //             ->value('queue_room_access');
    //         $originalArray = json_decode($permissions, true);
    //         $filteredArray = array_filter($originalArray, function($value) {
    //             return array_filter($value, fn($obj) => $obj['module_id'] == 2);
    //         });

    //         $filteredStdClass = array_map(function($value) {
    //             return array_values(array_filter($value, fn($obj) => $obj['permission'] != 0));
    //         }, $filteredArray);

    //         $queueroomwithpermission = $queueRooms->map(function($room) use ($filteredStdClass) {
    //             $roomId = $room->id;
    //             if (isset($filteredStdClass[$roomId]) && count($filteredStdClass[$roomId]) > 0) {
    //                 $room['permission'] = $filteredStdClass[$roomId][0]['permission'];
    //             } else {
    //                 $room['permission'] = null;
    //             }
    //             return $room;
    //         })->filter(fn($room) => $room['permission'] !== null)->values();
    //     }

    //     return view('queue-room.queueRoom', compact('queueroomwithpermission'));
    // }

    public function viewpageCreateQueue(Request $request)
    {
        $prUserId = Auth::user()->pr_user_id;
        $languages = Language::all();
        $queuetemplates = QueueRoomTemplate::where('parent_user_id', $prUserId)->get();
        $bypasstemplates = BypassTemplate::where('parent_user_id', $prUserId)->get();
        $designtemplates = QueuetbDesignTemplate::where('parent_user_id', $prUserId)->get();
        $emailtemplates = EmailSmsTemplate::where('parent_user_id', $prUserId)->where('status', 2)->get();
        $smstemplates = EmailSmsTemplate::where('parent_user_id', $prUserId)->where('status', 1)->get();
        $serverTimezoneOffset = time();
        $session_data = [];
        $session_file_data = [];
        if (Session::has('queue_draft')) {
            $session_data = Session::get('queue_draft');
        }
        if (Session::has('file_paths')) {
            $session_file_data['file_paths'] = Session::get('file_paths');
        }
        return view('queue-room.createqueue', compact('languages', 'queuetemplates', 'bypasstemplates', 'designtemplates', 'smstemplates', 'emailtemplates', 'serverTimezoneOffset', 'session_data', 'session_file_data'));
    }

    public function viewQueueRoomDesign(Request $request)
    {
        $user = Auth::user();
        $prUserId = Auth::user()->pr_user_id;
        $user_role = Auth::user()->role;

        if ($user_role == 1) {
            // $queueRoomtemps = QueueRoom::where('parent_user_id', $user->pr_user_id)->get();
            $queueRoomtemp = QueueRoom::where('parent_user_id', $user->pr_user_id)->orderBy('id', 'desc')->paginate(5);
            $queueRoomtemps = $queueRoomtemp;
            // $queueRoomtemps = $queueRoomtemps->toArray();
            foreach ($queueRoomtemps as &$room) {
                $room['permission'] = '';
            }
        } else {
            // Fetch paginated QueueRoom records
            $queueRoomtemp = QueueRoom::where('parent_user_id', $user->pr_user_id)->paginate(12); // Here '12' is the per page value

            // Fetch permissions
            $permissions = PermissionAccess::where('user_id', auth()->id())->value('queue_room_access');
            $originalArray = json_decode($permissions);
            $filteredArray = [];
            $filteredStdClass = [];

            // Filter based on module_id == 3
            foreach ($originalArray as $key => $value) {
                foreach ($value as $obj) {
                    if ($obj->module_id == 3) {
                        $filteredArray[$key][] = $obj;
                        break;
                    }
                }
            }

            // Convert filtered array to stdClass format
            foreach ($filteredArray as $key => $value) {
                $filteredStdClass[$key] = $value;
            }

            // Remove queue rooms with no permissions
            $filteredQueueRooms = [];
            foreach ($queueRoomtemp as $room) {
                $roomId = $room->id;
                if (isset($filteredStdClass[$roomId]) && $filteredStdClass[$roomId][0]->permission != 0) {
                    $room->permission = $filteredStdClass[$roomId][0]->permission;
                    $filteredQueueRooms[] = $room;
                }
            }

            // Create a new paginator for the filtered results
            $page = request()->get('page', 1);
            $perPage = 2;
            $total = count($filteredQueueRooms);
            $startingPoint = ($page * $perPage) - $perPage;

            $queueRoomtemps = new \Illuminate\Pagination\LengthAwarePaginator(
                array_slice($filteredQueueRooms, $startingPoint, $perPage),
                $total,
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        }

        // return $queueRoomtemps;die;

        return view('queue-room.temp_queueDesign', compact('queueRoomtemps'));
    }

    public function queueRoomEdit($id)
    {
        $user_id = Auth::user()->id;
        $user_role = Auth::user()->role;
        if ($user_role !== 1) {
            // dd($user_role);
            $permission_for_this = 0;

            $query = 'SELECT queue_room_access  FROM queuetb_permission_access WHERE  user_id = ' . $user_id;
            // $common_module_id_data = DB::select($query);
            $common_module_id_data = DB::selectOne($query);
            if ($common_module_id_data) {
                // $common_module_id = $common_module_id_data[0]->queue_room_access;
                $common_module_id = $common_module_id_data->queue_room_access;
                $common_module_id = json_decode($common_module_id, true);

                $commonModuleData = $common_module_id[$id];

                foreach ($commonModuleData as $module) {
                    if ($module['module_id'] == 2) {
                        $permission_for_this = $module['permission'];
                    }
                }
            }
        } else {
            $permission_for_this = 2;
        }

        $queueRoom = DB::table('queuetb_queue_room as qr')
            ->select(
                'qr.id as queue_room_id',
                'qr.is_uploaded',
                'qr.queue_room_name',
                'qr.queue_room_type',
                'qr.queue_room_icon',
                'qr.queue_timezone',
                'qr.queue_timezone_name',
                'qr.is_started',
                'qr.start_date',
                'qr.start_time',
                'qr.is_ended',
                'qr.end_date',
                'qr.end_time',
                'qr.queue_room_template_id',
                'qr.is_customurl',
                'qr.target_url',
                'qr.max_traffic_visitor',
                'qr.currunt_traffic_count',
                'qr.enable_bypass',
                'qr.bypass_template_id',
                'qr.is_prequeue',
                'qr.prequeue_starttime',
                'qr.start_time_epoch',
                'qr.end_time_epoch',
                'qr.queue_room_design_tempid',
                'qr.sms_notice_tempid',
                'qr.email_notice_tempid',
                'qr.last_modified_by',
                'qr.timezone_based_start_datetime',
                'qr.timezone_based_end_datetime',
                'qr.queue_html_page_url',
                'qr.postqueue_html_page_url',
                'qr.priorityqueue_html_page_url',
                'qr.prequeue_html_page_url',
                'qr.session_type',
                'qr.time_input',
                'd.id as design_template_id',
                'd.template_name as design_template_name',
                'd.languages as design_template_languages',
                'd.default_language as design_template_default_language',
                'd.parent_user_id as design_template_parent_user_id',
                'd.updated_at as design_template_updated_at',
                'd.created_at as design_template_created_at',
                'd.last_modified_by as design_template_last_modified_by',
                't.id as room_template_id',
                't.template_name as room_template_name',
                't.input_url as room_template_input_url',
                // 't.protection_level as room_template_protection_level',
                't.is_advance_setting as room_template_is_advance_setting',
                't.advance_setting_rules as room_template_advance_setting_rules',
                't.last_modified_by as room_template_last_modified_by',
                'es_sms.id as sms_template_id',
                'es_sms.sms_template_name',
                'es_sms.html_content as sms_template_html_content',
                'es_sms.status as sms_template_status',
                'es_sms.parent_user_id as sms_template_parent_user_id',
                'es_sms.last_modified_by as sms_template_last_modified_by',
                'es_email.id as email_template_id',
                'es_email.email_template_name',
                'es_email.sms_template_name as email_template_sms_template_name',
                'es_email.html_content as email_template_html_content',
                'es_email.status as email_template_status',
                'es_email.parent_user_id as email_template_parent_user_id',
                'es_email.last_modified_by as email_template_last_modified_by',
                'bypass.id as bypass_temp_id',
                'bypass.template_name as bypass_temp_name',
                'bypass.bypass_url as bypass_temp_url',
                'bypass.id as bypass_temp_id',
                'bypass.id as bypass_temp_id',
            )
            ->join('queuetb_design_template as d', 'qr.queue_room_design_tempid', '=', 'd.id')
            ->join('queue_room_template as t', 'qr.queue_room_template_id', '=', 't.id')
            ->leftJoin('queuetb_email_sms as es_sms', 'qr.sms_notice_tempid', '=', 'es_sms.id')
            ->leftJoin('queuetb_email_sms as es_email', 'qr.email_notice_tempid', '=', 'es_email.id')
            ->leftJoin('bypass_template as bypass', 'qr.bypass_template_id', '=', 'bypass.id')
            // ->where('qr.id', $id)
            ->where([['qr.id', $id], ['qr.parent_user_id', Auth::user()->pr_user_id]])
            ->first();

        // return $queueRoom;die;
        if (!empty($queueRoom)) {

            $prUserId = Auth::user()->pr_user_id;
            $languages = Language::all();
            $queuetemplates = QueueRoomTemplate::where('parent_user_id', $prUserId)->get();
            $bypasstemplates = BypassTemplate::where('parent_user_id', $prUserId)->get();
            $designtemplates = QueuetbDesignTemplate::where('parent_user_id', $prUserId)->get();
            $emailtemplates = EmailSmsTemplate::where('parent_user_id', $prUserId)->where('status', 2)->get();
            $smstemplates = EmailSmsTemplate::where('parent_user_id', $prUserId)->where('status', 1)->get();

            $in_line_template = Logics::getStaticHTMLTemplate('queue', $id, $prUserId);
            $in_line_template = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $in_line_template);
            /** Get static html template | end */

            $serverTimezoneOffset = time();
            return view('queue-room.editqueue', compact('queueRoom', 'languages', 'serverTimezoneOffset', 'queuetemplates', 'bypasstemplates', 'designtemplates', 'emailtemplates', 'smstemplates', 'permission_for_this', 'in_line_template'));
        } else {

            return back()->with('warning', 'You\'re not authorize to access this queue information.');
        }
    }

    public function downloadPassCodes($bypass_tamp_id)
    {
        $query = 'SELECT pass_code FROM bypass_pass_codes WHERE bypass_tamp_id = ' . $bypass_tamp_id;
        $pass_code_data = DB::select($query);

        if ($pass_code_data) {
            return Excel::download(new PasscodesExport($pass_code_data), 'passcodes.xlsx');
        }
        return response()->json(['error' => 'No passcodes found for the given room_id'], 404);
    }

    public function queueRoomUpdate(Request $request, $id)
    {
        $tomezone = $request->input('timezone');
        $timezone1 = explode('|', $tomezone);

        $user = Auth::user();
        $prUserId = $user->pr_user_id;
        $userId = $user->id;
        $data = $request->input('advancedata');

        if (! is_null($data)) {
            if ($data['value'][0] == '') {
                $arr = [];
            } else {
                $arr = [];
                $i = 0;
                foreach ($data['condition_place'] as $key => $place) {
                    $row = [
                        'operator' => $i == 0 ? null : $data['operator'][$i - 1],
                        'condition_place' => $place,
                        'condition' => $data['condition'][$i],
                        'value' => $data['value'][$i],
                    ];
                    $i++;
                    $arr[] = $row;
                }
            }
        } else {
            $arr = [];
        }

        $queueRoom = QueueRoom::findOrFail($id);


    
        if ($request->input('template_id_data') == 0 && ! is_null($request->input('template_name'))) {
            $queueRoomTemplate = new QueueRoomTemplate();
            $queueRoomTemplate->template_name = $request->input('template_name');
            $queueRoomTemplate->input_url = $request->input('input_url');
            $queueRoomTemplate->protection_level = $request->input('protection') ?? '0';
            $queueRoomTemplate->is_advance_setting = $request->has('AdvanceSettingCheckBox') ? 1 : 0;
            $queueRoomTemplate->parent_user_id = $prUserId;
            if ($queueRoomTemplate->is_advance_setting == 1) {
                $queueRoomTemplate->advance_setting_rules = json_encode($arr);
            }
            $userId = Auth::id();
            $queueRoomTemplate->last_modified_by = $userId;
            $queueRoomTemplate->save();
            $savedtemplateId = $queueRoomTemplate->id;
        } elseif (is_null($request->input('template_name'))) {
            $savedtemplateId = $queueRoom->queue_room_template_id;
        } else {
            $savedtemplateId = $request->input('template_id_data');
        }

        if ($request->hasFile('queue_icon')) {
            $image = $request->file('queue_icon');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
        } else {
            $imageName = $queueRoom->queue_room_icon;
        }

        if ($request->input('QueueRoomDesign_id') == 0 && ! is_null($request->input('QueueRoomDesignTemplate_name'))) {
            $designtempdata = [
                'template_name' => $request->input('QueueRoomDesignTemplate_name'),
                'languages' => json_encode($request->input('queue_language')),
                'default_language' => $request->input('design-temp-setDefault'),
                'last_modified_by' => $userId,
                'parent_user_id' => $prUserId,
                'updated_at' => date('d-M-Y H:i:s', time()),
            ];

            $designtemplateId = DB::table('queuetb_design_template')->insertGetId($designtempdata);
        } elseif (!empty($request->input('QueueRoomDesign_id'))) {
            $getDesignData = DB::table('queuetb_design_template')->where('id', $request->input('QueueRoomDesign_id'))->first();
            // return $getDesignData->default_language;die;
            $language = $getDesignData->default_language;
            // $query = "UPDATE in_line_tamplates SET language = :language WHERE room_id = :id";
            // $a = DB::select($query, ['language' => $language, 'id' => $id]);

            $designtemplateId = $request->input('QueueRoomDesign_id');
        } elseif (is_null($request->input('QueueRoomDesignTemplate_name'))) {
            $designtemplateId = $queueRoom->queue_room_design_tempid;
        } else {
            $designtemplateId = $request->input('QueueRoomDesign_id');
        }

        if ($request->input('SetupBypass') == 1) {
            try {
                $request->validate([
                    'byPassSelectTemplateid' => 'required|numeric',
                ]);
                if ($request->input('byPassSelectTemplateid') == 0 && ! is_null($request->input('byPassSelectTemplate_name'))) {
                    $bypassTemplate = new BypassTemplate();
                    $bypassTemplate->template_name = $request->input('byPassSelectTemplate_name');
                    $bypassTemplate->bypass_url = $request->input('Bypassurl');
                    $bypassTemplate->parent_user_id = $prUserId;
                    $bypassTemplate->save();
                    $bypassTemplateId = $bypassTemplate->id;
                } elseif (is_null($request->input('byPassSelectTemplate_name'))) {
                    $bypassTemplateId = $queueRoom->bypass_template_id;
                } else {
                    $bypassTemplateId = $request->input('byPassSelectTemplateid');
                }
            } catch (\Illuminate\Validation\ValidationException $e) {
                return back()->withErrors($e->errors())->withInput();
            }
        } else {
            $bypassTemplateId = null;
        }

        if ($bypassTemplateId !== null) {
            if ($request->hasFile('filebyPass')) {
                $delete_passcodes = DB::table('bypass_pass_codes')->where('bypass_tamp_id', $bypassTemplateId)->delete();
                $bypass_tamp_id = $bypassTemplateId;
                Excel::import(new PasscodesImport($bypass_tamp_id), $request->file('filebyPass'));
            }
        }

        $SMSCreateTemplate = $request->input('SMSCreateTemplate');
        $SMSTemplate = $request->input('SMSTemplate');
        $EmailCreateTemplate = $request->input('EmailCreateTemplate');
        $EmailTemplate = $request->input('EmailTemplate');

        if ($SMSCreateTemplate == 0 && ! is_null($SMSTemplate)) {
            $smsTemplate = new EmailSmsTemplate();
            $smsTemplate->sms_template_name = $SMSTemplate;
            $smsTemplate->status = 1;
            $smsTemplate->parent_user_id = $prUserId;
            $smsTemplate->last_modified_by = $userId;
            $smsTemplate->html_content = $request->input('editorsmsContent');
            $smsTemplate->save();

            $smstempId = $smsTemplate->id;
        } elseif (is_null($SMSTemplate)) {
            $smstempId = $queueRoom->sms_notice_tempid;
        } else {
            $smstempId = $SMSCreateTemplate;
        }

        if ($EmailCreateTemplate == 0 && ! is_null($EmailTemplate)) {
            $emailTemplate = new EmailSmsTemplate();
            $emailTemplate->email_template_name = $EmailTemplate;
            $emailTemplate->status = 2;
            $emailTemplate->parent_user_id = $prUserId;
            $emailTemplate->last_modified_by = $userId;
            $emailTemplate->html_content = $request->input('editoremailContent');
            $emailTemplate->save();

            $emailtempId = $emailTemplate->id;
        } elseif (is_null($EmailTemplate)) {
            $emailtempId = $queueRoom->email_notice_tempid;
        } else {
            $emailtempId = $EmailCreateTemplate;
        }

        $startDateTime = $request->input('custom_start_date') . ' ' . $request->input('custom_start_time');

        $startEpoch = strtotime($startDateTime);

        $endDateTime = $request->input('custom_end_date') . ' ' . $request->input('custom_end_time');

        $endEpoch = strtotime($endDateTime);
        $started = $request->input('startTime');
        $ended = $request->input('endTime');

        $backendTimeZone = date_default_timezone_get();
        $inputTimeZoneOffset = $request->input('timezone');
        // echo $inputTimeZoneOffset;

        // $started = $request->input('startTime');
        if ($started == 1) {
            $start_date1 = $request->input('startDateValue');
            $start_time1 = $request->input('startTimeValue');
        } else {
            $start_date1 = $request->input('custom_start_date');
            $start_time1 = $request->input('custom_start_time');
        }

        $server_time_epoch = $this->convertToTimeZone($start_date1, $start_time1, $inputTimeZoneOffset);

        $ended = $request->input('endTime');
        if ($ended == 1) {
            $end_date1 = $request->input('endDateValue');
            $end_time1 = $request->input('endTimeValue');
        } else {
            $end_date1 = $request->input('custom_end_date');
            $end_time1 = $request->input('custom_end_time');

            if ($ended != 2) {
                $endDateTime = Carbon::parse($end_date1 . ' ' . $end_time1);
                $currentDateTime = Carbon::now();
                // dd($endDateTime->toDateTimeString(), $currentDateTime->toDateTimeString());
                if ($endDateTime->lessThan($currentDateTime)) {
                    $ended = 1;  // Set ended to 1 if the end date-time is in the past
                } else {
                    $ended = 0;  // Set ended to 0 if the end date-time is in the future
                }
            }
        }
        // return $ended;die;
        $end_server_time_epoch = $this->convertToTimeZone($end_date1, $end_time1, $inputTimeZoneOffset);

        if ($request->input('CustomURl') == 1) {
            if ($request->input('template_id') == 0) {
                $target_url = $request->input('input_url');
            } else {
                $target_url = DB::table('queue_room_template')
                    ->where('id', $request->input('template_id'))
                    ->value('input_url');
            }
        } else {
            $target_url = $request->input('custom_url');
        }

        $isUploaded = false;
        if (!empty($request->file('queueHtmlFile'))) {
            $isUploaded = true;
            $uploadqueueHtmlFile = Fileupload::uploadFileInS3($request->file('queueHtmlFile'), 'queue', $prUserId, $id);
        }

        if (!empty($request->file('preQueueHtmlFile'))) {
            $isUploaded = true;
            $uploadpreQueueHtmlFile = Fileupload::uploadFileInS3($request->file('preQueueHtmlFile'), 'prequeue', $prUserId, $id);
        }

        if (!empty($request->file('postQueueHtmlFile'))) {
            $isUploaded = true;
            $uploadpostQueueHtmlFile = Fileupload::uploadFileInS3($request->file('postQueueHtmlFile'), 'postqueue', $prUserId, $id);
        }

        if (!empty($request->file('priorityAccessPageHtmlFile'))) {
            $isUploaded = true;
            $uploadpriorityAccessPageHtmlFile = Fileupload::uploadFileInS3($request->file('priorityAccessPageHtmlFile'), 'priorityaccess', $prUserId, $id);
        }

        $queueRoom1 = QueueRoom::findOrFail($id);
        $queueRoom1->queue_room_name = !empty($request->input('roomname')) ? $request->input('roomname') : $queueRoom1->queue_room_name;
        $queueRoom1->queue_room_type = !empty($request->input('queuetype')) ? $request->input('queuetype') : $queueRoom1->queue_room_type;
        $queueRoom1->queue_room_icon = !empty($imageName) ? $imageName : $queueRoom1->queue_room_icon;
        $queueRoom1->queue_timezone = $timezone1[0];
        $queueRoom1->queue_timezone_name = $timezone1[1];
        $queueRoom1->start_date = !empty($start_date1) ? $start_date1 : $queueRoom1->start_date;
        $queueRoom1->start_time = !empty($start_time1) ? $start_time1 : $queueRoom1->start_time;
        $queueRoom1->is_started = (int) $started;
        // $queueRoom1->is_started = !empty($started) ? (int) $started : $queueRoom1->is_started;
        // $queueRoom1->is_ended = !empty($ended) ? (int) $ended : $queueRoom1->is_ended;
        $queueRoom1->is_ended = $ended !== null ? (int) $ended : $queueRoom1->is_ended;
        $queueRoom1->end_date = !empty($end_date1) ? $end_date1 : $queueRoom1->end_date;
        $queueRoom1->end_time = !empty($end_time1) ? $end_time1 : $queueRoom1->end_time;
        $queueRoom1->timezone_based_start_datetime = $request->input('convertedstartDateTime');
        $queueRoom1->timezone_based_start_datetime_epoch = $startEpoch;
        $queueRoom1->timezone_based_end_datetime = $request->input('convertedEndDateTime');
        $queueRoom1->timezone_based_end_datetime_epoch = $endEpoch;

        $queueRoom1->start_time_epoch = $server_time_epoch;
        $queueRoom1->end_time_epoch = $end_server_time_epoch;

        $queueRoom1->sms_notice_tempid = $smstempId;
        $queueRoom1->email_notice_tempid = $emailtempId;
        $queueRoom1->queue_room_template_id = $savedtemplateId;
        $queueRoom1->is_customurl = ($request->input('CustomURl')) ? $request->input('CustomURl') : $queueRoom1->is_customurl;
        $queueRoom1->target_url = !empty($target_url) ? $target_url : $queueRoom1->target_url;
        $queueRoom1->max_traffic_visitor = !empty($request->input('max_traffic')) ? $request->input('max_traffic') : $queueRoom1->max_traffic_visitor;
        $queueRoom1->enable_bypass = $request->input('SetupBypass') ?? 0;
        $queueRoom1->bypass_template_id = $bypassTemplateId ?? null;
        $queueRoom1->is_prequeue = $request->input('preQueueSetup');
        // $queueRoom1->is_prequeue = !empty($request->input('preQueueSetup')) ? $request->input('preQueueSetup') : $queueRoom1->is_prequeue;
        // $queueRoom1->is_prequeue = $request->input('startTime');
        $queueRoom1->prequeue_starttime = !empty($request->input('BeforeTimeforPrequeue')) ? $request->input('BeforeTimeforPrequeue') : $queueRoom1->prequeue_starttime;
        $queueRoom1->queue_room_design_tempid = $designtemplateId;
        $queueRoom1->last_modified_by = $user->id;
        $queueRoom1->parent_user_id = $prUserId;
        $queueRoom1->is_draft = 0;
        $queueRoom1->is_uploaded = $isUploaded;
        $queueRoom1->queue_html_page_url = !empty($uploadqueueHtmlFile) ? $uploadqueueHtmlFile : $queueRoom->queue_html_page_url;
        $queueRoom1->postqueue_html_page_url = !empty($uploadpostQueueHtmlFile) ? $uploadpostQueueHtmlFile : $queueRoom->postqueue_html_page_url;
        $queueRoom1->priorityqueue_html_page_url = !empty($uploadpriorityAccessPageHtmlFile) ? $uploadpriorityAccessPageHtmlFile : $queueRoom->priorityqueue_html_page_url;
        $queueRoom1->prequeue_html_page_url = !empty($uploadpreQueueHtmlFile) ? $uploadpreQueueHtmlFile : $queueRoom->prequeue_html_page_url;
        $queueRoom1->session_type = $request->input('session_type');
        $queueRoom1->current_space = !empty($request->input('max_traffic')) ? $request->input('max_traffic') : $queueRoom1->max_traffic_visitor;
        $queueRoom1->time_input = !empty($request->input('time_input')) ? $request->input('time_input') : $queueRoom1->time_input;
        if ($request->input('saveasdraft')) {
            $queueRoom1->is_draft = 1;
        } else {
            $queueRoom1->is_draft = 0; 
        }
        
        $queueRoom1->save();
  
        $queueRoom1->save();

        return redirect()->route('queue-room-view');
    }

    public function queueRoomDelete($id)
    {
        $queueRoom = QueueRoom::findOrFail($id);
        $queueRoom->delete();
        return redirect()->back()->with('success', 'QueueRoom deleted successfully');
    }

    public function getBypassData($id)
    {
        $bypassTemplate = BypassTemplate::find($id);
        echo $bypassTemplate;
    }

    public function getdesigntempData($id)
    {
        $QueuetbDesignTemplate = QueuetbDesignTemplate::find($id);

        $languages = json_decode(json_decode($QueuetbDesignTemplate->languages));
        // var_dump($languages);
        $languageData = DB::table('languages')
            ->whereIn('code', $languages)
            ->get();
        $QueuetbDesignTemplate->languageData = $languageData;

        $in_line_template = DB::table('in_line_tamplates as ilt')
            ->select('*')
            ->where('template_id', $id)
            ->where('language', $QueuetbDesignTemplate->default_language)
            ->get();
        if (! empty($in_line_template)) {
            foreach ($in_line_template as &$singleTemplate) {
                $singleTemplate->htm_data = html_entity_decode($singleTemplate->htm_data);
            }
        }

        $QueuetbDesignTemplate->in_line_template = $in_line_template;
        echo $QueuetbDesignTemplate;
    }

    public function getEmailData($id)
    {
        $emailTemplate = EmailSmsTemplate::find($id);
        echo $emailTemplate;
    }

    public function getsmsData($id)
    {
        $smsTemplate = EmailSmsTemplate::find($id);
        echo $smsTemplate;
    }

    public function getTemplateData($id)
    {
        $queueTemplate = QueueRoomTemplate::find($id);
        echo $queueTemplate;
    }

    /** This function using for getting the total visitor count in the rooms */
    public function getQueueTotalvisitors(Request $request)
    {
        $user = Auth::user();
        $prUserId = $user->pr_user_id;

        $visitorForNow = QueueRoom::where('parent_user_id', $prUserId)->sum('max_traffic_visitor');

        $getSubscriptionId = DB::table('queuetb_users')->where('id', $prUserId)->value('subscription_plan_id');

        $getSubscriptionInfo = DB::table('queuetb_subscription_plan')->select('maximum_traffic', 'setup_sms', 'setup_email')->where('id', $getSubscriptionId)->first();

        if ($request->type == 1) {
            $calculateVisitorCount = ($getSubscriptionInfo->maximum_traffic - $visitorForNow);
            // return $getSubscriptionInfo->maximum_traffic;die;
            if ($calculateVisitorCount >= $request->count) {
                return response()->json(['status' => true, 'data' => $request->count, 'calculateVisitorCount' => $calculateVisitorCount]);
            } else {
                return response()->json(['status' => false, 'data' => $request->count, 'calculateVisitorCount' => $calculateVisitorCount]);
            }
        } else {
            $value = [
                'setup_sms' => $getSubscriptionInfo->setup_sms,
                'setup_email' => $getSubscriptionInfo->setup_email,
            ];

            return response()->json(['status' => true, 'message' => 'success', 'data' => $value]);
        }
    }
    public function endRoom($id)
{
    $room = QueueRoom::find($id);
    if (!$room) {
        return response()->json(['success' => false, 'message' => 'Room not found']);
    }

    if (!$room->is_started) {
        return response()->json(['success' => false, 'message' => 'Room has not started']);
    }

    // Always update the end status, even if there is "No end Time"
    if ($room->is_ended) {
        $room->is_ended = 1;
        $room->end_date = date('Y-m-d');
        $room->end_time = date('H:i:s');
        $room->save();

        return response()->json(['success' => true]);
    }

    $room->is_ended = 1;
    $room->end_date = date('Y-m-d');
    $room->end_time = date('H:i:s');
    $room->save();

    return response()->json(['success' => true]);
}

}
