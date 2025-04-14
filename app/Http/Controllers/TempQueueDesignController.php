<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\PermissionAccess;
use App\Models\QueuetbDesignTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TempQueueDesignController extends Controller
{
    public function queueDesignEdit(Request $request, $id, $roomId)
    {
        $user = Auth::user();
        $userRole = $user->role;
        $userId = $user->id;

        $queueRoomTemplates = QueuetbDesignTemplate::where('id', $id)->get();

        if ($queueRoomTemplates->isEmpty()) {
            return redirect()->back()->with('error', 'Queue Room Template not found.');
        }

        $queueRoomTemplate = $queueRoomTemplates->toArray(); // Convert to array

        // Ensure index 0 exists
        if (!isset($queueRoomTemplate[0])) {
            return redirect()->back()->with('error', 'Invalid template data.');
        }

        $langUser = json_decode($queueRoomTemplate[0]['languages'] ?? '[]', true);

        $languages = Language::all();
        $matchedLanguages = [];

        $decodedLangUser = is_array($langUser) ? $langUser : [];


        foreach ($decodedLangUser as $userCode) {
            foreach ($languages as $lang) {
                if ($lang->code === $userCode) {
                    $matchedLanguages[] = $lang;
                    break;
                }
            }
        }


        return view('queue-room.temp_queueDesignEdit', compact('languages', 'queueRoomTemplate', 'matchedLanguages', 'roomId'));
    }




    // public function queueDesignEdit(Request $request, $id)
    // {
    //     $user = Auth::user();
    //     $userId = $user->id;
    //     $userRole = $user->role;

    //     $queueRoomTemplates = QueuetbDesignTemplate::find($id);
    //     if (!$queueRoomTemplates) {
    //         return back()->withErrors('Template not found');
    //     }

    //     $queueRoomTemplate = $queueRoomTemplates->toArray();
    //     $filteredStdClass = [];

    //     if ($userRole != 1) {
    //         $permissions = PermissionAccess::where('user_id', $userId)->value('queue_room_access');
    //         $originalArray = json_decode($permissions, true);

    //         $filteredArray = array_filter($originalArray, function($value) use ($id) {
    //             foreach ($value as $obj) {
    //                 if ($obj['module_id'] === 3) {
    //                     return true;
    //                 }
    //             }
    //             return false;
    //         });

    //         foreach ($filteredArray as $key => $value) {
    //             if ($key === $id) {
    //                 $filteredStdClass[$key] = $value;
    //                 break;
    //             }
    //         }
    //     }

    //     $langUser = json_decode($queueRoomTemplate['languages'], true);
    //     $languages = Language::all()->toArray();
    //     $matchedLanguages = array_filter($languages, function($lang) use ($langUser) {
    //         return in_array($lang['code'], $langUser);
    //     });

    //     return view('queue-room.temp_queueDesignEdit', compact('languages', 'queueRoomTemplate', 'matchedLanguages'));
    // }


    // public function queueDesigeupdate(Request $request, $id)
    // {
    //     $user = Auth::user();
    //     $userId = $user->id;
    //     $prUserId = $user->pr_user_id;

    //     $designtempdata = [
    //         'template_name' => $request->input('QueueRoomDesignTemplate_name'),
    //         'languages' => json_encode($request->input('queue_language')),
    //         'default_language' => $request->input('setDefault'),
    //         'last_modified_by' => $userId,
    //         'parent_user_id' => $prUserId,
    //         'updated_at' => date('Y-m-d H:i:s'),
    //     ];

    //     // Update the record in the database
    //     QueuetbDesignTemplate::where('id', $id)->update($designtempdata);

    //     // Optionally, you can return a response or redirect the user
    //     return redirect()->route('viewQueueRoomDesign')->with('success', 'Template updated successfully');
    // }

    public function queueDesigeupdate(Request $request, $id)
    {
        $user = Auth::user();
        $userId = $user->id;
        $prUserId = $user->pr_user_id;

        $queueLanguages = $request->input('queue_language', []);

        // Prepare the data for update
        $designTemplateData = [
            'template_name' => $request->input('QueueRoomDesignTemplate_name'),
            'languages' => json_encode($queueLanguages),
            'default_language' => $request->input('setDefault'),
            'last_modified_by' => $userId,
            'parent_user_id' => $prUserId,
            'updated_at' => now(),
        ];

        // dd($designTemplateData);

        // Update the record in the database
        QueuetbDesignTemplate::where('id', $id)->update($designTemplateData);

        // Optionally, you can return a response or redirect the user
        return redirect()->route('viewQueueRoomDesign')->with('success', 'Template updated successfully');
    }


    public function queueDesigedelete($id, $queue_id)
    {
        $queueDelete = QueuetbDesignTemplate::destroy($id);

        DB::table('queuetb_queue_room')
            ->where('id', $queue_id)
            ->update(['queue_room_design_tempid' => null]);
        return redirect()->route('viewQueueRoomDesign')->with('success', 'Template deleted successfully');
    }
}
