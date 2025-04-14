<?php
namespace App\Http\Controllers\queuebackend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Models\QueueRoom;


class StatsApiService extends Controller
{
    public function __construct()
    {
        $this->this_epoch_time = time();
    }

    public function dashGraphData(Request $request)
    {
        $requestData = $request->all();
        $data_time_key = null;
        $room_id = null;
        if (isset($requestData['selectedValue'])) {
            $data_time_key = $requestData['selectedValue'];
        }

        if (isset($requestData['room_id'])) {
           $room_id = $requestData['room_id'];
        }

        $to_time = time();
        $stats_data = [];
        $room_id = $requestData['room_id'];

        $getStartAndEndTimeOfTheQueue = QueueRoom::select('start_time', 'end_time')->where('id', $room_id)->first();

        $enter_queue_room_data = $this->getDataFromDB($data_time_key, 5, $room_id);
        $enter_queue_room_data = $this->extractCount($enter_queue_room_data);
        $url_bypass_data = $this->getDataFromDB($data_time_key, 1, $room_id);
        $url_bypass_data = $this->extractCount($url_bypass_data);
        $finished_queue_room_data =  $this->getDataFromDB($data_time_key, 6, $room_id);
        $finished_queue_room_data = $this->extractCount($finished_queue_room_data);
        $no_traffic_data =  $this->getDataFromDB($data_time_key, 10, $room_id);
        $no_traffic_data = $this->extractCount($no_traffic_data);
        $abandon_queue_data =  $this->getDataFromDB($data_time_key, 0, $room_id);
        $abandon_queue_data = $this->extractCount($abandon_queue_data);
        $all_count_data = $this->getDataFromDB($data_time_key, 99, $room_id);
        $all_count_data = $this->extractCount($all_count_data);
        $abandon_queue_data =  $this->getDataFromDB($data_time_key, 0, $room_id);
        $abandon_queue_data = $this->extractCount($abandon_queue_data);
        $traffic_data =  $this->getDataFromDB($data_time_key, 3, $room_id);
        $traffic_data = $this->extractCount($traffic_data);

        /** making time slot according to start time and end time | start */
        $start = Carbon::today()->setTimeFromTimeString($getStartAndEndTimeOfTheQueue->start_time);
        $end = Carbon::tomorrow()->setTimeFromTimeString($getStartAndEndTimeOfTheQueue->end_time ?? '00:00'); // End of the current day (Midnight)

        $times = [];

        while ($start < $end) {
            $times[] = $start->format('g:i A'); // Format to 12-hour time (e.g., 9:00 AM)
            $start->addMinutes(30);
        }
        /** making time slot according to start time and end time | end */

        $final_response = [

            // 'labels' => '["9:00 AM", "9:30 AM", "10:00 AM", "10:30 AM", "11:00 AM", "11:30 AM", "12:00 PM"]',
            'labels' => $times,

            'datasets' => [

                "Traffic" => $traffic_data,

                "Enter queue room" => $enter_queue_room_data,

                "URL bypass" => $url_bypass_data,

                "No traffic" =>  $no_traffic_data,

                "Finished queue" =>  $finished_queue_room_data,

                "Visitors" =>  $all_count_data,

                "Abandon queue" => $abandon_queue_data,

            ]

        ];



        return response()->json($final_response, 200);

    }



    public function getDataFromDB($data_time_key, $status, $room_id)

    {

        if ($status == 99) {

            $condition_string = " AND  room_id =  " . $room_id . " ";

        } else {

            if ($data_time_key == "TOTAL") {

               // $condition_string = " WHERE status = " . $status . " AND  room_id =  " . $room_id . " ";
                $condition_string = " WHERE  room_id =  " . $room_id . " ";

            } else {

                $condition_string = " AND status = " . $status . " AND  room_id =  " . $room_id . " ";

            }

        }



        $query = "";

        $to_time = $this->this_epoch_time;



        switch ($data_time_key) {

            case "LAST_HOUR":

                $query = "SELECT UNIX_TIMESTAMP(updated_at) DIV (10 * 60) AS label, COUNT(*) AS data_count FROM queuetb_raw_queue_operations WHERE  updated_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR) " . $condition_string . "   GROUP BY  UNIX_TIMESTAMP(updated_at) DIV (10 * 60); ";

                $from_time = strtotime('-1 hour', $to_time);

                while ($from_time <= $to_time) {

                    $x_labels[] = date('h:i A', $from_time);

                    $from_time += 600;

                }

                break;

            case "LAST_DAY":

                $interval = '1 HOUR';

                $query = "SELECT  UNIX_TIMESTAMP(updated_at) DIV (1 * 60 * 60) AS label, COUNT(*) AS data_count  FROM queuetb_raw_queue_operations WHERE updated_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)  " . $condition_string . "   GROUP BY  UNIX_TIMESTAMP(updated_at) DIV (1 * 60 * 60);";

                $from_time = strtotime('-1 day', $to_time);

                while ($from_time <= $to_time) {

                    $x_labels[] = date('h:i A d-m-Y', $from_time);

                    $from_time += 3600;

                }

                break;

            case "LIVE":

                $query = "SELECT  UNIX_TIMESTAMP(updated_at) DIV (1 * 60) AS label, COUNT(*) AS data_count FROM queuetb_raw_queue_operations WHERE   updated_at >= DATE_SUB(NOW(), INTERVAL 20 MINUTE)  " . $condition_string . "      GROUP BY   UNIX_TIMESTAMP(updated_at) DIV (1 * 60); ";

                $from_time = strtotime('- 20 MINUTE', $to_time);

                while ($from_time <= $to_time) {

                    $x_labels[] = date('h:i A', $from_time);

                    $from_time += 60;

                }

                break;

            case "TOTAL":

                // $query = "SELECT DATE_FORMAT(updated_at, '%h:%i %p') AS label, COUNT(*) AS data_count FROM queuetb_raw_queue_operations  " . $condition_string;

                



            // $query = "SELECT DATE_FORMAT(updated_at, '%h:%i %p') AS label, COUNT(*) AS data_count 
            //           FROM queuetb_raw_queue_operations 
            //           $condition_string 
            //           GROUP BY DATE_FORMAT(updated_at, '%h:%i %p')";
            
                    $conditions = [];
                    if (isset($room_id)) {
                        $conditions[] = "room_id = $room_id";
                    }

                    $condition_string = "";
                    if (count($conditions) > 0) {
                        $condition_string = "WHERE " . implode(" AND ", $conditions);
                    }

                     $query = "SELECT DATE_FORMAT(updated_at, '%h:%i %p') AS label, COUNT(*) AS data_count 
                              FROM queuetb_raw_queue_operations 
                              $condition_string 
                              GROUP BY DATE_FORMAT(updated_at, '%h:%i %p')";

                    $x_labels[0] = date("g:iA d-m-Y");


                break;

            default:

                break;

        }

        

        $query_data = DB::select($query);



        if ($query_data) {

            return $query_data;

        } else {

            return [];

        }

    }



    function extractCount($data)

    {

        $blank_count = [];

        if ($data) {

            for ($i = 0; $i < count($data); $i++) {

                $blank_count[] = $data[$i]->data_count;

            }

        }

        return $blank_count;

    }



    function dashSankyData(Request $request)

    {
       
        $requestData = $request->all();



        $data_time_key = null;

        $room_id = null;



        if (isset($requestData['selectedValue'])) {

            $data_time_key = $requestData['selectedValue'];

        }



        if (isset($requestData['room_id'])) {

            $room_id = $requestData['room_id'];

        }

        

        $to_time = time();

        $stats_data = [];

        $room_id = $requestData['room_id'];



        $enter_queue_room_data = $this->getDataFromDB($data_time_key, 5, $room_id);

        $enter_queue_room_data = $this->extractCount($enter_queue_room_data);



        $url_bypass_data = $this->getDataFromDB($data_time_key, 1, $room_id);

        $url_bypass_data = $this->extractCount($url_bypass_data);



        $finished_queue_room_data =  $this->getDataFromDB($data_time_key, 6, $room_id);

        $finished_queue_room_data = $this->extractCount($finished_queue_room_data);



        $no_traffic_data =  $this->getDataFromDB($data_time_key, 10, $room_id);

        $no_traffic_data = $this->extractCount($no_traffic_data);



        $abandon_queue_data =  $this->getDataFromDB($data_time_key, 0, $room_id);

        $abandon_queue_data = $this->extractCount($abandon_queue_data);



        $all_count_data = $this->getDataFromDB($data_time_key, 99, $room_id);

        $all_count_data = $this->extractCount($all_count_data);



        $abandon_queue_data =  $this->getDataFromDB($data_time_key, 0, $room_id);

        $abandon_queue_data = $this->extractCount($abandon_queue_data);



        $traffic_data =  $this->getDataFromDB($data_time_key, 3, $room_id);

        $traffic_data = $this->extractCount($traffic_data);


        // $final_response = [

        //     'labels' => '["9:00 AM", "9:30 AM", "10:00 AM", "10:30 AM", "11:00 AM", "11:30 AM", "12:00 PM"]',

        //     'datasets' => [

        //         "Traffic" => $traffic_data,

        //         "Enter queue room" => $enter_queue_room_data,

        //         "URL bypass" => $url_bypass_data,

        //         "No traffic" =>  $no_traffic_data,

        //         "Finished queue" =>  $finished_queue_room_data,

        //         "Visitors" =>  $all_count_data,

        //         "Abandon queue" => $abandon_queue_data,

        //     ]

        // ];

        

        $requestData = $request->all();

        $room_id = $requestData['room_id'];

		

		



		if(empty($all_count_data) && empty($enter_queue_room_data) && empty($enter_queue_room_data)) {

			$cond = 0;

		} else {

			$cond = 1;

		}

         $final_response = [

            "datasets" => [

                [

                    "data" => [

                        [

                            "from" => "Traffic",

                            "to" => "Visitors",

                            "flow" =>  empty($cond) ? ['1'] : array_map('intval', $all_count_data),

                        ],

                        [

                            "from" => "Traffic",

                            "to" => "Enter queue room",

                            "flow" => empty($cond) ? ['1'] : array_map('intval', $enter_queue_room_data),

                        ],

                        [

                            "from" => "Traffic",

                            "to" => "Visitors",

                            "flow" => empty($cond) ? ['1'] : array_map('intval', $all_count_data),

                        ],

                        [

                            "from" => "Enter queue room",

                            "to" => "Visitors",

                            "flow" => empty($cond) ? ['1'] : array_map('intval', $enter_queue_room_data),

                        ],

                        [

                            "from" => "Enter queue room",

                            "to" => "Abandonqueue",

                            "flow" =>  empty($cond) ? ['1'] : array_map('intval', $abandon_queue_data),

                        ]

                    ]

                ]

            ]

        ]; 

        return response()->json($final_response, 200);

    }

}

