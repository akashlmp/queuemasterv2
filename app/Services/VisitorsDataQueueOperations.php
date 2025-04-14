<?php

namespace App\Http\Controllers\queuebackend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cookie;


class VisitorsDataQueueOperations extends Controller
{
    public function __construct()
    {
        $this->this_epoch_time = time();
        $this->default_language = "en";
    }

    function operateQueue(Request $request)
    {

        // Extract data from JSON request
        $requestData = $request->all();
        Log::channel('queue-operations')->info(json_encode($requestData));

        $customer_id            =   $requestData['scriptAttributes']['cid'];
        $customer_domain        =   $requestData['scriptAttributes']['domain'];
        $queue_room_id          =   base64_decode($requestData['scriptAttributes']['intercept']);
        // $queue_room_id          =   $requestData['scriptAttributes']['intercept'];
        $browser_session_id     =   $requestData['session_id'];

        //get all browser session_data
        $query = "SELECT id,browser_url,status,queue_serial_number,queue_serial_number_id  FROM queuetb_raw_queue_operations WHERE  browser_session_id = " . $browser_session_id;
        $browser_session_data = DB::select($query);

        $op_id = $browser_session_data[0]->id;

        if ($browser_session_data) {
            $main_status = $browser_session_data[0]->status;
            if ($main_status == 6) 
            {

                $quueu_process_data = $this->processThisSession($queue_room_id);
                $this->updateRawtables($browser_session_id, 6);
                $final_response = $quueu_process_data['final_response'];
                $response_code = $quueu_process_data['response_code'];

                $cookie1 = $quueu_process_data['cookie1'];
                $cookie2 = $quueu_process_data['cookie2'];

                $query = "UPDATE queuetb_queue_room SET currunt_traffic_count  = ( currunt_traffic_count - 1 )  WHERE id = " . $queue_room_id;
                DB::select($query);
            } else if ($main_status == 1) {

                $query = "SELECT id, target_url   FROM queuetb_queue_room  Where id  = " . $queue_room_id;

                $queue_room_data = DB::select($query);

                $target_url = $queue_room_data[0]->target_url;

                $final_response = [
                    'status' => 1,
                    'message' => 'Page has verified bypass access',
                    'qProcessOp' => false,
                    'redirectionUrl' => $target_url
                ];
                $response_code = 200;


                $cookie1 = Cookie::forget('qProcessOp');
                $cookie2 = Cookie::forget('qSessionId');
                $cookie3 = Cookie::forget('checkByepassStatus');



                $query = "UPDATE queuetb_queue_room SET currunt_traffic_count  = ( currunt_traffic_count - 1 )  WHERE id = " . $queue_room_id;
                DB::select($query);
            } else if ($main_status == 4) {



                $query = "SELECT qr.id,qr.start_time_epoch,qr.is_started, qr_design.default_language  
                           FROM queuetb_queue_room as qr 
                            INNER JOIN  queuetb_design_template as qr_design ON qr.queue_room_design_tempid   =  qr_design.id
                           WHERE qr.id = " . $queue_room_id;
                $queue_room_data = DB::select($query);
               
                if (isset($queue_room_data[0]->default_language) or !is_null($queue_room_data[0]->default_language)) {
                    $def_lang = $queue_room_data[0]->default_language;
                } else {
                    $def_lang = $this->default_language;
                }

                $query = "SELECT htm_data FROM in_line_tamplates where type = 'prequeue_page' and room_id = $queue_room_id and
                language = '$def_lang'";
                $lang_data = DB::select($query);

                $htmlContent = html_entity_decode($lang_data[0]->htm_data);

                $endTime = $queue_room_data[0]->start_time_epoch;

                // Calculate the remaining time in seconds
                $remainingTime = $endTime - $this->this_epoch_time;

                // Calculate rounded hours and remaining minutes
                $roundedHours = floor($remainingTime / 3600); // 3600 seconds in an hour
                $remainingMinutes = round(($remainingTime % 3600) / 60); // Remaining minutes after removing complete hours

                // Load HTML content into a DOMDocument
                $dom = new \DOMDocument();
                libxml_use_internal_errors(true); // Disable warnings for malformed HTML
                $dom->loadHTML($htmlContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

                // Find all <span> tags and update the one with the title "custom_timer"
                $spans = $dom->getElementsByTagName('span');
              
                foreach ($spans as $span) {
             
                    if ($span->getAttribute('title') == 'custum_timer') {
                        // Format the new time string using PHP variables
                        $formattedTime = sprintf("%02d hours : %02d min", $roundedHours, $remainingMinutes);
            
                        // Update the <span> tag content with the formatted time
                        $span->nodeValue = $formattedTime;
                    }
                }
            
                // Find all form elements in the document
                $forms = $dom->getElementsByTagName('form');
                foreach ($forms as $form) {
                    // Update the action attribute of each form
                    $form->setAttribute('action', env('APP_URL') . "api/api-submit");
                    $form->setAttribute('method', "post");

                    // Create hidden input fields for room_id and browser_session_id
                    $roomInput = $dom->createElement('input');
                    $roomInput->setAttribute('type', 'hidden');
                    $roomInput->setAttribute('name', 'room_id');
                    $roomInput->setAttribute('value', $queue_room_id);

                    $browserInput = $dom->createElement('input');
                    $browserInput->setAttribute('type', 'hidden');
                    $browserInput->setAttribute('name', 'browser_session_id');
                    $browserInput->setAttribute('value', $browser_session_id);

                    $manual_customer_id = $dom->createElement('input');
                    $manual_customer_id->setAttribute('type', 'hidden');
                    $manual_customer_id->setAttribute('name', 'manual_customer_id');
                    $manual_customer_id->setAttribute('value', $customer_id);

                    // Append the hidden input fields to each form
                    $form->appendChild($roomInput);
                    $form->appendChild($browserInput);
                    $form->appendChild($manual_customer_id);
                }

                // Save the modified HTML back to a string
                $modifiedHtml = $dom->saveHTML();

                // Final response
                $final_response = [
                    'status' => 4,
                    'message' => 'Prequeue has been started',
                    'htmlBody' => $modifiedHtml
                ];
                $response_code = 200;
            } else if ($main_status == 10) {

                $query = "SELECT id, target_url   FROM queuetb_queue_room  Where id  = " . $queue_room_id;

                $queue_room_data = DB::select($query);

                $target_url = $queue_room_data[0]->target_url;

                $final_response = [
                    'status' => 10,
                    'message' => 'Page has not traffic , redirecting to site',
                    'qProcessOp' => false,
                    'redirectionUrl' => $target_url
                ];
                $response_code = 200;


                $cookie1 = Cookie::forget('qProcessOp');
                $cookie2 = Cookie::forget('qSessionId');
                $cookie3 = Cookie::forget('checkByepassStatus');



                $query = "UPDATE queuetb_queue_room SET currunt_traffic_count  = ( currunt_traffic_count - 1 )  WHERE id = " . $queue_room_id;
                DB::select($query);
            } else {
                $browser_url = $browser_session_data[0]->browser_url;

                //get all queue  information related to this user
                $query = "SELECT qr.id, qr.queue_room_name, qr.queue_room_template_id, qr.target_url, qr.bypass_template_id, qr.sms_notice_tempid, qr.email_notice_tempid, qr.enable_bypass, bp_tamp.bypass_url,qr.queue_room_icon,
                qr_tamp.protection_level,qr_tamp.is_advance_setting,qr_tamp.advance_setting_rules,qr_tamp.input_url, qr.max_traffic_visitor,qr.currunt_traffic_count,qr.is_started,qr.is_ended,qr.start_time_epoch,qr_design.default_language
                FROM queuetb_queue_room as qr
                INNER JOIN queue_room_template as qr_tamp ON qr.queue_room_template_id   = qr_tamp.id
                INNER JOIN  queuetb_design_template as qr_design ON qr.queue_room_design_tempid   =  qr_design.id
                LEFT JOIN bypass_template as bp_tamp ON qr.bypass_template_id = bp_tamp.id
                WHERE (qr.parent_user_id = " . $customer_id . ") 
                AND (qr.id  = " . $queue_room_id . ")";
                $queue_room_data = DB::select($query);
                if (isset($queue_room_data[0]->default_language) or !is_null($queue_room_data[0]->default_language)) {
                    $def_lang = $queue_room_data[0]->default_language;
                } else {
                    $def_lang = $this->default_language;
                }
                $is_queue_started     = $queue_room_data[0]->is_started;
                $is_queue_ended       = $queue_room_data[0]->is_ended;
                $max_traffic_visitor  = $queue_room_data[0]->max_traffic_visitor;
                $currunt_traffic_count  = $queue_room_data[0]->currunt_traffic_count;


                if (($is_queue_started == 1) && ($is_queue_ended == 0 or $is_queue_ended == 2)) {

                    $bypass_status  = $this->checkBypass($queue_room_data, $browser_url, $queue_room_id, $customer_id, $browser_session_id);

                    if ($bypass_status['condition_flag']) {
                        $this->updateRawtables($browser_session_id, 2);

                        $final_response = $bypass_status['final_response'];
                        $response_code = $bypass_status['response_code'];


                        $query = "UPDATE queuetb_queue_room SET currunt_traffic_count  = ( currunt_traffic_count - 1 )  WHERE id = " . $queue_room_id;
                        DB::select($query);
                    } else {

                        //in rule case starts
                        $protection_level       = $queue_room_data[0]->protection_level;
                        $is_advance_setting     = $queue_room_data[0]->is_advance_setting;
                        $input_url              = $queue_room_data[0]->input_url;

                        $put_in_queue           = false;

                        $visitor_access_url_domain      = parse_url($browser_url, PHP_URL_HOST);
                        $visitor_access_url_page_path   = parse_url($browser_url, PHP_URL_PATH);
                        $visitor_access_url = $visitor_access_url_domain . $visitor_access_url_page_path;
                        if ($protection_level == 1) {

                            if ($browser_url == $input_url) {
                                $put_in_queue = true;
                            }
                        }

                        if ($protection_level == 2) {

                            $input_url_domain =  parse_url($input_url, PHP_URL_HOST);
                            if ($visitor_access_url_domain == $input_url_domain) {
                                $put_in_queue = true;
                            }
                        }

                        if ($is_advance_setting == 1) {
                            $advance_setting_rules  = json_decode($queue_room_data[0]->advance_setting_rules, true);

                            $condition_op = "";
                            $condition_string  = "";
                            for ($i = 0; $i < count($advance_setting_rules); $i++) {
                                if (($advance_setting_rules[$i]['operator'])) {
                                    $condition_op = $advance_setting_rules[$i]['operator'];
                                }

                                $condition_string  .= " " . $condition_op . " ";

                                if ($advance_setting_rules[$i]['condition_place'] == "HOST_NAME") {

                                    if ($advance_setting_rules[$i]['condition'] == "EQUALS") {

                                        if ($visitor_access_url_domain == $advance_setting_rules[$i]['value']) {
                                            $condition_string  .= " (true) ";
                                        } else {
                                            $condition_string  .= " (false) ";
                                        }
                                    }

                                    if ($advance_setting_rules[$i]['condition'] == "DOES_NOT_EQUAL") {

                                        if ($visitor_access_url_domain != $advance_setting_rules[$i]['value']) {
                                            $condition_string  .= " (true) ";
                                        } else {
                                            $condition_string  .= " (false) ";
                                        }
                                    }

                                    if ($advance_setting_rules[$i]['condition'] == "CONTAINS") {
                                        if (str_contains($visitor_access_url_domain, $advance_setting_rules[$i]['value'])) {
                                            $condition_string  .= " (true) ";
                                        } else {
                                            $condition_string  .= " (false) ";
                                        }
                                    }

                                    if ($advance_setting_rules[$i]['condition'] == "DOES_NOT_CONTAIN") {
                                        if (!str_contains($visitor_access_url_domain, $advance_setting_rules[$i]['value'])) {
                                            $condition_string  .= " (true) ";
                                        } else {
                                            $condition_string  .= " (false) ";
                                        }
                                    }
                                }
                                if ($advance_setting_rules[$i]['condition_place'] == "PAGE_PATH") {

                                    if ($advance_setting_rules[$i]['condition'] == "EQUALS") {

                                        if ($visitor_access_url_page_path == $advance_setting_rules[$i]['value']) {
                                            $condition_string  .= " (true) ";
                                        } else {
                                            $condition_string  .= " (false) ";
                                        }
                                    }
                                    if ($advance_setting_rules[$i]['condition'] == "DOES_NOT_EQUAL") {

                                        if ($visitor_access_url_page_path != $advance_setting_rules[$i]['value']) {
                                            $condition_string  .= " (true) ";
                                        } else {
                                            $condition_string  .= " (false) ";
                                        }
                                    }
                                    if ($advance_setting_rules[$i]['condition'] == "CONTAINS") {
                                        if (str_contains($visitor_access_url_page_path, $advance_setting_rules[$i]['value'])) {
                                            $condition_string  .= " (true) ";
                                        } else {
                                            $condition_string  .= " (false) ";
                                        }
                                    }
                                    if ($advance_setting_rules[$i]['condition'] == "DOES_NOT_CONTAIN") {
                                        if (!str_contains($visitor_access_url_page_path, $advance_setting_rules[$i]['value'])) {
                                            $condition_string  .= " (true) ";
                                        } else {
                                            $condition_string  .= " (false) ";
                                        }
                                    }
                                }

                                if ($advance_setting_rules[$i]['condition_place'] == "PAGE_URL") {

                                    if ($advance_setting_rules[$i]['condition'] == "EQUALS") {

                                        if ($visitor_access_url == $advance_setting_rules[$i]['value']) {
                                            $condition_string  .= " (true) ";
                                        } else {
                                            $condition_string  .= " (false) ";
                                        }
                                    }
                                    if ($advance_setting_rules[$i]['condition'] == "DOES_NOT_EQUAL") {

                                        if ($visitor_access_url != $advance_setting_rules[$i]['value']) {
                                            $condition_string  .= " (true) ";
                                        } else {
                                            $condition_string  .= " (false) ";
                                        }
                                    }
                                    if ($advance_setting_rules[$i]['condition'] == "CONTAINS") {
                                        if (str_contains($visitor_access_url, $advance_setting_rules[$i]['value'])) {
                                            $condition_string  .= " (true) ";
                                        } else {
                                            $condition_string  .= " (false) ";
                                        }
                                    }
                                    if ($advance_setting_rules[$i]['condition'] == "DOES_NOT_CONTAIN") {
                                        if (!str_contains($visitor_access_url, $advance_setting_rules[$i]['value'])) {
                                            $condition_string  .= " (true) ";
                                        } else {
                                            $condition_string  .= " (false) ";
                                        }
                                    }
                                }
                            }
                            if ($condition_string) {
                                eval('$result = $condition_string;');
                                if ($result) {
                                    $put_in_queue = true;
                                } else {
                                    $put_in_queue = false;
                                };
                            }
                        }

                        if ($put_in_queue) {

                            $queue_serial_number = $browser_session_data[0]->queue_serial_number;

                            if (!$queue_serial_number) {
                                $queue_serial_data = $this->assignQueueSerialNumber($queue_room_id, $max_traffic_visitor, $currunt_traffic_count = false);

                                $log_message = "Added to queue for session_id: " . ($browser_session_id) . " queue_serial_number: "  . " is - " . json_encode($queue_serial_data);
                                Log::channel('in-queue-room')->info($log_message);

                                $query = "UPDATE queuetb_raw_queue_operations SET queue_serial_number = '" . $queue_serial_data['queue_serial_number'] . "'  , queue_serial_number_id = " . $queue_serial_data['queue_serial_number_id'] . "  WHERE browser_session_id = " . $browser_session_id;
                                DB::select($query);

                                $queue_serial_number = $queue_serial_data['queue_serial_number'];
                            }

                            $this->updateRawtables($browser_session_id, 5);

                            $number_query = "SELECT count(id) as count FROM queuetb_raw_queue_operations where ( room_id = " . $queue_room_id . " ) and (status = 3 OR status = 5) AND id < " . $op_id;
                            $number_data =   DB::select($number_query);

                            if ($number_data) {
                                $number_in_line = $number_data[0]->count;
                            } else {
                                $number_in_line = 0;
                            }
                            $expected_wait_time = (floor($number_in_line / $max_traffic_visitor)) + 1;

                            $number_query = "SELECT sum(storage_occupied_number) as count FROM queue_serial_number_management where ( room_id = " . $queue_room_id . " ) and (cron_status = 0 )";

                            $total_number =   DB::select($number_query);
                            $total_number = $total_number[0]->count;
                            if (!$total_number or $total_number == 0) {
                                $total_number = 1;
                            }

                            $per = (1 -  (floor($number_in_line / $total_number)));

                            $query = "SELECT htm_data FROM in_line_tamplates where type = 'queue_page' and room_id = $queue_room_id and
                            language = '$def_lang'";
                            $lang_data = DB::select($query);


                            $htmlContent = html_entity_decode($lang_data[0]->htm_data);
                 

                            // Load HTML content into a DOMDocument
                            $dom = new \DOMDocument();
                            libxml_use_internal_errors(true);
                            $dom->loadHTML('<?xml encoding="utf-8" ?>' . $htmlContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

                            
                            // Update the <span> tag with title 'custom_wait_time'
                            $span_tags = $dom->getElementsByTagName('span');
                            foreach ($span_tags as $span_tag) {
                                if ($span_tag->hasAttribute('title') && $span_tag->getAttribute('title') == 'custum_wait_time') {
                                    // Update the <span> tag content with the value of $expected_wait_time
                                    $span_tag->nodeValue = $expected_wait_time . " Min";
                                }
                            }
                            $span_tags1 = $dom->getElementsByTagName('span');
                            foreach ($span_tags1 as $span_tag1) {
                                if ($span_tag1->hasAttribute('title') && $span_tag1->getAttribute('title') == 'custum_in_line') {
                                    // Update the <span> tag content with the value of $number_in_line
                                    $span_tag1->nodeValue = $number_in_line;
                                    // Break out of the loop once the desired <span> tag is found and updated
                                }
                            }

                            // Set inline CSS for elements with class "bar"
                            $bar_elements = $dom->getElementsByTagName('div');
                            foreach ($bar_elements as $bar_element) {
                                if ($bar_element->hasAttribute('class') && $bar_element->getAttribute('class') == 'bar') {
                                    // Set inline CSS for the bar element
                                    $bar_element->setAttribute('style', "width: {$per}%");
                                }
                            }


                            // Find the form tag directly without using form id
                            $form_elements = $dom->getElementsByTagName('form');
                            foreach ($form_elements as $form_element) {
                                // Update the form action value to env('APP_URL')."api/waiting-room-noti-submit"
                                $form_element->setAttribute('action', env('APP_URL') . "api/waiting-room-noti-submit");
                                if ($form_element->hasAttribute('method')) {
                                    $form_element->removeAttribute('method');
                                }
                                // Manually add the method attribute with the value 'post'
                                $form_element->setAttribute('method', 'post');

                                // Create a new input element
                                $new_input = $dom->createElement('input');

                                // Set attributes for the new input element
                                $new_input->setAttribute('type', 'hidden');
                                $new_input->setAttribute('name', 'manual_browser_session_id');
                                $new_input->setAttribute('value', $browser_session_id);
                                
                                
                                $manual_customer_id = $dom->createElement('input');

                                $manual_customer_id->setAttribute('type', 'hidden');
                                $manual_customer_id->setAttribute('name', 'manual_customer_id');
                                $manual_customer_id->setAttribute('value', $customer_id);

                                // Append the new input element to the form
                                $form_element->appendChild($manual_customer_id);                                
                                $form_element->appendChild($new_input);

                            }
                            // Save the modified HTML back to a string
                            $modifiedHtml = $dom->saveHTML();


                            $final_response = [
                                'status' => 5,
                                'message' => 'Visitor is in queue ',
                                'queue_serial_number' => $queue_serial_number,
                                'qProcessOp' => true,
                                'htmlBody' => $modifiedHtml,
                            ];
                            $response_code = 200;
                        } else {

                            $cookie1 = Cookie::forget('qProcessOp');
                            $cookie2 = Cookie::forget('qSessionId');
                            $cookie3 = Cookie::forget('checkByepassStatus');


                            $final_response = [
                                'status' => 0,
                                'message' => 'Visitor is not in queue',
                                'qProcessOp' => false,
                            ];
                            $response_code = 200;
                        }
                    }
                } else {
                    $start_time_epoch       = $queue_room_data[0]->start_time_epoch;

                    if ($this->this_epoch_time  > $start_time_epoch) {

                        $queue_serial_number = $browser_session_data[0]->queue_serial_number;

                        if (!$queue_serial_number) {
                            $queue_serial_data = $this->assignQueueSerialNumber($queue_room_id, $max_traffic_visitor, $currunt_traffic_count = false);

                            $log_message = "Added to queue for session_id: " . ($browser_session_id) . " queue_serial_number: "  . " is - " . json_encode($queue_serial_data);
                            Log::channel('in-queue-room')->info($log_message);

                            $query = "UPDATE queuetb_raw_queue_operations SET queue_serial_number = '" . $queue_serial_data['queue_serial_number'] . "'  , queue_serial_number_id = " . $queue_serial_data['queue_serial_number_id'] . "  WHERE browser_session_id = " . $browser_session_id;
                            DB::select($query);

                            $queue_serial_number = $queue_serial_data['queue_serial_number'];
                        }

                        $this->updateRawtables($browser_session_id, 5);

                        $number_query = "SELECT count(id) as count FROM queuetb_raw_queue_operations where ( room_id = " . $queue_room_id . " ) and (status = 3 OR status = 5) AND id < " . $op_id;
                        $number_data =   DB::select($number_query);

                        if ($number_data) {
                            $number_in_line = $number_data[0]->count;
                        } else {
                            $number_in_line = 0;
                        }
                        $expected_wait_time = (floor($number_in_line / $max_traffic_visitor)) + 1;

                        $number_query = "SELECT sum(storage_occupied_number) as count FROM queue_serial_number_management where ( room_id = " . $queue_room_id . " ) and (cron_status = 0 )";

                        $total_number =   DB::select($number_query);
                        $total_number = $total_number[0]->count;
                        if (!$total_number or $total_number == 0) {
                            $total_number = 1;
                        }

                        $per = (1 -  (floor($number_in_line / $total_number)));
                        $blade_file_data = [
                            'number_in_line' =>  $number_in_line,
                            'waiting_time' => $expected_wait_time . " Min",
                            "progress_bar_per" => $per,
                        ];

                        $query = "SELECT htm_data FROM in_line_tamplates where type = 'queue_page' and room_id = $queue_room_id and
                        language = '$def_lang'";
                        $lang_data = DB::select($query);
                        $htmlContent = html_entity_decode($lang_data[0]->htm_data);

                        // Load HTML content into a DOMDocument
                        $dom = new \DOMDocument();
                        libxml_use_internal_errors(true); // Disable warnings for malformed HTML
                        $dom->loadHTML($htmlContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

                        // Find all <span> tags in the document
                        $spans = $dom->getElementsByTagName('span');

                        // Loop through each <span> tag
                        foreach ($spans as $span) {
                            // Update the <span> tag with specific content based on its position or attributes
                            $class = $span->getAttribute('class');
                            switch ($class) {
                                case 'icryg':
                                    $span->nodeValue = 'You are now in line';
                                    break;
                                case 'irt7z':
                                    $span->nodeValue = 'Sit tight! Your turn is almost here..';
                                    break;
                                case 'ia53m':
                                    $span->nodeValue = 'Your number in line : ' . $number_in_line;
                                    break;
                                case 'ickqe':
                                    $span->nodeValue = 'Estimated wait time : ' . $expected_wait_time . ' Min';
                                    break;
                                default:
                                    // Do nothing for other <span> tags
                                    break;
                            }
                        }

                        // Find all <div> tags in the document
                        $divs = $dom->getElementsByTagName('div');

                        // Loop through each <div> tag
                        foreach ($divs as $div) {
                            // Update the <div> tag with specific content based on its position or attributes
                            $class = $div->getAttribute('class');
                            switch ($class) {
                                case 'i1tbh':
                                    $div->nodeValue = 'update will be shown here';
                                    break;
                                default:
                                    // Do nothing for other <div> tags
                                    break;
                            }
                        }

                        // Find all <form> tags in the document
                        $forms = $dom->getElementsByTagName('form');

                        // Loop through each <form> tag
                        foreach ($forms as $form) {
                            // Update the form action value
                            $form->setAttribute('action', env('APP_URL') . 'api/waiting-room-noti-submit');
                            if ($form->hasAttribute('method')) {
                                $form->removeAttribute('method');
                            }
                            // Manually add the method attribute with the value 'post'
                            $form->setAttribute('method', 'post');

                            // Find the first <input> tag inside the form
                            $inputs = $form->getElementsByTagName('input');
                            $input_browser_session_id = $inputs->item(0); // Assuming it's the first input tag

                            // Update the value of the input with name 'browser_session_id'
                            if ($input_browser_session_id && $input_browser_session_id->getAttribute('name') == 'browser_session_id') {
                                $input_browser_session_id->setAttribute('value', $browser_session_id);
                            }

                            // Create a new <input> tag
                            $new_input = $dom->createElement('input');
                            $new_input->setAttribute('type', 'hidden');
                            $new_input->setAttribute('name', 'manual_browser_session_id');
                            $new_input->setAttribute('value', $browser_session_id);

                            // Append the new input element to the form
                         

                            $manual_customer_id = $dom->createElement('input');

                            // Set attributes for the new input element
                            $manual_customer_id->setAttribute('type', 'hidden');
                            $manual_customer_id->setAttribute('name', 'manual_customer_id');    
                            $manual_customer_id->setAttribute('value', $customer_id);

                            // Append the new input element to the form
                            $form->appendChild($manual_customer_id);
                            $form->appendChild($new_input);
                        }

                        // Save the modified HTML back to a string
                        $modifiedHtml = $dom->saveHTML();

                        $final_response = [
                            'status' => 5,
                            'message' => 'Visitor is in queue ',
                            'queue_serial_number' => $queue_serial_number,
                            'qProcessOp' => true,
                            'htmlBody' => $modifiedHtml,
                        ];
                        $response_code = 200;
                    } else {

                        $cookie1 = Cookie::forget('qProcessOp');
                        $cookie2 = Cookie::forget('qSessionId');
                        $cookie3 = Cookie::forget('checkByepassStatus');


                        $final_response = [
                            'status' => 0,
                            'message' => 'Queue is not started yet',
                            'qProcessOp' => false,
                        ];
                        $response_code = 200;
                    }
                }
            }
        } else {

            $cookie1 = Cookie::forget('qProcessOp');
            $cookie2 = Cookie::forget('qSessionId');
            $cookie3 = Cookie::forget('checkByepassStatus');

            $final_response = [
                'status' => 0,
                'message' => 'session id not found queue',
                'qProcessOp' => false,
            ];
            $response_code = 404;
        }
        $this->addResponseLog($browser_session_id, $final_response, $response_code);

        if (isset($cookie1) && isset($cookie2)) {

            return response()->json($final_response, $response_code)
                ->withCookie($cookie1)
                ->withCookie($cookie2);
        } else {

            return response()->json($final_response, $response_code);
        }
    }

    public function addResponseLog($session_id, $response, $response_code)
    {
        $log_message = "Response for session_id: " . json_encode($session_id) . " code: " . $response_code . " is - " . json_encode($response);
        Log::channel('queue-operations')->info($log_message);
    }

    public function processThisSession($queue_room_id)
    {
        $query = "SELECT qr.id, qr.target_url , qr_tamp.input_url
        FROM queuetb_queue_room as qr 
        INNER JOIN queue_room_template as qr_tamp ON qr.queue_room_template_id   = qr_tamp.id Where qr.id  = " . $queue_room_id;

        $queue_room_data = DB::select($query);

        $target_url = $queue_room_data[0]->target_url;

        $same_target_flag = false;
        if($queue_room_data[0]->target_url == $queue_room_data[0]->input_url)
        {
            $same_target_flag = false;
        }

        $final_response = [];
        $response_code = "";

        $final_response = [
            'status' => 6,
            'message' => 'Visitors queue has been processed',
            'qProcessOp' => false,
            'redirectionUrl' => $target_url,
            'same_target_flag' => $same_target_flag
        ];
        $response_code = 200;


        $cookie1 = Cookie::forget('qProcessOp');
        $cookie2 = Cookie::forget('qSessionId');
        $cookie3 = Cookie::forget('checkByepassStatus');


        $function_return_data = [
            'final_response' => $final_response,
            'response_code' => $response_code,
            'cookie1' => $cookie1,
            'cookie2' => $cookie2,
        ];

        return $function_return_data;
    }

    public function checkBypass($queue_room_data, $browser_url, $queue_room_id, $customer_id, $browser_session_id)
    {
        $final_response = [];
        $response_code = "";
        $condition_flag = false;


        $is_enable_byepass = $queue_room_data[0]->enable_bypass;

        $bypass_url = $queue_room_data[0]->bypass_url;
        $target_url = $queue_room_data[0]->target_url;
        $queue_room_image_url = env('APP_URL') . "public/images/" . $queue_room_data[0]->queue_room_icon;


        if (($is_enable_byepass) && ($browser_url == $bypass_url)) {
            //check if redirecion 

            // $cookie1 = Cookie::forget('qProcessOp');
            // $cookie2 = Cookie::forget('qSessionId');


            $visitor_data['status'] = 2;

            $blade_file = "bypass-tamplates.bypass_access_code";
            $blade_file_data = [
                'room_id' =>  $queue_room_id,
                'customer_id' => $customer_id,
                'queue_room_image_url' => $queue_room_image_url,
                'redirect_url' => $bypass_url,
                'browser_session_id' => $browser_session_id,
                'target_url' => $target_url,

            ];
            $htmlBody = View::make($blade_file, $blade_file_data)->render();

            $final_response = [
                'status' => 2,
                'message' => 'Visitor has bypass access needed passcode access',
                'qProcessOp' => true,
                'htmlBody' => $htmlBody,
            ];
            $response_code = 200;
            $condition_flag = true;
        }

        $function_return_data = [
            'final_response' => $final_response,
            'response_code' => $response_code,
            'condition_flag' => $condition_flag,
        ];

        return $function_return_data;
    }

    public function updateRawtables($browser_session_id, $status)
    {

        $query = "UPDATE queuetb_raw_queue_operations SET status = " . $status . " , last_updated_epoch = " . $this->this_epoch_time . " WHERE browser_session_id = " . $browser_session_id;
        $queue_room_data = DB::select($query);
        $query = "UPDATE queuetb_raw_user_request_log SET status = " . $status . " WHERE browser_session_id = " . $browser_session_id;
        $queue_room_data = DB::select($query);
    }

    public function assignQueueSerialNumber($room_id, $max_traffic_visitor = false)
    {
        if (!$max_traffic_visitor) {
            $query = "SELECT id, max_traffic_visitor, currunt_traffic_count 
            FROM queuetb_queue_room where status = 0 and id = " . $room_id;
            $max_traffic_visitor = DB::select($query);
            $max_traffic_visitor = $max_traffic_visitor[0]->max_traffic_visitor;
            $currunt_traffic_count = $max_traffic_visitor[0]->currunt_traffic_count;
        }

        $query = "SELECT id, slot_number, queue_serial_number, storage_status,storage_free_number,storage_occupied_number,max_traffic_visitor, cron_status 
        FROM queue_serial_number_management where room_id = " . $room_id . "   ORDER BY id DESC   LIMIT 1";

        $queue_serial_number_data = DB::select($query);


        if (($queue_serial_number_data)) {
            $slot_number                = $queue_serial_number_data[0]->slot_number;
            $this_status                 = $queue_serial_number_data[0]->cron_status;

            if ($this_status == 0) {

                $storage_status             = $queue_serial_number_data[0]->storage_status;
                if (($storage_status == 0)) {

                    $queue_serial_number        = $queue_serial_number_data[0]->queue_serial_number;
                    $storage_occupied_number    = $queue_serial_number_data[0]->storage_occupied_number;
                    $storage_free_number        = $queue_serial_number_data[0]->storage_free_number;
                    $updated_storage_occupied_number = $storage_occupied_number + 1;
                    $updated_storage_free_number     = $storage_free_number - 1;

                    if ($updated_storage_occupied_number == $queue_serial_number_data[0]->max_traffic_visitor) {
                        $storage_status = 1;
                    }

                    $query =  "UPDATE queue_serial_number_management SET 
                                storage_occupied_number = " . $updated_storage_occupied_number . ",
                                storage_free_number = " . $updated_storage_free_number . ",
                                storage_status = " . $storage_status . ",
                                max_traffic_visitor = " . $max_traffic_visitor . "
                                WHERE id =  " . $queue_serial_number_data[0]->id;
                    DB::select($query);

                    $data = ['queue_serial_number' => $queue_serial_number, 'queue_serial_number_id' => $queue_serial_number_data[0]->id];
                    return $data;
                } else {
                    $slot_number = $slot_number + 1;
                    $queue_serial_number = $slot_number . "-" . $room_id;


                    $data = [
                        'queue_serial_number' => $queue_serial_number,
                        'slot_number' => $slot_number,
                        'room_id' => $room_id,
                        'started_at' => time(),
                        'max_traffic_visitor' => $max_traffic_visitor,
                        'storage_occupied_number' => 1,
                        'storage_free_number' => $max_traffic_visitor - 1,

                    ];

                    if ($max_traffic_visitor == 1) {
                        $data["storage_status"] = 1;
                    }

                    $insert = DB::table("queue_serial_number_management")->insertGetId($data);

                    if ($insert) {
                        $data = ['queue_serial_number' => $queue_serial_number, 'queue_serial_number_id' => $insert];
                        return $data;
                    } else {
                        return false;
                    }
                }
            } else {
                $slot_number = $slot_number + 1;
                $queue_serial_number = $slot_number . "-" . $room_id;

                $data = [
                    'queue_serial_number' => $queue_serial_number,
                    'slot_number' => $slot_number,
                    'room_id' => $room_id,
                    'started_at' => time(),
                    'max_traffic_visitor' => $max_traffic_visitor,
                    'storage_occupied_number' => 1,
                    'storage_free_number' => $max_traffic_visitor - 1,

                ];
                if ($max_traffic_visitor == 1) {
                    $data['storage_status'] = 1;
                }
                $insert = DB::table("queue_serial_number_management")->insertGetId($data);

                if ($insert) {
                    $data = ['queue_serial_number' => $queue_serial_number, 'queue_serial_number_id' => $insert];
                    return $data;
                } else {
                    return false;
                }
            }
        } else {
            $slot_number = 1;

            $queue_serial_number = $slot_number . "-" . $room_id;


            $data = [
                'queue_serial_number' => $queue_serial_number,
                'slot_number' => $slot_number,
                'room_id' => $room_id,
                'started_at' => time(),
                'max_traffic_visitor' => $max_traffic_visitor,
                'storage_occupied_number' => 1,
                'storage_free_number' => $max_traffic_visitor - 1,

            ];
            if ($max_traffic_visitor == 1) {
                $data['storage_status'] = 1;
            }
            $insert = DB::table("queue_serial_number_management")->insertGetId($data);

            if ($insert) {
                $data = ['queue_serial_number' => $queue_serial_number, 'queue_serial_number_id' => $insert];
                return $data;
            } else {
                return false;
            }
        }
    }

    public function verifyBypassCode(Request $request)
    {
        Log::channel('queue-operations')->info(json_encode($request));

        $requestData    =   $request->all();
        $pass_code    =   $requestData['search2'];
        $room_id    =   $requestData['manual_room_id'];
        $browser_session_id    =   $requestData['manual_browser_session_id'];
        $customer_id    =   $requestData['manual_customer_id'];

        $query =    "SELECT bpc.id 
                    FROM bypass_pass_codes as bpc 
                    inner join queuetb_queue_room as qr ON bpc.bypass_tamp_id = qr.bypass_template_id
                    WHERE ( qr.id = $room_id ) AND ( bpc.pass_code = '" . $pass_code . "') ";

        $pass_code_data = DB::select($query);
        Log::channel('queue-operations')->info(json_encode($pass_code_data));

        if ($pass_code_data) {
            $this->updateRawtables($browser_session_id, 1);
        }

        $query = "SELECT id,browser_url from queuetb_raw_queue_operations  WHERE browser_session_id = '" . $browser_session_id . "' and customer_id =" . $customer_id;
        $queue_room_data = DB::select($query);
        $redirectUrl = $queue_room_data[0]->browser_url;
        return redirect($redirectUrl);
    }


    public function checkBypassCodeStatus(Request $request)
    {

        $requestData    =   $request->all();
        $browser_session_id    =   $requestData['session_id'];
        $queue_room_id          =   base64_decode($requestData['scriptAttributes']['intercept']);
        // $queue_room_id          =   $requestData['scriptAttributes']['intercept'];
        $customer_id = $requestData['scriptAttributes']['cid'] ?? null;


        $query = "SELECT id,status FROM queuetb_raw_queue_operations WHERE  browser_session_id = " . $browser_session_id;
        $browser_session_data = DB::select($query);



        if ($browser_session_data) {
            $main_status = $browser_session_data[0]->status;
            if ($main_status == 1) {
                $final_response = [
                    'status' => 1,
                    'message' => 'Visitor has bypass access ',
                ];
                $response_code = 200;
            } else {
                $query = "SELECT qr.id, qr.target_url, bp_tamp.bypass_url,qr.queue_room_icon,qr_design.default_language 
                FROM queuetb_queue_room as qr
                LEFT JOIN bypass_template as bp_tamp ON qr.bypass_template_id = bp_tamp.id                
                INNER JOIN  queuetb_design_template as qr_design ON qr.queue_room_design_tempid   =  qr_design.id
                WHERE (qr.parent_user_id = " . $customer_id . ") 
                AND (qr.id  = " . $queue_room_id . ")";
                $queue_room_data = DB::select($query);
                if (isset($queue_room_data[0]->default_language) or !is_null($queue_room_data[0]->default_language)) {
                    $this->default_language = $queue_room_data[0]->default_language;
                }

                $bypass_url = $queue_room_data[0]->bypass_url;
                $target_url = $queue_room_data[0]->target_url;
                $queue_room_image_url = env('APP_URL') . "public/images/" . $queue_room_data[0]->queue_room_icon;
                $query = "SELECT htm_data FROM in_line_tamplates where type = 'priority_access_page' and room_id = $queue_room_id and
                language = '$def_lang'";
                $lang_data = DB::select($query);

                $modifiedHtml = html_entity_decode($lang_data[0]->htm_data);
                // Load HTML content into a DOMDocument
                $dom = new \DOMDocument();
                libxml_use_internal_errors(true); // Disable warnings for malformed HTML
                $dom->loadHTML($modifiedHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

                // Find all image elements in the document
                $images = $dom->getElementsByTagName('img');
                foreach ($images as $img) {
                    // Update the src attribute of each image
                    $img->setAttribute('src', $queue_room_image_url);
                }

                // Find all form elements in the document
                $forms = $dom->getElementsByTagName('form');
                foreach ($forms as $form) {
                    // Update the action attribute of each form
                    $form->setAttribute('action', env('APP_URL') . "api/verify-bypass-code");
                    $form->setAttribute('method', "post");

                    // Create hidden input fields for room_id and browser_session_id
                    $manual_room_id = $dom->createElement('input');
                    $manual_room_id->setAttribute('type', 'hidden');
                    $manual_room_id->setAttribute('name', 'manual_room_id');
                    $manual_room_id->setAttribute('value', $queue_room_id);

                    $manual_browser_session_id = $dom->createElement('input');
                    $manual_browser_session_id->setAttribute('type', 'hidden');
                    $manual_browser_session_id->setAttribute('name', 'manual_browser_session_id');
                    $manual_browser_session_id->setAttribute('value', $browser_session_id);

                    $manual_customer_id = $dom->createElement('input');
                    $manual_customer_id->setAttribute('type', 'hidden');
                    $manual_customer_id->setAttribute('name', 'manual_customer_id');
                    $manual_customer_id->setAttribute('value', $customer_id);

                    $manual_target_url = $dom->createElement('input');
                    $manual_target_url->setAttribute('type', 'hidden');
                    $manual_target_url->setAttribute('name', 'manual_target_url');
                    $manual_target_url->setAttribute('value', $target_url);

                    $manual_bypass_url = $dom->createElement('input');
                    $manual_bypass_url->setAttribute('type', 'hidden');
                    $manual_bypass_url->setAttribute('name', 'manual_bypass_url');
                    $manual_bypass_url->setAttribute('value', $bypass_url);

                    // Append the hidden input fields to the form
                    $form->appendChild($manual_room_id);
                    $form->appendChild($manual_browser_session_id);
                    $form->appendChild($manual_customer_id);
                    $form->appendChild($manual_target_url);
                    $form->appendChild($manual_bypass_url);
                }

                // Save the modified HTML back to a string
                $modifiedHtml = $dom->saveHTML();

                $final_response = [
                    'status' => 2,
                    'message' => 'Visitor has bypass access needed passcode access',
                    'qProcessOp' => true,
                    'checkByepassStatus' => true,
                    'htmlBody' => $modifiedHtml,
                ];
                $response_code = 200;
            }
        } else {

            $final_response = [
                'status' => 2,
                'message' => 'Visitor has bypass access needed passcode access',
            ];
            $response_code = 200;
        }

        if (isset($cookie1) && isset($cookie2)) {

            return response()->json($final_response, $response_code)
                ->withCookie($cookie1)
                ->withCookie($cookie2)
                ->withCookie($cookie3);
        } else {

            return response()->json($final_response, $response_code);
        }
    }


    public function notificationRequest(Request $request)
    {
        $requestData = $request->all();
        $email = $requestData['search2'];
        $room_id = $requestData['room_id'];
        $customer_id = $requestData['manual_customer_id'];

        $browser_session_id = $requestData['browser_session_id'];

        $query = "UPDATE queuetb_raw_queue_operations SET pre_q_notification_mail = '" . $email . "' WHERE browser_session_id = '" . $browser_session_id . "'";
        $queue_room_data = DB::select($query);


        $query = "SELECT id,browser_url from queuetb_raw_queue_operations  WHERE browser_session_id = '" . $browser_session_id . "' and customer_id =" . $customer_id;
        $queue_room_data = DB::select($query);
        $redirectUrl = $queue_room_data[0]->browser_url;
        return redirect($redirectUrl);
    }

    public function waitingRoomNotificationRequest(Request $request)
    {

        $requestData = $request->all();
        $email = $requestData['search2'];
        $room_id = $requestData['room_id'];
        $customer_id = $requestData['manual_customer_id'];

        $browser_session_id = $requestData['manual_browser_session_id'];

        $query = "UPDATE queuetb_raw_queue_operations SET q_notification_mail = '" . $email . "' WHERE browser_session_id = '" . $browser_session_id . "'";
        $queue_room_data = DB::select($query);


        $query = "SELECT id,browser_url from queuetb_raw_queue_operations  WHERE browser_session_id = '" . $browser_session_id . "' and customer_id =" . $customer_id;
        $queue_room_data = DB::select($query);
        $redirectUrl = $queue_room_data[0]->browser_url;
        return redirect($redirectUrl);
    }
}
