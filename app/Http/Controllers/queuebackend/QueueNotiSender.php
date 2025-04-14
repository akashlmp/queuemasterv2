<?php

namespace App\Http\Controllers\queuebackend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Mail;
use App\Notifications\QueueMail;

class QueueNotiSender extends Controller
{
    private function sendEmailNotification($emailAddress, $emailTemplateId)
    {
        $emailContent = DB::table('queuetb_email_sms')
            ->where('id', $emailTemplateId)
            ->value('html_content');
        $emailTemplatePlainText = strip_tags($emailContent);
        $emailTemplatePlainText = trim($emailTemplatePlainText);

        try 
        {
            Mail::to($emailAddress)->send(new QueueMail($emailTemplatePlainText));
            return true;
        } 
        catch (\Exception $e) 
        {
            echo "<br>";
            var_dump($e->getMessage());
            Log::error("Failed to send email: " . $e->getMessage());
            return false;
        }
    }

    // private function sendEmailNotification($emailAddress, $emailTemplateId)
    // {
    //     // Fetch the email content from the database
    //     $emailContent = DB::table('queuetb_email_sms')
    //         ->where('id', $emailTemplateId)
    //         ->value('html_content');

    //     // Process the email content
    //     $emailTemplatePlainText = strip_tags($emailContent);
    //     $emailTemplatePlainText = trim($emailTemplatePlainText);

    //     try 
    //     {
    //         // Send the email using a Mailable class
    //         Mail::to($emailAddress)->send(new QueueMail($emailTemplatePlainText));
    //         return true;
    //     } 
    //     catch (\Exception $e) 
    //     {
    //         // Log the error with detailed information
    //         Log::error("Failed to send email to {$emailAddress} with template ID {$emailTemplateId}: " . $e->getMessage(), ['exception' => $e]);
    //         return false;
    //     }
    // }


    private function sendSMSNotification($phoneNumber, $smsTemplateId)
    {
        try {
            $smsTemplate = DB::table('queuetb_email_sms')
                ->where('id', $smsTemplateId)
                ->value('html_content');
            
            // If the template is null or empty, you might want to handle it as well.
            if (empty($smsTemplate)) {
                throw new Exception('SMS template not found for the given ID');
            }
            
            $smsTemplatePlainText = strip_tags($smsTemplate);
            $smsTemplatePlainText = trim($smsTemplatePlainText);
                    
            $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));
            
            $twiliosend = $twilio->messages->create(
                $phoneNumber,
                [
                    'from' => config('services.twilio.phone_number'),
                    'body' => $smsTemplatePlainText
                ]
            );

            return true;
        } catch (Exception $e) {
            // Log the error or handle it according to your application's requirements
            Log::error('Error sending SMS: ' . $e->getMessage());
            return false;
        }
    }

    // private function sendSMSNotification($phoneNumber, $smsTemplateId)
    // {
    //     try {
    //         // Retrieve the SMS template content
    //         $smsTemplate = DB::table('queuetb_email_sms')
    //             ->where('id', $smsTemplateId)
    //             ->value('html_content');

    //         // Handle null or empty template scenario
    //         if (empty($smsTemplate)) {
    //             throw new \RuntimeException('SMS template not found for the given ID: ' . $smsTemplateId);
    //         }

    //         // Process the template content
    //         $smsTemplatePlainText = strip_tags($smsTemplate);
    //         $smsTemplatePlainText = trim($smsTemplatePlainText);

    //         // Initialize Twilio client
    //         $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));

    //         // Send the SMS
    //         $twilio->messages->create(
    //             $phoneNumber,
    //             [
    //                 'from' => config('services.twilio.phone_number'),
    //                 'body' => $smsTemplatePlainText
    //             ]
    //         );

    //         return true;
    //     } catch (\Exception $e) {
    //         // Log the error with detailed information
    //         Log::error('Error sending SMS to ' . $phoneNumber . ' with template ID ' . $smsTemplateId . ': ' . $e->getMessage(), [
    //             'exception' => $e
    //         ]);
    //         return false;
    //     }
    // }


    public function QueueNotiSender()
    {
        try {
            DB::beginTransaction();

            $this_epoch_time = time();
            $query = "SELECT op.id, op.room_id, op.q_notification_mail, qr.sms_notice_tempid, qr.email_notice_tempid 
                      FROM queuetb_raw_queue_operations AS op 
                      INNER JOIN queuetb_queue_room AS qr ON qr.id = op.room_id 
                      WHERE qr.is_started = 1 
                        AND op.q_noti_email_sent = 0 
                        AND qr.is_ended != 1      
                        AND op.q_notification_mail IS NOT NULL 
                      ORDER BY op.id DESC;";

            $preQueueData = DB::select($query);

            foreach ($preQueueData as $data) {
                if (filter_var($data->q_notification_mail, FILTER_VALIDATE_EMAIL)) {
                    $emailTemplateId = $data->email_notice_tempid;
                    $send = self::sendEmailNotification($data->q_notification_mail, $emailTemplateId);
                } else {
                    $smsTemplateId = $data->sms_notice_tempid;
                    $send = self::sendSMSNotification($data->q_notification_mail, $smsTemplateId);
                }
                if($send == true)
                {
                    DB::table('queuetb_raw_queue_operations')
                    ->where('id', $data->id)
                    ->update(['q_noti_email_sent' => 1]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to process pre-queue notifications: " . $e->getMessage());
        }
    }

    // public function QueueNotiSender()
    // {
    //     try {
    //         DB::beginTransaction();

    //         $this_epoch_time = time();

    //         $preQueueData = DB::table('queuetb_raw_queue_operations as op')
    //             ->join('queuetb_queue_room as qr', 'qr.id', '=', 'op.room_id')
    //             ->where('qr.is_started', 1)
    //             ->where('op.q_noti_email_sent', 0)
    //             ->where('qr.is_ended', '!=', 1)
    //             ->whereNotNull('op.q_notification_mail')
    //             ->select('op.id', 'op.room_id', 'op.q_notification_mail', 'qr.sms_notice_tempid', 'qr.email_notice_tempid')
    //             ->orderByDesc('op.id')
    //             ->get();

    //         foreach ($preQueueData as $data) {
    //             if (filter_var($data->q_notification_mail, FILTER_VALIDATE_EMAIL)) {
    //                 $emailTemplateId = $data->email_notice_tempid;
    //                 $send = $this->sendEmailNotification($data->q_notification_mail, $emailTemplateId);
    //             } else {
    //                 $smsTemplateId = $data->sms_notice_tempid;
    //                 $send = $this->sendSMSNotification($data->q_notification_mail, $smsTemplateId);
    //             }

    //             if ($send) {
    //                 DB::table('queuetb_raw_queue_operations')
    //                     ->where('id', $data->id)
    //                     ->update(['q_noti_email_sent' => 1]);
    //             }
    //         }

    //         DB::commit();
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error("Failed to process queue notifications: " . $e->getMessage(), [
    //             'exception' => $e
    //         ]);
    //     }
    // }


 
}
