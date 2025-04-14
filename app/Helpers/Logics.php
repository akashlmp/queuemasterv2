<?php 
namespace App\helpers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Psr\Log\LoggerInterface;
use Illuminate\Support\Str;
use App\Models\QueueRoom;
use App\Helpers\Fileupload;
use DB;

class Logics
{
    /** S3 uploaded .html file dynamic | start */  
    /** This function using for get static .html template for template edit */
    public static function getStaticHTMLTemplate($type, $queueRoomId, $customerId, $templateId = null)
    {
        // Fetch HTML from S3
        $s3 = new S3Client([
            'region'  => env('AWS_DEFAULT_REGION'),
            'version' => 'latest',
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);

        if (!empty($queueRoomId)) {
            $bucket = 'static-website-'.$type.'-'.$queueRoomId. '-' . $customerId;
        } else {
            $bucket = 'static-template-'.$type.'-'.$templateId. '-' . $customerId;
        }

        $checkBucket = Logics::checkBucket($bucket);
        if (empty($checkBucket))
        {
            $htmlqueue = Logics::getDummyCode('queue_page_tab');
            $htmlpostqueue = Logics::getDummyCode('pre_queue_page_tab');
            $htmlpriorityaccess = Logics::getDummyCode('postqueue_page_tab');
            $htmlprequeue = Logics::getDummyCode('priority_access_page_tab');

            if (!empty($queueRoomId))
            {
                // return "call room ".$queueRoomId;die;
                Fileupload::uploadFileInS3($htmlqueue, 'queue', $customerId, $queueRoomId, 1);
                Fileupload::uploadFileInS3($htmlpostqueue, 'postqueue', $customerId, $queueRoomId, 1);
                Fileupload::uploadFileInS3($htmlpriorityaccess, 'priorityaccess', $customerId, $queueRoomId, 1);
                Fileupload::uploadFileInS3($htmlprequeue, 'prequeue', $customerId, $queueRoomId, 1);
            } else {
                // return "call temp ".$templateId;die;
                Fileupload::uploadTemplateInS3($htmlqueue, 'queue', $customerId,$templateId);
                Fileupload::uploadTemplateInS3($htmlpostqueue, 'postqueue',$customerId, $templateId);
                Fileupload::uploadTemplateInS3($htmlpriorityaccess,'priorityaccess', $customerId, $templateId);
                Fileupload::uploadTemplateInS3($htmlprequeue, 'prequeue',$customerId, $templateId);
            }
        }
        
        // $bucket = 'static-website-'.$type.'-119-65';
        $key = 'index.html'; // Path to your HTML file in the bucket

        $result = $s3->getObject([
            'Bucket' => $bucket,
            'Key'    => $key,
        ]);

        $htmlContent = $result['Body'];
        return html_entity_decode($htmlContent);
        // return $htmlContent;
    }

    public static function showQueuePage($queueRoomId, $browserSessionId, $customerId, $thisEpochTime, $per, $numberInLine)
    {
        // // Fetch HTML from S3
        // $s3 = new S3Client([
        //     'region'  => env('AWS_DEFAULT_REGION'),
        //     'version' => 'latest',
        //     'credentials' => [
        //         'key'    => env('AWS_ACCESS_KEY_ID'),
        //         'secret' => env('AWS_SECRET_ACCESS_KEY'),
        //     ],
        // ]);

        // // 'static-website-' . $type .'-'.$roomId. '-' . $userId;
        // $bucket = 'static-website-queue-'.$queueRoomId. '-' . $customerId;
        // $key = 'index.html'; // Path to your HTML file in the bucket

        // $result = $s3->getObject([
        //     'Bucket' => $bucket,
        //     'Key'    => $key,
        // ]);

        $htmlContent = Logics::getStaticHTMLTemplate('queue', $queueRoomId, $customerId);
        $queueRoomData = DB::table('queuetb_queue_room as qr')
                    ->join('queuetb_design_template as qr_design', 'qr.queue_room_design_tempid', '=', 'qr_design.id')
                    ->select('qr.id', 'qr.start_time_epoch', 'qr.is_started', 'qr_design.default_language')
                    ->where('qr.id', $queueRoomId)
                    ->first();

        // Ensure the query result is not empty before accessing its elements
        if (is_null($queueRoomData)) {
            return response()->json(['error' => 'Queue room not found'], 404);
        }

        $htmlContent = html_entity_decode($htmlContent);

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $htmlContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        
        $spanTags = $dom->getElementsByTagName('span');
        foreach ($spanTags as $spanTag) {
            if ($spanTag->hasAttribute('title') && $spanTag->getAttribute('title') == 'cusm_wait_time') {
                $spanTag->nodeValue = "{$expected_wait_time} Min";
            } elseif ($spanTag->hasAttribute('title') && $spanTag->getAttribute('title') == 'cusm_in_line') {
                $spanTag->nodeValue = $numberInLine;
            }
        }
        
        $barElements = $dom->getElementsByTagName('div');
        foreach ($barElements as $barElement) {
            if ($barElement->hasAttribute('class') && $barElement->getAttribute('class') == 'bar') {
                $barElement->setAttribute('style', "width: {$per}%");
            }
        }
        
        $formElements = $dom->getElementsByTagName('form');
        foreach ($formElements as $formElement) {
            $formElement->setAttribute('action', env('APP_URL') . "api/waiting-room-noti-submit");
            if ($formElement->hasAttribute('method')) {
                $formElement->removeAttribute('method');
            }
            $formElement->setAttribute('method', 'post');

            $roomInput = $dom->createElement('input');
            $roomInput->setAttribute('type', 'hidden');
            $roomInput->setAttribute('name', 'room_id');
            $roomInput->setAttribute('value', $queueRoomId);
            $formElement->appendChild($roomInput);
        
            $newInput = $dom->createElement('input');
            $newInput->setAttribute('type', 'hidden');
            $newInput->setAttribute('name', 'manual_browser_session_id');
            $newInput->setAttribute('value', $browserSessionId);
            $formElement->appendChild($newInput);
        
            $manualCustomerId = $dom->createElement('input');
            $manualCustomerId->setAttribute('type', 'hidden');
            $manualCustomerId->setAttribute('name', 'manual_customer_id');
            $manualCustomerId->setAttribute('value', $customerId);
            $formElement->appendChild($manualCustomerId);
        }
        
        $modifiedHtml = $dom->saveHTML();
        // // Append the script tag to the head or body
        // $script = $dom->createElement('script');
        // $script->setAttribute('src', url("swap-temp/js-test/ext.js"));
        // $script->setAttribute('type', 'text/javascript');

        // // Check if there is a <head> element
        // $head = $dom->getElementsByTagName('head')->item(0);
        // if ($head) {
        //     $head->appendChild($script);
        // } else {
        //     // If there's no <head>, append it to the body instead
        //     $body = $dom->getElementsByTagName('body')->item(0);
        //     if ($body) {
        //         $body->appendChild($script);
        //     }
        // }

        // $modifiedHtml = $dom->saveHTML();

        return $modifiedHtml;
    }

    public static function createAndAppendHiddenInput($dom, $form, $name, $value)
    {
        $input = $dom->createElement('input');
        $input->setAttribute('type', 'hidden');
        $input->setAttribute('name', $name);
        $input->setAttribute('value', $value);
        $form->appendChild($input);
    }

    public static function showPreQueuePage($queueRoomId, $browserSessionId, $customerId, $thisEpochTime)
    {
        // // Fetch HTML from S3
        // $s3 = new S3Client([
        //     'region'  => env('AWS_DEFAULT_REGION'),
        //     'version' => 'latest',
        //     'credentials' => [
        //         'key'    => env('AWS_ACCESS_KEY_ID'),
        //         'secret' => env('AWS_SECRET_ACCESS_KEY'),
        //     ],
        // ]);

        // // 'static-website-' . $type .'-'.$roomId. '-' . $userId;
        // $bucket = 'static-website-prequeue-'.$queueRoomId. '-' . $customerId;
        // $key = 'index.html'; // Path to your HTML file in the bucket

        // $result = $s3->getObject([
        //     'Bucket' => $bucket,
        //     'Key'    => $key,
        // ]);

        $htmlContent = Logics::getStaticHTMLTemplate('prequeue', $queueRoomId, $customerId);

        $queueRoomData = DB::table('queuetb_queue_room as qr')
                    ->join('queuetb_design_template as qr_design', 'qr.queue_room_design_tempid', '=', 'qr_design.id')
                    ->select('qr.id', 'qr.start_time_epoch', 'qr.is_started', 'qr_design.default_language')
                    ->where('qr.id', $queueRoomId)
                    ->first();

        // Decode HTML content
        $htmlContent = html_entity_decode($htmlContent);

        // Calculate remaining time
        $endTime = $queueRoomData->start_time_epoch;
        $remainingTime = ($endTime - $thisEpochTime);

        $roundedHours = floor($remainingTime / 3600);
        $remainingMinutes = round(($remainingTime % 3600) / 60);

        // Modify the HTML content
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true); 
        $dom->loadHTML($htmlContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $spans = $dom->getElementsByTagName('span');
        foreach ($spans as $span) {
            if ($span->getAttribute('title') == 'cusm_timer') {
                $formattedTime = sprintf("%02d hours : %02d min", $roundedHours, $remainingMinutes);
                $span->nodeValue = $formattedTime;
            }
        }

        $forms = $dom->getElementsByTagName('form');
        foreach ($forms as $form) {
            // Set the form action URL and method
            $form->setAttribute('action', env('APP_URL') . "api/api-submit");
            $form->setAttribute('method', "post");
        
            // Create and append hidden inputs
            Logics::createAndAppendHiddenInput($dom, $form, 'room_id', $queueRoomId);
            Logics::createAndAppendHiddenInput($dom, $form, 'browser_session_id', $browserSessionId);
            Logics::createAndAppendHiddenInput($dom, $form, 'manual_customer_id', $customerId);
        }

        $modifiedHtml = $dom->saveHTML();

        // // Append the script tag to the head or body
        // $script = $dom->createElement('script');
        // $script->setAttribute('src', url("swap-temp/js-test/ext.js"));
        // $script->setAttribute('type', 'text/javascript');

        // // Check if there is a <head> element
        // $head = $dom->getElementsByTagName('head')->item(0);
        // if ($head) {
        //     $head->appendChild($script);
        // } else {
        //     // If there's no <head>, append it to the body instead
        //     $body = $dom->getElementsByTagName('body')->item(0);
        //     if ($body) {
        //         $body->appendChild($script);
        //     }
        // }

        // // Save the modified HTML
        // $modifiedHtml = $dom->saveHTML();
        return $modifiedHtml;
    }

    public static function showQueuePageWithSwitch($queueRoomId, $numberInLine, $expectedWaitTime, $browserSessionId, $customerId)
    {
        // // Fetch HTML from S3
        // $s3 = new S3Client([
        //     'region'  => env('AWS_DEFAULT_REGION'),
        //     'version' => 'latest',
        //     'credentials' => [
        //         'key'    => env('AWS_ACCESS_KEY_ID'),
        //         'secret' => env('AWS_SECRET_ACCESS_KEY'),
        //     ],
        // ]);

        // // 'static-website-' . $type .'-'.$roomId. '-' . $userId;
        // $bucket = 'static-website-queue-'.$queueRoomId. '-' . $customerId;
        // $key = 'index.html'; // Path to your HTML file in the bucket

        // $result = $s3->getObject([
        //     'Bucket' => $bucket,
        //     'Key'    => $key,
        // ]);

        $htmlContent = Logics::getStaticHTMLTemplate('queue', $queueRoomId, $customerId);

        $htmlContent = html_entity_decode($htmlContent);
                    
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($htmlContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        
        // Update spans
        $spans = $dom->getElementsByTagName('span');
        foreach ($spans as $span) {
            $class = $span->getAttribute('class');
            switch ($class) {
                case 'icryg':
                    $span->nodeValue = 'You are now in line';
                    break;
                case 'irt7z':
                    $span->nodeValue = 'Sit tight! Your turn is almost here..';
                    break;
                case 'ia53m':
                    $span->nodeValue = 'Your number in line : ' . $numberInLine;
                    break;
                case 'ickqe':
                    $span->nodeValue = 'Estimated wait time : ' . $expectedWaitTime . ' Min';
                    break;
                default:
                    break;
            }
        }
        
        // Update divs
        $divs = $dom->getElementsByTagName('div');
        foreach ($divs as $div) {
            $class = $div->getAttribute('class');
            switch ($class) {
                case 'i1tbh':
                    $div->nodeValue = 'update will be shown here';
                    break;
                default:
                    break;
            }
        }
        
        // Update forms
        $forms = $dom->getElementsByTagName('form');
        foreach ($forms as $form) {
            $form->setAttribute('action', env('APP_URL') . 'api/waiting-room-noti-submit');
            if ($form->hasAttribute('method')) {
                $form->removeAttribute('method');
            }
            $form->setAttribute('method', 'post');
        
            $inputs = $form->getElementsByTagName('input');
            $inputBrowserSessionId = $inputs->item(0);
        
            if ($inputBrowserSessionId && $inputBrowserSessionId->getAttribute('name') == 'browser_session_id') {
                $inputBrowserSessionId->setAttribute('value', $browserSessionId);
            }

            $roomInput = $dom->createElement('input');
            $roomInput->setAttribute('type', 'hidden');
            $roomInput->setAttribute('name', 'room_id');
            $roomInput->setAttribute('value', $queueRoomId);
            $form->appendChild($roomInput);
        
            $newInput = $dom->createElement('input');
            $newInput->setAttribute('type', 'hidden');
            $newInput->setAttribute('name', 'manual_browser_session_id');
            $newInput->setAttribute('value', $browserSessionId);
        
            $manualCustomerId = $dom->createElement('input');
            $manualCustomerId->setAttribute('type', 'hidden');
            $manualCustomerId->setAttribute('name', 'manual_customer_id');
            $manualCustomerId->setAttribute('value', $customerId);
        
            $form->appendChild($manualCustomerId);
            $form->appendChild($newInput);
        }
        
        $modifiedHtml = $dom->saveHTML();

        // // Append the script tag to the head or body
        // $script = $dom->createElement('script');
        // $script->setAttribute('src', url("swap-temp/js-test/ext.js"));
        // $script->setAttribute('type', 'text/javascript');

        // // Check if there is a <head> element
        // $head = $dom->getElementsByTagName('head')->item(0);
        // if ($head) {
        //     $head->appendChild($script);
        // } else {
        //     // If there's no <head>, append it to the body instead
        //     $body = $dom->getElementsByTagName('body')->item(0);
        //     if ($body) {
        //         $body->appendChild($script);
        //     }
        // }

        // // Save the modified HTML
        // $modifiedHtml = $dom->saveHTML();
        return $modifiedHtml;
    }

    public static function showPriorityAccessPage($queueRoomId, $queueRoomImageUrl, $browserSessionId, $customerId, $targetURL, $bypassURL)
    {
        // // Fetch HTML from S3
        // $s3 = new S3Client([
        //     'region'  => env('AWS_DEFAULT_REGION'),
        //     'version' => 'latest',
        //     'credentials' => [
        //         'key'    => env('AWS_ACCESS_KEY_ID'),
        //         'secret' => env('AWS_SECRET_ACCESS_KEY'),
        //     ],
        // ]);

        // // 'static-website-' . $type .'-'.$roomId. '-' . $userId;
        // $bucket = 'static-website-priorityaccess-'.$queueRoomId. '-' . $customerId;
        // $key = 'index.html'; // Path to your HTML file in the bucket

        // $result = $s3->getObject([
        //     'Bucket' => $bucket,
        //     'Key'    => $key,
        // ]);

        // $htmlContent = $result['Body'];

        $htmlContent = Logics::getStaticHTMLTemplate('priorityaccess', $queueRoomId, $customerId);

        $modifiedHtml = html_entity_decode($htmlContent);
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($modifiedHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        // Modify <img> elements
        $images = $dom->getElementsByTagName('img');
        foreach ($images as $img) {
            $img->setAttribute('src', $queueRoomImageUrl);
        }
    
        // Create an array of hidden fields once
        $hiddenFields = [
            'manual_room_id' => $queueRoomId,
            'manual_browser_session_id' => $browserSessionId,
            'manual_customer_id' => $customerId,
            'manual_target_url' => $targetURL,
            'manual_bypass_url' => $bypassURL
        ];
    
        // Modify <form> elements
        $forms = $dom->getElementsByTagName('form');
    
        $actionUrl = env('APP_URL') . "/api/verify-bypass-code";
        $method = 'post';
        foreach ($forms as $form) {
            // Set form attributes
            $form->setAttribute('action', $actionUrl);
            $form->setAttribute('method', $method);
        
            // Create and append hidden fields
            foreach ($hiddenFields as $name => $value) {
                $input = $dom->createElement('input');
                $input->setAttribute('type', 'hidden');
                $input->setAttribute('name', $name);
                $input->setAttribute('value', $value);
                $form->appendChild($input);
            }
             $roomInput = $dom->createElement('input');
            $roomInput->setAttribute('type', 'hidden');
            $roomInput->setAttribute('name', 'room_id');
            $roomInput->setAttribute('value', $queueRoomId);
            $form->appendChild($roomInput);
        }
    
        $modifiedHtml = $dom->saveHTML();

        // // Append the script tag to the head or body
        // $script = $dom->createElement('script');
        // $script->setAttribute('src', url("swap-temp/js-test/ext.js"));
        // $script->setAttribute('type', 'text/javascript');

        // // Check if there is a <head> element
        // $head = $dom->getElementsByTagName('head')->item(0);
        // if ($head) {
        //     $head->appendChild($script);
        // } else {
        //     // If there's no <head>, append it to the body instead
        //     $body = $dom->getElementsByTagName('body')->item(0);
        //     if ($body) {
        // $body->appendChild($script);
        //     }
        // }

        // // Save the modified HTML
        // $modifiedHtml = $dom->saveHTML();
        return $modifiedHtml;
    }

    /** This function using for checking the bucket is exist or not */
    public static function checkBucket($bucket)
    {
        $s3 = new S3Client([
            'region'  => env('AWS_DEFAULT_REGION'),
            'version' => 'latest',
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);

        /** Checking bucket is exist or not */
        $buckets = $s3->listBuckets();
        $bucketExists = false;
        foreach ($buckets['Buckets'] as $buckenName) {
            if ($buckenName['Name'] === $bucket) {
                $bucketExists = true;
                break;
            }
        }

        return $bucketExists;
    }

    /** This function using for the dummy html code */
    public static function getDummyCode($type)
    {
        $query = "SELECT html_data FROM default_html_data where type ='{$type}' ";
        $default_html_data = DB::selectOne($query);
        return $default_html_data->html_data;
    }
    /** S3 uploaded .html file dynamic | end */

    /** This function for checking the time is started or not | start */
    public static function checkStartCondition($prequeueStarttime, $startTimeEpoch) {
        $prequeueStarttimeSeconds = $prequeueStarttime * 60;
        $currentTime = Carbon::now()->timestamp;
        $timeDifference = $startTimeEpoch - $currentTime;
        // if ($timeDifference >= $prequeueStarttimeSeconds) {
        if ($timeDifference <= $prequeueStarttimeSeconds) {
            return true;  // Time has started
        } else {
            return false; // Time has not started
        }
    }
    /** This function for checking the time is started or not | end */
}
?>