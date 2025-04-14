<?php

namespace App\Http\Controllers\queuebackend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Mail;
use App\Notifications\PrequeueMail;

class PreQueueNotiSender extends Controller
{
    
    // private function sendEmailNotification($emailAddress, $emailTemplateId)
    // {
    //     $emailContent = DB::table('queuetb_email_sms')
    //                     ->where('id', $emailTemplateId)
    //                     ->value('html_content');

    //     $emailTemplatePlainText = strip_tags($emailContent);
    //     $emailTemplatePlainText = trim($emailTemplatePlainText);
    //     try 
    //     {
    //         Mail::to($emailAddress)->send(new PrequeueMail($emailTemplatePlainText));
    //         return true;
    //     } 
    //     catch (\Exception $e) 
    //     {
    //         echo "<br>";
    //         var_dump($e->getMessage());
    //         Log::error("Failed to send email: " . $e->getMessage());
    //         return false;
    //     }
    // }

    private function sendEmailNotification($emailAddress, $emailTemplateId)
    {
        // Fetch email content from the database
        $emailContent = DB::table('queuetb_email_sms')
                          ->where('id', $emailTemplateId)
                          ->value('html_content');

        if (!$emailContent) {
            Log::error("Email template not found for ID: $emailTemplateId");
            return false;
        }

        // Prepare email content
        $emailTemplatePlainText = strip_tags($emailContent);
        $emailTemplatePlainText = trim($emailTemplatePlainText);

        try {
            // Send email using a mailable class
            Mail::to($emailAddress)->send(new PrequeueMail($emailTemplatePlainText));
            return true;
        } catch (\Exception $e) {
            // Log the error and return false
            Log::error("Failed to send email to $emailAddress: " . $e->getMessage());
            return false;
        }
    }


    // private function sendSMSNotification($phoneNumber, $smsTemplateId)
    // {
    //     try {
    //         $smsTemplate = DB::table('queuetb_email_sms')
    //             ->where('id', $smsTemplateId)
    //             ->value('html_content');
            
    //         // If the template is null or empty, you might want to handle it as well.
    //         if (empty($smsTemplate)) {
    //             throw new Exception('SMS template not found for the given ID');
    //         }
            
    //         $smsTemplatePlainText = strip_tags($smsTemplate);
    //         $smsTemplatePlainText = trim($smsTemplatePlainText);
                    
    //         $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));
            
    //         $twiliosend = $twilio->messages->create(
    //             $phoneNumber,
    //             [
    //                 'from' => config('services.twilio.phone_number'),
    //                 'body' => $smsTemplatePlainText
    //             ]
    //         );

    //         return true;
    //     } catch (Exception $e) {
    //         // Log the error or handle it according to your application's requirements
    //         Log::error('Error sending SMS: ' . $e->getMessage());
    //         return false;
    //     }
    // }

    private function sendSMSNotification($phoneNumber, $smsTemplateId)
    {
        try {
            // Fetch the SMS template from the database
            $smsTemplate = DB::table('queuetb_email_sms')
                ->where('id', $smsTemplateId)
                ->value('html_content');

            // Check if the template was found
            if (empty($smsTemplate)) {
                Log::error("SMS template not found for ID: $smsTemplateId");
                throw new \Exception('SMS template not found for the given ID');
            }

            // Prepare SMS content
            $smsTemplatePlainText = strip_tags($smsTemplate);
            $smsTemplatePlainText = trim($smsTemplatePlainText);

            // Initialize Twilio client
            $twilio = new \Twilio\Rest\Client(config('services.twilio.sid'), config('services.twilio.token'));

            // Send SMS
            $twilio->messages->create(
                $phoneNumber,
                [
                    'from' => config('services.twilio.phone_number'),
                    'body' => $smsTemplatePlainText
                ]
            );

            return true;
        } catch (\Exception $e) {
            // Log the error with context
            Log::error('Error sending SMS to ' . $phoneNumber . ': ' . $e->getMessage());
            return false;
        }
    }

    // public function PreQueueNotiSender()
    // {
    //     try {
    //         DB::beginTransaction();
    
    //         $this_epoch_time = time();
    //         $query = "SELECT op.id, op.room_id, op.pre_q_notification_mail, qr.sms_notice_tempid, qr.email_notice_tempid 
    //                   FROM queuetb_raw_queue_operations AS op 
    //                   INNER JOIN queuetb_queue_room AS qr ON qr.id = op.room_id 
    //                   WHERE qr.is_started = 0 
    //                     AND qr.is_prequeue = 1 
    //                     AND op.noti_email_sent = 0 
    //                     AND qr.is_ended != 1      
    //                     AND op.pre_q_notification_mail IS NOT NULL 
    //                     -- AND $this_epoch_time > (qr.start_time_epoch - qr.prequeue_starttime * 60)
    //                     -- AND $this_epoch_time < qr.start_time_epoch
    //                   ORDER BY op.id DESC;";
    
    //         $preQueueData = DB::select($query);        
    
    //         foreach ($preQueueData as $data) {
    //             if (filter_var($data->pre_q_notification_mail, FILTER_VALIDATE_EMAIL)) 
    //             {
    //                 $emailTemplateId = $data->email_notice_tempid;
    //                 $send = self::sendEmailNotification($data->pre_q_notification_mail, $emailTemplateId);
    //             } 
    //             else 
    //             {
    //                 $smsTemplateId = $data->sms_notice_tempid;
    //                 $send = self::sendSMSNotification($data->pre_q_notification_mail, $smsTemplateId);
    //             }
    //             if($send == true)
    //             {
    //                 DB::table('queuetb_raw_queue_operations')
    //                 ->where('id', $data->id)
    //                 ->update(['noti_email_sent' => 1]);
    //             }

    //         }
    
    //         DB::commit();
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error("Failed to process pre-queue notifications: " . $e->getMessage());
    //     }
    // }
   
    public function PreQueueNotiSender()
    {
        try {
            DB::beginTransaction();
        
            $this_epoch_time = time();
            $query = "SELECT op.id, op.room_id, op.pre_q_notification_mail, qr.sms_notice_tempid, qr.email_notice_tempid 
                      FROM queuetb_raw_queue_operations AS op 
                      INNER JOIN queuetb_queue_room AS qr ON qr.id = op.room_id 
                      WHERE qr.is_started = 0 
                        AND qr.is_prequeue = 1 
                        AND op.noti_email_sent = 0 
                        AND qr.is_ended != 1      
                        AND op.pre_q_notification_mail IS NOT NULL 
                        -- AND $this_epoch_time > (qr.start_time_epoch - qr.prequeue_starttime * 60)
                        -- AND $this_epoch_time < qr.start_time_epoch
                      ORDER BY op.id DESC;";
    
            $preQueueData = DB::select($query);        
        
            foreach ($preQueueData as $data) {
                $send = false;
            
                if (filter_var($data->pre_q_notification_mail, FILTER_VALIDATE_EMAIL)) {
                    $emailTemplateId = $data->email_notice_tempid;
                    $send = $this->sendEmailNotification($data->pre_q_notification_mail, $emailTemplateId);
                } else {
                    $smsTemplateId = $data->sms_notice_tempid;
                    $send = $this->sendSMSNotification($data->pre_q_notification_mail, $smsTemplateId);
                }
            
                if ($send) {
                    DB::table('queuetb_raw_queue_operations')
                        ->where('id', $data->id)
                        ->update(['noti_email_sent' => 1]);
                }
            }
        
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to process pre-queue notifications: " . $e->getMessage(), ['exception' => $e]);
        }
    }


}
