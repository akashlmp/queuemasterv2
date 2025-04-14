<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Storage;
use Http;
use Aws\S3\S3Client;
use Illuminate\Support\Str;
use App\Models\QueueRoom;
use App\Models\QueuetbDesignTemplate;
use App\Models\DeveloperScript;
use App\Helpers\Fileupload;
use App\Helpers\Logics;

class CanvasController extends Controller
{

    // public function edittemplate($template_id = false, $lang_id = 'en', $room_id = false)
    // {
    //     $query = 'SELECT qdt.languages , qdt.default_language
    // 	FROM queuetb_design_template as qdt
    // 	WHERE qdt.id = ' . $template_id;

    //     $queuetb_design_template = DB::select($query);

    //     if (! empty($queuetb_design_template[0]->languages) && $queuetb_design_template[0]->languages !== 'null') {
    //         $languages = json_decode($queuetb_design_template[0]->languages);
    //     } else {
    //         $languages = ['en'];
    //     }
    //     if (is_null($queuetb_design_template[0]->default_language)) {
    //         $default_language = 'en';
    //     } else {
    //         $default_language = $queuetb_design_template[0]->default_language;
    //     }

    //     $query = 'SELECT id, htm_data FROM in_line_tamplates Where (template_id  = ' . $template_id . ") and (language = '{$lang_id}') and (type = 'queue_page')";
    //     $lang_html_data = DB::select($query);

    //     if (isset($lang_html_data[0]->htm_data)) {
    //         $queue_page_tab = html_entity_decode($lang_html_data[0]->htm_data);
    //     } else {
    //         $queue_page_tab = $this->getDummyData('queue_page_tab');
    //     }

    //     $query = 'SELECT id, htm_data FROM in_line_tamplates Where (template_id  = ' . $template_id . ") and (language = '{$lang_id}') and (type = 'prequeue_page')";
    //     $lang_html_data = DB::select($query);

    //     if (isset($lang_html_data[0]->htm_data)) {
    //         $pre_queue_page_tab = html_entity_decode($lang_html_data[0]->htm_data);
    //     } else {
    //         $pre_queue_page_tab = $this->getDummyData('pre_queue_page_tab');
    //     }

    //     $query = 'SELECT id, htm_data FROM in_line_tamplates Where (template_id  = ' . $template_id . ") and (language = '{$lang_id}') and (type = 'postqueue_page')";
    //     $lang_html_data = DB::select($query);

    //     if (isset($lang_html_data[0]->htm_data)) {
    //         $postqueue_page_tab = html_entity_decode($lang_html_data[0]->htm_data);
    //     } else {
    //         $postqueue_page_tab = $this->getDummyData('postqueue_page_tab');
    //     }

    //     $query = 'SELECT id, htm_data FROM in_line_tamplates Where (template_id  = ' . $template_id . ") and (language = '{$lang_id}') and (type = 'priority_access_page')";
    //     $lang_html_data = DB::select($query);
    //     if (isset($lang_html_data[0]->htm_data)) {
    //         $priority_access_page_tab = html_entity_decode($lang_html_data[0]->htm_data);
    //     } else {
    //         $priority_access_page_tab = $this->getDummyData('priority_access_page_tab');
    //     }

    //     if (isset($lang_html_data[0]->htm_data)) {
    //         $priority_access_page_tab = html_entity_decode($lang_html_data[0]->htm_data);
    //     } else {
    //         $priority_access_page_tab = $this->getDummyData('priority_access_page_tab');
    //     }
    //     date_default_timezone_set('Asia/Hong_Kong');
    //     // Get the current date and time
    //     $currentDateTime = date('d/m/Y H:i').' HKT';

    //     // Define the text to be replaced
    //     $oldDateTime = '07/12/2024 12:04 HKT';

    //     // Replace the old date and time with the current date and time
    //     $priority_access_page_tab = str_replace($oldDateTime, $currentDateTime, $priority_access_page_tab);
    //     $pre_queue_page_tab = str_replace($oldDateTime, $currentDateTime, $pre_queue_page_tab);
    //     $postqueue_page_tab = str_replace($oldDateTime, $currentDateTime, $postqueue_page_tab);
    //     $queue_page_tab = str_replace($oldDateTime, $currentDateTime, $queue_page_tab);
    //     $data = [
    //         'room_id' => $room_id ?? null,
    //         'template_id' => $template_id ?? null,
    //         'queue_page_tab' => $queue_page_tab,
    //         'pre_queue_page_tab' => $pre_queue_page_tab,
    //         'postqueue_page_tab' => $postqueue_page_tab,
    //         'priority_access_page_tab' => $priority_access_page_tab,
    //         'languages' => $languages,
    //         'default_language' => $default_language,
    //         'lang_id' => $lang_id,
    //     ];
    //     return view('canvas', $data);
    // }

    public function edittemplate($template_id = null, $lang_id = 'en', $room_id = null)
    {
        // $queuetb_design_template = QueuetbDesignTemplate::select('languages', 'default_language', 'parent_user_id')->where('id', $template_id)->first();
        if (!empty($room_id)) {
            $query = 'SELECT qr.id, qr.queue_room_design_tempid, qr.is_uploaded, qr.parent_user_id, qr.queue_html_page_url, qr.postqueue_html_page_url, qr.priorityqueue_html_page_url, qr.prequeue_html_page_url, qrt.is_advance_setting, qrt.advance_setting_rules, qrt.input_url, qr.queue_room_template_id, qdt.languages, qdt.default_language
	                FROM queuetb_queue_room as qr
	                INNER JOIN queuetb_design_template as qdt ON qr.queue_room_design_tempid = qdt.id
	                LEFT JOIN queue_room_template as qrt ON qr.queue_room_template_id = qrt.id
	                WHERE qr.id = ' . $room_id;

            $queuetb_design_template = DB::selectOne($query);
        } else {
            $queuetb_design_template = QueuetbDesignTemplate::select('languages', 'default_language', 'parent_user_id')->where('id', $template_id)->first();
        }

        if (!empty($queuetb_design_template->languages) && $queuetb_design_template->languages !== 'null') {
            $languages = json_decode($queuetb_design_template->languages, true); // true to get an associative array
            if (!is_array($languages)) {
                $languages = ['en']; // Default to 'en' if decoding fails
            }
        } else {
            $languages = ['en'];
        }

        if (is_null($queuetb_design_template->default_language)) {
            $default_language = 'en';
        } else {
            $default_language = $queuetb_design_template->default_language;
        }

        // $queue_page_tab = Logics::getDummyCode('queue_page_tab');
        // $pre_queue_page_tab = Logics::getDummyCode('pre_queue_page_tab');
        // $postqueue_page_tab = Logics::getDummyCode('postqueue_page_tab');
        // $priority_access_page_tab = Logics::getDummyCode('priority_access_page_tab');

        $getQueueTemplate = Logics::getStaticHTMLTemplate('queue', $room_id, $queuetb_design_template->parent_user_id, $template_id);
        if (!empty($getQueueTemplate)) {
            $queue_page_tab = $getQueueTemplate;
        } else {
            // $queue_page_tab = $this->getDummyData('queue_page_tab');
            $queue_page_tab = Logics::getDummyCode('queue_page_tab');
        }

        $getPrequeueTemplate = Logics::getStaticHTMLTemplate('prequeue', $room_id, $queuetb_design_template->parent_user_id, $template_id);
        if (!empty($getPrequeueTemplate)) {
            $pre_queue_page_tab = $getPrequeueTemplate;
        } else {
            // $pre_queue_page_tab = $this->getDummyData('pre_queue_page_tab');
            $pre_queue_page_tab = Logics::getDummyCode('pre_queue_page_tab');
        }

        $getPostqueueTemplate = Logics::getStaticHTMLTemplate('postqueue', $room_id, $queuetb_design_template->parent_user_id, $template_id);
        if (!empty($getPostqueueTemplate)) {
            $postqueue_page_tab = $getPostqueueTemplate;
        } else {
            // $postqueue_page_tab = $this->getDummyData('postqueue_page_tab');
            $postqueue_page_tab = Logics::getDummyCode('postqueue_page_tab');
        }

        $getPriorityaccessTemplate = Logics::getStaticHTMLTemplate('priorityaccess', $room_id, $queuetb_design_template->parent_user_id, $template_id);
        if (!empty($getPriorityaccessTemplate)) {
            $priority_access_page_tab = $getPriorityaccessTemplate;
        } else {
            // $priority_access_page_tab = $this->getDummyData('priority_access_page_tab');
            $priority_access_page_tab = Logics::getDummyCode('priority_access_page_tab');
        }

        /** creating a developer script | start */
        // Retrieve developer script
        $developerScript = DeveloperScript::firstOrNew(['id' => 1]);
        $script = $developerScript->developer_script;
        $scriptDataForJs = '';
        if (!empty($room_id)) {
            if (!empty($queuetb_design_template->is_advance_setting) && $queuetb_design_template->is_advance_setting == 1 && !empty($queuetb_design_template->advance_setting_rules)) {
                $advance_setting_rules = json_decode($queuetb_design_template->advance_setting_rules, true);
                if (!empty($advance_setting_rules)) {
                    $loop = 1;
                    foreach ($advance_setting_rules as $advance_setting_rule) {
                        if (!empty($advance_setting_rule['operator'])) {
                            $scriptDataForJs .= ' data-condition-op-' . $loop . '="' . $advance_setting_rule['operator'] . '" ';
                        }
                        if (!empty($advance_setting_rule['condition_place'])) {
                            $scriptDataForJs .= ' data-condition-place-' . $loop . '="' . $advance_setting_rule['condition_place'] . '" ';
                        }
                        if (!empty($advance_setting_rule['condition'])) {
                            $scriptDataForJs .= ' data-condition-' . $loop . '="' . $advance_setting_rule['condition'] . '" ';
                        }
                        if (!empty($advance_setting_rule['value'])) {
                            $scriptDataForJs .= ' data-condition-value-' . $loop . '="' . $advance_setting_rule['value'] . '" ';
                        }
                        $loop++;
                    }
                }
            }
        }

        /** creating a developer script | end */
        date_default_timezone_set('Asia/Hong_Kong');
        // Get the current date and time
        $currentDateTime = date('d/m/Y H:i') . ' HKT';

        // Define the text to be replaced
        $oldDateTime = '07/12/2024 12:04 HKT';

        $priority_access_page_tab = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $priority_access_page_tab);
        $pre_queue_page_tab = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $pre_queue_page_tab);
        $postqueue_page_tab = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $postqueue_page_tab);
        $queue_page_tab = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $queue_page_tab);
        // Replace the old date and time with the current date and time
        $priority_access_page_tab = str_replace($oldDateTime, $currentDateTime, $priority_access_page_tab);
        $pre_queue_page_tab = str_replace($oldDateTime, $currentDateTime, $pre_queue_page_tab);
        $postqueue_page_tab = str_replace($oldDateTime, $currentDateTime, $postqueue_page_tab);
        $queue_page_tab = str_replace($oldDateTime, $currentDateTime, $queue_page_tab);

        $data = [
            'room_id' => $room_id ?? null,
            'template_id' => $template_id ?? null,
            'queue_page_tab' => $queue_page_tab,
            'pre_queue_page_tab' => $pre_queue_page_tab,
            'postqueue_page_tab' => $postqueue_page_tab,
            'priority_access_page_tab' => $priority_access_page_tab,
            'languages' => $languages,
            'default_language' => $default_language,
            'lang_id' => $lang_id,
            'scriptDataForJs' => $scriptDataForJs,
            'data_intercept_domain' => $queuetb_design_template->input_url,
            'data_intercept' => base64_encode($queuetb_design_template->id),
            // 'data_intercept' => $queuetb_design_template->id,
            'data_c' => $queuetb_design_template->parent_user_id,
            'script' => $script,
        ];

        // $data = [
        //     'room_id' => $room_id ?? null,
        //     'template_id' => $template_id ?? null,
        //     'queue_page_tab' => $queue_page_tab,
        //     'pre_queue_page_tab' => $pre_queue_page_tab,
        //     'postqueue_page_tab' => $postqueue_page_tab,
        //     'priority_access_page_tab' => $priority_access_page_tab,
        //     'languages' => $languages,
        //     'default_language' => $default_language,
        //     'lang_id' => $lang_id,
        // ];

        return view('canvas', $data);
    }

    public function create()
    {
        if (Session::has('queue_draft')) {
            $templateInfo = [
                'QueueRoomDesignTemplate_name' => Session::get('queue_draft')['QueueRoomDesignTemplate_name'],
                'design_temp_setDefault' => Session::get('queue_draft')['design-temp-setDefault'],
                'queue_language' => Session::get('queue_draft')['queue_language'],
            ];
            //$templateInfo = Session::get('selected_templates');
            //Session::set('selected_language_templates', $event_data_display);
            $userId = Auth::id();
            $user = Auth::user();
            $prUserId = $user->pr_user_id;
            $designtempdata = [
                'template_name' => $templateInfo['QueueRoomDesignTemplate_name'],
                'languages' => json_encode($templateInfo['queue_language']),
                'default_language' => $templateInfo['design_temp_setDefault'],
                'last_modified_by' => $userId,
                'parent_user_id' => $prUserId,
                'updated_at' => date('d-M-Y H:i:s', time()),
            ];
            $designtemplateId = DB::table('queuetb_design_template')->insertGetId($designtempdata);
            // return redirect()->route('edittemplate.inline.room', ['id' => $designtemplateId]);
            return redirect()->route('edittemplate.inline.room', ['id' => $designtemplateId, 'lang_id' => null, 'roomId' => null]);
        }

        return redirect()->route('createqueue')->withErrors(['msg' => 'Design template info not found']);
    }

    // public function index($room_id = false, $lang_id = 'en')
    // {
    //     $query = 'SELECT qr.id,qr.queue_room_design_tempid,qdt.languages , qdt.default_language
    // 	FROM queuetb_queue_room as qr
    // 	INNER JOIN queuetb_design_template as qdt ON qr.queue_room_design_tempid = qdt.id
    // 	WHERE qr.id = ' . $room_id;

    //     $queue_room_data = DB::select($query);

    //     if (isset($queue_room_data[0]->languages)) {
    //         $languages = json_decode($queue_room_data[0]->languages);
    //     } else {
    //         $languages = ['en'];
    //     }
    //     if (is_null($queue_room_data[0]->default_language)) {
    //         $default_language = 'en';
    //     } else {
    //         $default_language = $queue_room_data[0]->default_language;
    //     }

    //     $query = 'SELECT id, htm_data FROM in_line_tamplates Where (room_id  = ' . $room_id . ") and (language = '{$lang_id}') and (type = 'queue_page')";
    //     $lang_html_data = DB::select($query);

    //     if (isset($lang_html_data[0]->htm_data)) {
    //         $queue_page_tab = html_entity_decode($lang_html_data[0]->htm_data);
    //     } else {
    //         $queue_page_tab = $this->getDummyData('queue_page_tab');
    //     }

    //     $query = 'SELECT id, htm_data FROM in_line_tamplates Where (room_id  = ' . $room_id . ") and (language = '{$lang_id}') and (type = 'prequeue_page')";
    //     $lang_html_data = DB::select($query);

    //     if (isset($lang_html_data[0]->htm_data)) {
    //         $pre_queue_page_tab = html_entity_decode($lang_html_data[0]->htm_data);
    //     } else {
    //         $pre_queue_page_tab = $this->getDummyData('pre_queue_page_tab');
    //     }

    //     $query = 'SELECT id, htm_data FROM in_line_tamplates Where (room_id  = ' . $room_id . ") and (language = '{$lang_id}') and (type = 'postqueue_page')";
    //     $lang_html_data = DB::select($query);

    //     if (isset($lang_html_data[0]->htm_data)) {
    //         $postqueue_page_tab = html_entity_decode($lang_html_data[0]->htm_data);
    //     } else {
    //         $postqueue_page_tab = $this->getDummyData('postqueue_page_tab');
    //     }

    //     $query = 'SELECT id, htm_data FROM in_line_tamplates Where (room_id  = ' . $room_id . ") and (language = '{$lang_id}') and (type = 'priority_access_page')";
    //     $lang_html_data = DB::select($query);
    //     if (isset($lang_html_data[0]->htm_data)) {
    //         $priority_access_page_tab = html_entity_decode($lang_html_data[0]->htm_data);
    //     } else {
    //         $priority_access_page_tab = $this->getDummyData('priority_access_page_tab');
    //     }
    //     date_default_timezone_set('Asia/Hong_Kong');
    //     // Get the current date and time
    //     $currentDateTime = date('d/m/Y H:i').' HKT';

    //     // Define the text to be replaced
    //     $oldDateTime = '07/12/2024 12:04 HKT';

    //     // Replace the old date and time with the current date and time
    //     $priority_access_page_tab = str_replace($oldDateTime, $currentDateTime, $priority_access_page_tab);
    //     $pre_queue_page_tab = str_replace($oldDateTime, $currentDateTime, $pre_queue_page_tab);
    //     $postqueue_page_tab = str_replace($oldDateTime, $currentDateTime, $postqueue_page_tab);
    //     $queue_page_tab = str_replace($oldDateTime, $currentDateTime, $queue_page_tab);
    //     $data = [
    //         'room_id' => $room_id,
    //         'template_id' => $template_id ?? null,
    //         'queue_page_tab' => $queue_page_tab,
    //         'pre_queue_page_tab' => $pre_queue_page_tab,
    //         'postqueue_page_tab' => $postqueue_page_tab,
    //         'priority_access_page_tab' => $priority_access_page_tab,
    //         'languages' => $languages,
    //         'default_language' => $default_language,
    //         'lang_id' => $lang_id,
    //     ];

    //     return view('canvas', $data);
    // }

    // public function index($room_id = false, $lang_id = 'en')
    // {
    //     $query = 'SELECT qr.id, qr.queue_room_design_tempid, qr.is_uploaded, qr.parent_user_id, qr.queue_html_page_url, qr.postqueue_html_page_url, qr.priorityqueue_html_page_url, qr.prequeue_html_page_url, qrt.is_advance_setting, qrt.advance_setting_rules, qrt.input_url, qr.queue_room_template_id, qdt.languages, qdt.default_language
    // 	FROM queuetb_queue_room as qr
    // 	INNER JOIN queuetb_design_template as qdt ON qr.queue_room_design_tempid = qdt.id
    // 	LEFT JOIN queue_room_template as qrt ON qr.queue_room_template_id = qrt.id
    // 	WHERE qr.id = ' . $room_id;

    //     $queue_room_data = DB::selectOne($query);

    //     if ($queue_room_data->is_uploaded != 1) {
    //         if (isset($queue_room_data->languages)) {
    //             $languages = json_decode($queue_room_data->languages);
    //         } else {
    //             $languages = ['en'];
    //         }
    //         if (is_null($queue_room_data->default_language)) {
    //             $default_language = 'en';
    //         } else {
    //             $default_language = $queue_room_data->default_language;
    //         }

    //         if (!empty($queue_room_data->queue_html_page_url) && !empty($queue_room_data->postqueue_html_page_url) && !empty($queue_room_data->priorityqueue_html_page_url) && !empty($queue_room_data->prequeue_html_page_url)) {
    //             $getQueueTemplate = Logics::getStaticHTMLTemplate('queue', $room_id, $queue_room_data->parent_user_id);
    //             if (!empty($getQueueTemplate)) {
    //                 $queue_page_tab = $getQueueTemplate;
    //             } else {
    //                 // $queue_page_tab = $this->getDummyData('queue_page_tab');
    //                 $queue_page_tab = Logics::getDummyCode('queue_page_tab');
    //             }

    //             $getPrequeueTemplate = Logics::getStaticHTMLTemplate('prequeue', $room_id, $queue_room_data->parent_user_id);
    //             if (!empty($getPrequeueTemplate)) {
    //                 $pre_queue_page_tab = $getPrequeueTemplate;
    //             } else {
    //                 // $pre_queue_page_tab = $this->getDummyData('pre_queue_page_tab');
    //                 $pre_queue_page_tab = Logics::getDummyCode('pre_queue_page_tab');
    //             }

    //             $getPostqueueTemplate = Logics::getStaticHTMLTemplate('postqueue', $room_id, $queue_room_data->parent_user_id);
    //             if (!empty($getPostqueueTemplate)) {
    //                 $postqueue_page_tab = $getPostqueueTemplate;
    //             } else {
    //                 // $postqueue_page_tab = $this->getDummyData('postqueue_page_tab');
    //                 $postqueue_page_tab = Logics::getDummyCode('postqueue_page_tab');
    //             }

    //             $getPriorityaccessTemplate = Logics::getStaticHTMLTemplate('priorityaccess', $room_id, $queue_room_data->parent_user_id);
    //             if (!empty($getPriorityaccessTemplate)) {
    //                 $priority_access_page_tab = $getPriorityaccessTemplate;
    //             } else {
    //                 // $priority_access_page_tab = $this->getDummyData('priority_access_page_tab');
    //                 $priority_access_page_tab = Logics::getDummyCode('priority_access_page_tab');
    //             }
    //         } else {
    //             $getQueueTemplate = Logics::getStaticHTMLTemplate('queue', null, $queue_room_data->parent_user_id, $queue_room_data->queue_room_design_tempid);
    //             if (!empty($getQueueTemplate)) {
    //                 $queue_page_tab = $getQueueTemplate;
    //             } else {
    //                 // $queue_page_tab = $this->getDummyData('queue_page_tab');
    //                 $queue_page_tab = Logics::getDummyCode('queue_page_tab');
    //             }

    //             $getPrequeueTemplate = Logics::getStaticHTMLTemplate('prequeue', null, $queue_room_data->parent_user_id, $queue_room_data->queue_room_design_tempid);
    //             if (!empty($getPrequeueTemplate)) {
    //                 $pre_queue_page_tab = $getPrequeueTemplate;
    //             } else {
    //                 // $pre_queue_page_tab = $this->getDummyData('pre_queue_page_tab');
    //                 $pre_queue_page_tab = Logics::getDummyCode('pre_queue_page_tab');
    //             }

    //             $getPostqueueTemplate = Logics::getStaticHTMLTemplate('postqueue', null, $queue_room_data->parent_user_id, $queue_room_data->queue_room_design_tempid);
    //             if (!empty($getPostqueueTemplate)) {
    //                 $postqueue_page_tab = $getPostqueueTemplate;
    //             } else {
    //                 // $postqueue_page_tab = $this->getDummyData('postqueue_page_tab');
    //                 $postqueue_page_tab = Logics::getDummyCode('postqueue_page_tab');
    //             }

    //             $getPriorityaccessTemplate = Logics::getStaticHTMLTemplate('priorityaccess', null, $queue_room_data->parent_user_id, $queue_room_data->queue_room_design_tempid);
    //             if (!empty($getPriorityaccessTemplate)) {
    //                 $priority_access_page_tab = $getPriorityaccessTemplate;
    //             } else {
    //                 // $priority_access_page_tab = $this->getDummyData('priority_access_page_tab');
    //                 $priority_access_page_tab = Logics::getDummyCode('priority_access_page_tab');
    //             }
    //         }

    //         /** creating a developer script | start */
    //         // Retrieve developer script
    //         $developerScript = DeveloperScript::firstOrNew(['id' => 1]);
    //         $script = $developerScript->developer_script;
    //         $scriptDataForJs = '';
    //         if (!empty($queue_room_data->is_advance_setting) && $queue_room_data->is_advance_setting == 1 && !empty($queue_room_data->advance_setting_rules)) {
    //             $advance_setting_rules = json_decode($queue_room_data->advance_setting_rules, true);
    //             if (!empty($advance_setting_rules)) {
    //                 $loop = 1;
    //                 foreach ($advance_setting_rules as $advance_setting_rule) {
    //                     if (!empty($advance_setting_rule['operator'])) {
    //                         $scriptDataForJs .= ' data-condition-op-' . $loop . '="' . $advance_setting_rule['operator'] . '" ';
    //                     }
    //                     if (!empty($advance_setting_rule['condition_place'])) {
    //                         $scriptDataForJs .= ' data-condition-place-' . $loop . '="' . $advance_setting_rule['condition_place'] . '" ';
    //                     }
    //                     if (!empty($advance_setting_rule['condition'])) {
    //                         $scriptDataForJs .= ' data-condition-' . $loop . '="' . $advance_setting_rule['condition'] . '" ';
    //                     }
    //                     if (!empty($advance_setting_rule['value'])) {
    //                         $scriptDataForJs .= ' data-condition-value-' . $loop . '="' . $advance_setting_rule['value'] . '" ';
    //                     }
    //                     $loop++;
    //                 }
    //             }
    //         }

    //         /** creating a developer script | end */
    //         date_default_timezone_set('Asia/Hong_Kong');
    //         // Get the current date and time
    //         $currentDateTime = date('d/m/Y H:i') . ' HKT';

    //         // Define the text to be replaced
    //         $oldDateTime = '07/12/2024 12:04 HKT';

    //         $priority_access_page_tab = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $priority_access_page_tab);
    //         $pre_queue_page_tab = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $pre_queue_page_tab);
    //         $postqueue_page_tab = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $postqueue_page_tab);
    //         $queue_page_tab = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $queue_page_tab);
    //         // Replace the old date and time with the current date and time
    //         $priority_access_page_tab = str_replace($oldDateTime, $currentDateTime, $priority_access_page_tab);
    //         $pre_queue_page_tab = str_replace($oldDateTime, $currentDateTime, $pre_queue_page_tab);
    //         $postqueue_page_tab = str_replace($oldDateTime, $currentDateTime, $postqueue_page_tab);
    //         $queue_page_tab = str_replace($oldDateTime, $currentDateTime, $queue_page_tab);

    //         $data = [
    //             'room_id' => $room_id ?? null,
    //             'template_id' => $template_id ?? null,
    //             'queue_page_tab' => $queue_page_tab,
    //             'pre_queue_page_tab' => $pre_queue_page_tab,
    //             'postqueue_page_tab' => $postqueue_page_tab,
    //             'priority_access_page_tab' => $priority_access_page_tab,
    //             'languages' => $languages,
    //             'default_language' => $default_language,
    //             'lang_id' => $lang_id,
    //             'scriptDataForJs' => $scriptDataForJs,
    //             'data_intercept_domain' => $queue_room_data->input_url,
    //             'data_intercept' => base64_encode($queue_room_data->id),
    //             // 'data_intercept' => $queue_room_data->id,
    //             'data_c' => $queue_room_data->parent_user_id,
    //             'script' => $script,
    //         ];

    //         return view('canvas', $data);
    //     } else {
    //         return redirect()->route('queue-room-view')->with('warning', 'You can not edit this template!');
    //     }
    // }
    // public function index($room_id = false, $lang_id = 'en')
    // {
    //     $query = 'SELECT qr.id, qr.queue_room_design_tempid, qr.is_uploaded, qr.parent_user_id, qr.queue_html_page_url, qr.postqueue_html_page_url, qr.priorityqueue_html_page_url, qr.prequeue_html_page_url, qrt.is_advance_setting, qrt.advance_setting_rules, qrt.input_url, qr.queue_room_template_id, qdt.languages, qdt.default_language
    //         FROM queuetb_queue_room as qr
    //         INNER JOIN queuetb_design_template as qdt ON qr.queue_room_design_tempid = qdt.id
    //         LEFT JOIN queue_room_template as qrt ON qr.queue_room_template_id = qrt.id
    //         WHERE qr.id = ' . $room_id;

    //     $queue_room_data = DB::selectOne($query);

    //     if ($queue_room_data->is_uploaded != 1) {
    //         // Ensure languages is a valid JSON array
    //         $languages = json_decode($queue_room_data->languages, true);
    //         if (!is_array($languages)) {
    //             $languages = ['en']; // Default to English if languages is not a valid array
    //         }

    //         if (is_null($queue_room_data->default_language)) {
    //             $default_language = 'en';
    //         } else {
    //             $default_language = $queue_room_data->default_language;
    //         }

    //         // Rest of your code remains the same...
    //         if (!empty($queue_room_data->queue_html_page_url) && !empty($queue_room_data->postqueue_html_page_url) && !empty($queue_room_data->priorityqueue_html_page_url) && !empty($queue_room_data->prequeue_html_page_url)) {
    //             $getQueueTemplate = Logics::getStaticHTMLTemplate('queue', $room_id, $queue_room_data->parent_user_id);
    //             if (!empty($getQueueTemplate)) {
    //                 $queue_page_tab = $getQueueTemplate;
    //             } else {
    //                 $queue_page_tab = Logics::getDummyCode('queue_page_tab');
    //             }

    //             $getPrequeueTemplate = Logics::getStaticHTMLTemplate('prequeue', $room_id, $queue_room_data->parent_user_id);
    //             if (!empty($getPrequeueTemplate)) {
    //                 $pre_queue_page_tab = $getPrequeueTemplate;
    //             } else {
    //                 $pre_queue_page_tab = Logics::getDummyCode('pre_queue_page_tab');
    //             }

    //             $getPostqueueTemplate = Logics::getStaticHTMLTemplate('postqueue', $room_id, $queue_room_data->parent_user_id);
    //             if (!empty($getPostqueueTemplate)) {
    //                 $postqueue_page_tab = $getPostqueueTemplate;
    //             } else {
    //                 $postqueue_page_tab = Logics::getDummyCode('postqueue_page_tab');
    //             }

    //             $getPriorityaccessTemplate = Logics::getStaticHTMLTemplate('priorityaccess', $room_id, $queue_room_data->parent_user_id);
    //             if (!empty($getPriorityaccessTemplate)) {
    //                 $priority_access_page_tab = $getPriorityaccessTemplate;
    //             } else {
    //                 $priority_access_page_tab = Logics::getDummyCode('priority_access_page_tab');
    //             }
    //         } else {
    //             $getQueueTemplate = Logics::getStaticHTMLTemplate('queue', null, $queue_room_data->parent_user_id, $queue_room_data->queue_room_design_tempid);
    //             if (!empty($getQueueTemplate)) {
    //                 $queue_page_tab = $getQueueTemplate;
    //             } else {
    //                 $queue_page_tab = Logics::getDummyCode('queue_page_tab');
    //             }

    //             $getPrequeueTemplate = Logics::getStaticHTMLTemplate('prequeue', null, $queue_room_data->parent_user_id, $queue_room_data->queue_room_design_tempid);
    //             if (!empty($getPrequeueTemplate)) {
    //                 $pre_queue_page_tab = $getPrequeueTemplate;
    //             } else {
    //                 $pre_queue_page_tab = Logics::getDummyCode('pre_queue_page_tab');
    //             }

    //             $getPostqueueTemplate = Logics::getStaticHTMLTemplate('postqueue', null, $queue_room_data->parent_user_id, $queue_room_data->queue_room_design_tempid);
    //             if (!empty($getPostqueueTemplate)) {
    //                 $postqueue_page_tab = $getPostqueueTemplate;
    //             } else {
    //                 $postqueue_page_tab = Logics::getDummyCode('postqueue_page_tab');
    //             }

    //             $getPriorityaccessTemplate = Logics::getStaticHTMLTemplate('priorityaccess', null, $queue_room_data->parent_user_id, $queue_room_data->queue_room_design_tempid);
    //             if (!empty($getPriorityaccessTemplate)) {
    //                 $priority_access_page_tab = $getPriorityaccessTemplate;
    //             } else {
    //                 $priority_access_page_tab = Logics::getDummyCode('priority_access_page_tab');
    //             }
    //         }

    //         /** creating a developer script | start */
    //         $developerScript = DeveloperScript::firstOrNew(['id' => 1]);
    //         $script = $developerScript->developer_script;
    //         $scriptDataForJs = '';
    //         if (!empty($queue_room_data->is_advance_setting) && $queue_room_data->is_advance_setting == 1 && !empty($queue_room_data->advance_setting_rules)) {
    //             $advance_setting_rules = json_decode($queue_room_data->advance_setting_rules, true);
    //             if (!empty($advance_setting_rules)) {
    //                 $loop = 1;
    //                 foreach ($advance_setting_rules as $advance_setting_rule) {
    //                     if (!empty($advance_setting_rule['operator'])) {
    //                         $scriptDataForJs .= ' data-condition-op-' . $loop . '="' . $advance_setting_rule['operator'] . '" ';
    //                     }
    //                     if (!empty($advance_setting_rule['condition_place'])) {
    //                         $scriptDataForJs .= ' data-condition-place-' . $loop . '="' . $advance_setting_rule['condition_place'] . '" ';
    //                     }
    //                     if (!empty($advance_setting_rule['condition'])) {
    //                         $scriptDataForJs .= ' data-condition-' . $loop . '="' . $advance_setting_rule['condition'] . '" ';
    //                     }
    //                     if (!empty($advance_setting_rule['value'])) {
    //                         $scriptDataForJs .= ' data-condition-value-' . $loop . '="' . $advance_setting_rule['value'] . '" ';
    //                     }
    //                     $loop++;
    //                 }
    //             }
    //         }

    //         /** creating a developer script | end */
    //         date_default_timezone_set('Asia/Hong_Kong');
    //         $currentDateTime = date('d/m/Y H:i') . ' HKT';
    //         $oldDateTime = '07/12/2024 12:04 HKT';

    //         $priority_access_page_tab = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $priority_access_page_tab);
    //         $pre_queue_page_tab = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $pre_queue_page_tab);
    //         $postqueue_page_tab = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $postqueue_page_tab);
    //         $queue_page_tab = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $queue_page_tab);

    //         $priority_access_page_tab = str_replace($oldDateTime, $currentDateTime, $priority_access_page_tab);
    //         $pre_queue_page_tab = str_replace($oldDateTime, $currentDateTime, $pre_queue_page_tab);
    //         $postqueue_page_tab = str_replace($oldDateTime, $currentDateTime, $postqueue_page_tab);
    //         $queue_page_tab = str_replace($oldDateTime, $currentDateTime, $queue_page_tab);

    //         $data = [
    //             'room_id' => $room_id ?? null,
    //             'template_id' => $template_id ?? null,
    //             'queue_page_tab' => $queue_page_tab,
    //             'pre_queue_page_tab' => $pre_queue_page_tab,
    //             'postqueue_page_tab' => $postqueue_page_tab,
    //             'priority_access_page_tab' => $priority_access_page_tab,
    //             'languages' => $languages,
    //             'default_language' => $default_language,
    //             'lang_id' => $lang_id,
    //             'scriptDataForJs' => $scriptDataForJs,
    //             'data_intercept_domain' => $queue_room_data->input_url,
    //             'data_intercept' => base64_encode($queue_room_data->id),
    //             'data_c' => $queue_room_data->parent_user_id,
    //             'script' => $script,
    //         ];

    //         return view('canvas', $data);
    //     } else {
    //         return redirect()->route('queue-room-view')->with('warning', 'You can not edit this template!');
    //     }
    // }
    public function index($room_id = false, $lang_id = 'en')
    {
        $query = 'SELECT qr.id, qr.queue_room_design_tempid, qr.is_uploaded, qr.parent_user_id, qr.queue_html_page_url, qr.postqueue_html_page_url, qr.priorityqueue_html_page_url, qr.prequeue_html_page_url, qrt.is_advance_setting, qrt.advance_setting_rules, qrt.input_url, qr.queue_room_template_id, qdt.languages, qdt.default_language
        FROM queuetb_queue_room as qr
        INNER JOIN queuetb_design_template as qdt ON qr.queue_room_design_tempid = qdt.id
        LEFT JOIN queue_room_template as qrt ON qr.queue_room_template_id = qrt.id
        WHERE qr.id = ' . $room_id;

        $queue_room_data = DB::selectOne($query);

        if ($queue_room_data->is_uploaded != 1) {
            // Ensure languages is a valid JSON array or fallback to default
            $languages = json_decode($queue_room_data->languages, true);
            if (!is_array($languages)) {
                $languages = ['en']; // Default to English if languages is not a valid array
            }

            if (is_null($queue_room_data->default_language)) {
                $default_language = 'en';
            } else {
                $default_language = $queue_room_data->default_language;
            }

            // Handling template data
            if (!empty($queue_room_data->queue_html_page_url) && !empty($queue_room_data->postqueue_html_page_url) && !empty($queue_room_data->priorityqueue_html_page_url) && !empty($queue_room_data->prequeue_html_page_url)) {
                $getQueueTemplate = Logics::getStaticHTMLTemplate('queue', $room_id, $queue_room_data->parent_user_id);
                if (!empty($getQueueTemplate)) {
                    $queue_page_tab = $getQueueTemplate;
                } else {
                    $queue_page_tab = Logics::getDummyCode('queue_page_tab');
                }

                $getPrequeueTemplate = Logics::getStaticHTMLTemplate('prequeue', $room_id, $queue_room_data->parent_user_id);
                if (!empty($getPrequeueTemplate)) {
                    $pre_queue_page_tab = $getPrequeueTemplate;
                } else {
                    $pre_queue_page_tab = Logics::getDummyCode('pre_queue_page_tab');
                }

                $getPostqueueTemplate = Logics::getStaticHTMLTemplate('postqueue', $room_id, $queue_room_data->parent_user_id);
                if (!empty($getPostqueueTemplate)) {
                    $postqueue_page_tab = $getPostqueueTemplate;
                } else {
                    $postqueue_page_tab = Logics::getDummyCode('postqueue_page_tab');
                }

                $getPriorityaccessTemplate = Logics::getStaticHTMLTemplate('priorityaccess', $room_id, $queue_room_data->parent_user_id);
                if (!empty($getPriorityaccessTemplate)) {
                    $priority_access_page_tab = $getPriorityaccessTemplate;
                } else {
                    $priority_access_page_tab = Logics::getDummyCode('priority_access_page_tab');
                }
            } else {
                // Fallback to default template if specific URLs are empty
                $getQueueTemplate = Logics::getStaticHTMLTemplate('queue', null, $queue_room_data->parent_user_id, $queue_room_data->queue_room_design_tempid);
                if (!empty($getQueueTemplate)) {
                    $queue_page_tab = $getQueueTemplate;
                } else {
                    $queue_page_tab = Logics::getDummyCode('queue_page_tab');
                }

                $getPrequeueTemplate = Logics::getStaticHTMLTemplate('prequeue', null, $queue_room_data->parent_user_id, $queue_room_data->queue_room_design_tempid);
                if (!empty($getPrequeueTemplate)) {
                    $pre_queue_page_tab = $getPrequeueTemplate;
                } else {
                    $pre_queue_page_tab = Logics::getDummyCode('pre_queue_page_tab');
                }

                $getPostqueueTemplate = Logics::getStaticHTMLTemplate('postqueue', null, $queue_room_data->parent_user_id, $queue_room_data->queue_room_design_tempid);
                if (!empty($getPostqueueTemplate)) {
                    $postqueue_page_tab = $getPostqueueTemplate;
                } else {
                    $postqueue_page_tab = Logics::getDummyCode('postqueue_page_tab');
                }

                $getPriorityaccessTemplate = Logics::getStaticHTMLTemplate('priorityaccess', null, $queue_room_data->parent_user_id, $queue_room_data->queue_room_design_tempid);
                if (!empty($getPriorityaccessTemplate)) {
                    $priority_access_page_tab = $getPriorityaccessTemplate;
                } else {
                    $priority_access_page_tab = Logics::getDummyCode('priority_access_page_tab');
                }
            }

            // Developer script logic
            $developerScript = DeveloperScript::firstOrNew(['id' => 1]);
            $script = $developerScript->developer_script;
            $scriptDataForJs = '';
            if (!empty($queue_room_data->is_advance_setting) && $queue_room_data->is_advance_setting == 1 && !empty($queue_room_data->advance_setting_rules)) {
                $advance_setting_rules = json_decode($queue_room_data->advance_setting_rules, true);
                if (is_array($advance_setting_rules) && !empty($advance_setting_rules)) {
                    $loop = 1;
                    foreach ($advance_setting_rules as $advance_setting_rule) {
                        if (!empty($advance_setting_rule['operator'])) {
                            $scriptDataForJs .= ' data-condition-op-' . $loop . '="' . $advance_setting_rule['operator'] . '" ';
                        }
                        if (!empty($advance_setting_rule['condition_place'])) {
                            $scriptDataForJs .= ' data-condition-place-' . $loop . '="' . $advance_setting_rule['condition_place'] . '" ';
                        }
                        if (!empty($advance_setting_rule['condition'])) {
                            $scriptDataForJs .= ' data-condition-' . $loop . '="' . $advance_setting_rule['condition'] . '" ';
                        }
                        if (!empty($advance_setting_rule['value'])) {
                            $scriptDataForJs .= ' data-condition-value-' . $loop . '="' . $advance_setting_rule['value'] . '" ';
                        }
                        $loop++;
                    }
                }
            }

            // Set timezone and update date
            date_default_timezone_set('Asia/Hong_Kong');
            $currentDateTime = date('d/m/Y H:i') . ' HKT';
            $oldDateTime = '07/12/2024 12:04 HKT';

            // Clean up HTML content (remove script tags)
            $priority_access_page_tab = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $priority_access_page_tab);
            $pre_queue_page_tab = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $pre_queue_page_tab);
            $postqueue_page_tab = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $postqueue_page_tab);
            $queue_page_tab = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $queue_page_tab);

            // Replace old date and time with the current one
            $priority_access_page_tab = str_replace($oldDateTime, $currentDateTime, $priority_access_page_tab);
            $pre_queue_page_tab = str_replace($oldDateTime, $currentDateTime, $pre_queue_page_tab);
            $postqueue_page_tab = str_replace($oldDateTime, $currentDateTime, $postqueue_page_tab);
            $queue_page_tab = str_replace($oldDateTime, $currentDateTime, $queue_page_tab);

            // Prepare the data for the view
            $data = [
                'room_id' => $room_id ?? null,
                'template_id' => $queue_room_data->queue_room_design_tempid ?? null,
                'queue_page_tab' => $queue_page_tab,
                'pre_queue_page_tab' => $pre_queue_page_tab,
                'postqueue_page_tab' => $postqueue_page_tab,
                'priority_access_page_tab' => $priority_access_page_tab,
                'languages' => $languages,
                'default_language' => $default_language,
                'lang_id' => $lang_id,
                'scriptDataForJs' => $scriptDataForJs,
                'data_intercept_domain' => $queue_room_data->input_url,
                'data_intercept' => base64_encode($queue_room_data->id),
                'data_c' => $queue_room_data->parent_user_id,
                'script' => $script,
            ];

            return view('canvas', $data);
        }
    }

    public function canvas_store(Request $request)
    {
        try {
            // Extract data from JSON request and sanitize HTML content
            $requestData = $request->all();
            $room_id = $requestData['room_id'] ?? null;
            $template_id = $requestData['template_id'] ?? null;
            $lang_id = $requestData['lang_id'];
            $sanitizedRequestData = array_map(function ($value) {
                return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            }, $requestData);

            if (!empty($template_id)) {
                $getTemplateQueueIds = QueueRoom::where('queue_room_design_tempid', $template_id)->pluck('id');
                if (sizeof($getTemplateQueueIds)) {
                    foreach ($getTemplateQueueIds as $roomId) {
                        /** update bucket static html template for room id */
                        self::commonForInline($roomId, $lang_id, 'queue_page', $sanitizedRequestData['queue_page'], $template_id);
                        self::commonForInline($roomId, $lang_id, 'postqueue_page', $sanitizedRequestData['postqueue_page'], $template_id);
                        self::commonForInline($roomId, $lang_id, 'priorityaccess_page', $sanitizedRequestData['priority_access_page'], $template_id);
                        self::commonForInline($roomId, $lang_id, 'prequeue_page', $sanitizedRequestData['prequeue_page'], $template_id);

                        /** update template bucket static html */
                        self::commonForInline(null, $lang_id, 'queue_page', $sanitizedRequestData['queue_page'], $template_id);
                        self::commonForInline(null, $lang_id, 'postqueue_page', $sanitizedRequestData['postqueue_page'], $template_id);
                        self::commonForInline(null, $lang_id, 'priorityaccess_page', $sanitizedRequestData['priority_access_page'], $template_id);
                        self::commonForInline(null, $lang_id, 'prequeue_page', $sanitizedRequestData['prequeue_page'], $template_id);
                    }
                }
            } else {
                self::commonForInline($room_id, $lang_id, 'queue_page', $sanitizedRequestData['queue_page'], $template_id);
                self::commonForInline($room_id, $lang_id, 'postqueue_page', $sanitizedRequestData['postqueue_page'], $template_id);
                self::commonForInline($room_id, $lang_id, 'priorityaccess_page', $sanitizedRequestData['priority_access_page'], $template_id);
                self::commonForInline($room_id, $lang_id, 'prequeue_page', $sanitizedRequestData['prequeue_page'], $template_id);
            }

            // return $this->commonForInline($room_id, $lang_id, 'queue_page', $sanitizedRequestData['queue_page'], $template_id);
            // self::commonForInline($room_id, $lang_id, 'queue_page', $sanitizedRequestData['queue_page'], $template_id);
            // self::commonForInline($room_id, $lang_id, 'postqueue_page', $sanitizedRequestData['postqueue_page'], $template_id);
            // self::commonForInline($room_id, $lang_id, 'priorityaccess_page', $sanitizedRequestData['priority_access_page'], $template_id);
            // self::commonForInline($room_id, $lang_id, 'prequeue_page', $sanitizedRequestData['prequeue_page'], $template_id);
            // $pages['queue_page'] = self::commonForInline($room_id, $lang_id, 'queue_page', $sanitizedRequestData['queue_page'], $template_id);
            // $pages['postqueue_page'] = self::commonForInline($room_id, $lang_id, 'postqueue_page', $sanitizedRequestData['postqueue_page'], $template_id);
            // $pages['priority_access_page'] = self::commonForInline($room_id, $lang_id, 'priority_access_page', $sanitizedRequestData['priority_access_page'], $template_id);
            // $pages['prequeue_page'] = self::commonForInline($room_id, $lang_id, 'prequeue_page', $sanitizedRequestData['prequeue_page'], $template_id);

            // return $pages;die;
            Session::flash('success', 'Template updated successfully');

            if (!empty($template_id)) {
                $queue_draft = Session::get('queue_draft');
                $queue_draft['QueueRoomDesign_id'] = $template_id;
                Session::put('queue_draft', $queue_draft);
            }

            $getTemplateAssignCount = $getTemplateQueueIds->count();

            return response()->json(['message' => 'Data updated successfully', 'templateCount' => $getTemplateAssignCount], 200);
        } catch (\Exception $e) {
            // return response()->json([
            //             'status' => false,
            //             'type' => 'fail',
            //             'message' => $e->getMessage()
            //         ], 422);
            // Handle any errors that occur during the update process
            Session::flash('error', '<i class="fa fa-check-circle"></i> Tamplate updatedation failed');

            \Log::error('Error occurred while updating data: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update data ' . $e->getMessage()], 500);
        }
    }


    private function updateQueuePages($room_id, $lang_id, $sanitizedRequestData, $template_id)
    {
        $pages = ['queue_page', 'postqueue_page', 'priorityaccess_page', 'prequeue_page'];
        foreach ($pages as $page) {
            self::commonForInline($room_id, $lang_id, $page, $sanitizedRequestData[$page], $template_id);
        }
    }

    // public function commonForInline($room_id, $lang_id, $type, $data, $template_id = 0)
    // {
    //     // $fileName = 'log-' . date('Y-m-d_H-i-s') . '.html';
    //     // $filePath = 'logs/' . $fileName;
    //     // // Save the log string to S3
    //     // return Storage::disk('public')->put($filePath, html_entity_decode($data));

    //     $getQueueUserId = QueueRoom::where('id', $room_id)->value('parent_user_id');
    //     $query = "	SELECT id FROM in_line_tamplates Where (language = '" . $lang_id . "') and (type = '{$type}')";
    //     if (! empty($room_id)) {
    //         $query .= ' and (room_id  = ' . $room_id . ') ';
    //     }
    //     if (! empty($template_id)) {
    //         $query .= ' and (template_id  = ' . $template_id . ') ';
    //     }
    //     $queue_page = DB::select($query);

    //     /** Create S3 bucket | start */
    //     // generate a unique busket name
    //     $typeSplit = explode('_', $type);
    //     $bucketName = 'static-website-'.$typeSplit[0].'-'.$getQueueUserId;
    //     // Initialize the S3 client
    //     $s3 = new S3Client([
    //         'version' => 'latest',
    //         'region'  => env('AWS_DEFAULT_REGION'),
    //         'credentials' => [
    //             'key'    => env('AWS_ACCESS_KEY_ID'),
    //             'secret' => env('AWS_SECRET_ACCESS_KEY'),
    //         ],
    //     ]);
    //     /** Create S3 bucket | end */

    //     if ($queue_page) {
    //         //update
    //         $id = $queue_page[0]->id;
    //         $query = "UPDATE in_line_tamplates SET htm_data = '{$data}' WHERE id =" . $id;
    //         $a = DB::select($query);

    //         /** upload .html file in S3 bucket | start */
    //         try {
    //             // create the S3 bucket
    //             $s3->createBucket([
    //                 'Bucket' => $bucketName,
    //                 'CreateBucketConfiguration' => [
    //                     'LocationConstraint' => env('AWS_DEFAULT_REGION'),
    //                 ],
    //             ]);

    //             // Wait until the bucket is created
    //             $s3->waitUntil('BucketExists', ['Bucket' => $bucketName]);

    //             // Upload the HTML file to the bucket
    //             $s3->putObject([
    //                 'Bucket' => $bucketName,
    //                 'Key'    => 'index.html',
    //                 'Body'   => html_entity_decode($data),
    //                 'ContentType' => 'text/html',
    //             ]);

    //             // Configure the bucket as a static website
    //             $s3->putBucketWebsite([
    //                 'Bucket' => $bucketName,
    //                 'WebsiteConfiguration' => [
    //                     'IndexDocument' => [
    //                         'Suffix' => 'index.html',
    //                     ],
    //                 ],
    //             ]);

    //             // Update bucket public access settings
    //             $s3->putPublicAccessBlock([
    //                 'Bucket' => $bucketName,
    //                 'PublicAccessBlockConfiguration' => [
    //                     'BlockPublicAcls' => false,
    //                     'IgnorePublicAcls' => false,
    //                     'BlockPublicPolicy' => false,
    //                     'RestrictPublicBuckets' => false,
    //                 ],
    //             ]);

    //             // Set bucket policy to make it public
    //             $bucketPolicy = [
    //                 "Version" => "2012-10-17",
    //                 "Statement" => [
    //                     [
    //                         "Sid" => "PublicReadGetObject",
    //                         "Effect" => "Allow",
    //                         "Principal" => "*",
    //                         "Action" => [
    //                             "s3:GetObject"
    //                         ],
    //                         "Resource" => [
    //                             "arn:aws:s3:::$bucketName/*"
    //                         ],
    //                     ],
    //                 ],
    //             ];

    //             // return json_encode($bucketPolicy);

    //             $bucketPolicy = $s3->putBucketPolicy([
    //                 'Bucket' => $bucketName,
    //                 'Policy' => json_encode($bucketPolicy),
    //             ]);

    //             // Construct the website URL
    //             $websiteUrl = "https://{$bucketName}.s3-website-" . env('AWS_DEFAULT_REGION') . ".amazonaws.com";

    //             if ($type == 'queue_page')
    //             {
    //                 $updateData = QueueRoom::where('id', $room_id)->update(['queue_html_page_url' => $websiteUrl]);
    //                 // return 'queue_page'.true;
    //             }elseif ($type == 'postqueue_page') {
    //                 $updateData = QueueRoom::where('id', $room_id)->update(['postqueue_html_page_url' => $websiteUrl]);
    //                 // return 'postqueue_page'.true;
    //             }elseif ($type == 'priority_access_page') {
    //                 $updateData = QueueRoom::where('id', $room_id)->update(['priorityqueue_html_page_url' => $websiteUrl]);
    //                 // return 'priority_access_page'.true;
    //             }else{
    //                 $updateData = QueueRoom::where('id', $room_id)->update(['prequeue_html_page_url' => $websiteUrl]);
    //                 // return 'prequeue_page'.true;
    //             }
    //             return $updateData;
    //         } catch (\Exception $err) {
    //             return response()->json([
    //                 'status' => false,
    //                 'type' => 'fail',
    //                 'message' => $err->getMessage()
    //             ], 422);
    //         }
    //         /** upload .html file in S3 bucket | end */
    //     } else {
    //         //insert
    //         $query = "insert into in_line_tamplates set language='".$lang_id."', `type` = '".$type."', htm_data='{$data}'";
    //         if (! empty($room_id)) {
    //             $query .= ', room_id='.$room_id.'';
    //         }
    //         if (! empty($template_id)) {
    //             $query .= ', template_id='.$template_id.'';
    //         }
    //         /*$query = "INSERT INTO in_line_tamplates (room_id, language, type, htm_data, template_id) VALUES ($room_id, '$lang_id', '$type', '$data', $template_id);";*/
    //         $a = DB::select($query);

    //         /** upload .html file in S3 bucket | start */
    //         try {
    //             // create the S3 bucket
    //             $s3->createBucket([
    //                 'Bucket' => $bucketName,
    //                 'CreateBucketConfiguration' => [
    //                     'LocationConstraint' => env('AWS_DEFAULT_REGION'),
    //                 ],
    //             ]);

    //             // Wait until the bucket is created
    //             $s3->waitUntil('BucketExists', ['Bucket' => $bucketName]);

    //             // Upload the HTML file to the bucket
    //             $s3->putObject([
    //                 'Bucket' => $bucketName,
    //                 'Key'    => 'index.html',
    //                 'Body'   => html_entity_decode($data),
    //                 'ContentType' => 'text/html',
    //             ]);

    //             // Configure the bucket as a static website
    //             $s3->putBucketWebsite([
    //                 'Bucket' => $bucketName,
    //                 'WebsiteConfiguration' => [
    //                     'IndexDocument' => [
    //                         'Suffix' => 'index.html',
    //                     ],
    //                 ],
    //             ]);

    //             // Update bucket public access settings
    //             $s3->putPublicAccessBlock([
    //                 'Bucket' => $bucketName,
    //                 'PublicAccessBlockConfiguration' => [
    //                     'BlockPublicAcls' => false,
    //                     'IgnorePublicAcls' => false,
    //                     'BlockPublicPolicy' => false,
    //                     'RestrictPublicBuckets' => false,
    //                 ],
    //             ]);

    //             // Set bucket policy to make it public
    //             $bucketPolicy = [
    //                 "Version" => "2012-10-17",
    //                 "Statement" => [
    //                     [
    //                         "Sid" => "PublicReadGetObject",
    //                         "Effect" => "Allow",
    //                         "Principal" => "*",
    //                         "Action" => [
    //                             "s3:GetObject"
    //                         ],
    //                         "Resource" => [
    //                             "arn:aws:s3:::$bucketName/*"
    //                         ],
    //                     ],
    //                 ],
    //             ];

    //             // return json_encode($bucketPolicy);

    //            $bucketPolicy = $s3->putBucketPolicy([
    //                 'Bucket' => $bucketName,
    //                 'Policy' => json_encode($bucketPolicy),
    //             ]);

    //             // return $bucketPolicy;die;

    //             // Construct the website URL
    //             $websiteUrl = "https://{$bucketName}.s3." . env      ('AWS_DEFAULT_REGION') . ".amazonaws.com";
    //             if ($type == 'queue_page')
    //             {
    //                 $updateData = QueueRoom::where('id', $room_id)->update(['queue_html_page_url' => $websiteUrl]);
    //                 return 'queue_page'.true;
    //             }elseif ($type == 'postqueue_page') {
    //                 $updateData = QueueRoom::where('id', $room_id)->update(['postqueue_html_page_url' => $websiteUrl]);
    //                 return 'postqueue_page'.true;
    //             }elseif ($type == 'priority_access_page') {
    //                 $updateData = QueueRoom::where('id', $room_id)->update(['priorityqueue_html_page_url' => $websiteUrl]);
    //                 return 'priority_access_page'.true;
    //             }else{
    //                 $updateData = QueueRoom::where('id', $room_id)->update(['prequeue_html_page_url' => $websiteUrl]);
    //                 return 'prequeue_page'.true;
    //             }
    //             return $updateData;
    //         } catch (\Exception $err) {
    //             return response()->json([
    //                 'status' => false,
    //                 'type' => 'fail',
    //                 'message' => $err->getMessage()
    //             ], 422);
    //         }
    //         /** upload .html file in S3 bucket | end */

    //         // Storage::disk('s3')->put($path, file_get_contents($data));
    //     }
    //     // return true;
    // }

    public function commonForInline($room_id, $lang_id, $type, $data, $template_id = 0)
    {
        /** Create S3 bucket | start */
        // generate a unique busket name
        $typeSplit = explode('_', $type);
        if (!empty($room_id)) {
            $getQueueUserId = QueueRoom::where('id', $room_id)->value('parent_user_id');
            $uploadFile = Fileupload::uploadFileInS3($data, $typeSplit[0], $getQueueUserId, $room_id, 1);
            if ($type == 'queue_page') {
                $updateData = QueueRoom::where('id', $room_id)->update(['queue_html_page_url' => $uploadFile]);
            } elseif ($type == 'postqueue_page') {
                $updateData = QueueRoom::where('id', $room_id)->update(['postqueue_html_page_url' => $uploadFile]);
            } elseif ($type == 'priorityaccess_page') {
                $updateData = QueueRoom::where('id', $room_id)->update(['priorityqueue_html_page_url' => $uploadFile]);
            } else {
                $updateData = QueueRoom::where('id', $room_id)->update(['prequeue_html_page_url' => $uploadFile]);
            }
            return $uploadFile;
            // return $updateData;
        } else {
            $getQueueUserId = QueuetbDesignTemplate::where('id', $template_id)->value('parent_user_id');
            $uploadFile = Fileupload::uploadTemplateInS3($data, $typeSplit[0], $getQueueUserId, $template_id);
            return $uploadFile;
        }

        return false;
        /** upload .html file in S3 bucket | end */
    }
}
