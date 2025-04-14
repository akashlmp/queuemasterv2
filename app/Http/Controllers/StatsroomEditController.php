<?php

namespace App\Http\Controllers;

use App\Models\QueueRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Climate\Product;

class StatsroomEditController extends Controller
{
    public function index()
    {
        $queueRooms = QueueRoom::all();

        $maxTrafficVisitors = [];

        foreach ($queueRooms as $room) {
            $maxTrafficVisitor = $room->max_traffic_visitor;

            $maxTrafficVisitors[$room->id] = $maxTrafficVisitor;
        }

        return view('stats-room.statsEdit', ['maxTrafficVisitors' => $maxTrafficVisitors, 'languages' => []]);
    }
    
    // public function edit($id)
    // {
    //     $query = 'SELECT qr.id, qr.max_traffic_visitor,qt.languages
    // 		FROM queuetb_queue_room as qr
    //         inner join queuetb_design_template as qt on  qt.id = qr.queue_room_design_tempid
    // 		Where (qr.id  = ' . $id . ')';
    //     $queue_room_data = DB::selectOne($query);
    //     $languages = json_decode($queue_room_data->languages);

    //     $max_traffic_visitor = $queue_room_data->max_traffic_visitor;
    //     $max_traffic_visitor = (int) $max_traffic_visitor;

    //     // $number_query = 'SELECT count(id) as count FROM queuetb_raw_queue_operations where ( room_id = ' . $id . ' ) and (status = 3 OR status = 5) ';

    //    $number_query = 'SELECT COUNT(CASE WHEN status = 3 THEN 1 END) AS count_status_3, COUNT(CASE WHEN status = 5 THEN 1 END) AS count_status_5, COUNT(*) AS total_count FROM queuetb_raw_queue_operations WHERE room_id = :id';

    //     $number_data = DB::selectOne($number_query, ['id' => $id]);

    //     if ($number_data) {
    //         $number_in_line = $number_data->count_status_3+$number_data->count_status_5;
    //         $number_drop = $number_data->total_count;
    //     } else {
    //         $number_in_line = 0;
    //         $number_drop = 0;
    //     }
        
    //     $main_drop_time = (floor($number_drop*$number_in_line))/100;
    //     $expected_wait_time = (floor($number_in_line / $max_traffic_visitor)) + 1;

    //     $max_traffic_visitor = $queue_room_data->max_traffic_visitor;

    //     $data = [
    //         'maxTrafficVisitor' => $max_traffic_visitor,
    //         'roomId' => $id,
    //         'languages' => json_decode($languages),
    //         'expected_wait_time' => $expected_wait_time,
    //         'main_drop_time' => $main_drop_time,
    //     ];

    //     return view('stats-room.statsEdit', $data);
    // }

    public function edit($id)
    {
        // Fetching data using Eloquent
        // $queueRoom = QueueRoom::select('id', 'max_traffic_visitor', 'queue_room_design_tempid')
        //     ->where('id', $id)
        //     ->with(['designTemplate:id,languages'])
        //     ->first();

        $query = 'SELECT qr.id, qr.max_traffic_visitor,qt.languages
    		FROM queuetb_queue_room as qr
            inner join queuetb_design_template as qt on  qt.id = qr.queue_room_design_tempid
    		Where (qr.id  = ' . $id . ')';
        $queueRoom = DB::selectOne($query);

        if (!$queueRoom) {
            // Handle the case where the queue room does not exist
            return redirect()->back()->with('error', 'Queue Room not found.');
        }

        // Decode the languages JSON
        $languages = json_decode($queueRoom->languages);

        // Calculate expected wait time and main drop time
        $max_traffic_visitor = (int)$queueRoom->max_traffic_visitor;

        $number_data = DB::selectOne(
            'SELECT COUNT(CASE WHEN status = 3 THEN 1 END) AS count_status_3, COUNT(CASE WHEN status = 5 THEN 1 END) AS count_status_5, COUNT(*) AS total_count 
             FROM queuetb_raw_queue_operations 
             WHERE room_id = :id',
            ['id' => $id]
        );

        $number_in_line = $number_data->count_status_3 + $number_data->count_status_5;
        $number_drop = $number_data->total_count;

        // $main_drop_time = (floor($number_drop / $number_in_line)) * 100;
        // $main_drop_time = ($number_in_line / $number_drop) * 100;
        $main_drop_time = ($number_drop > 0) ? ($number_in_line / $number_drop) * 100 : 0;


        // $expected_wait_time = (floor($number_in_line / $max_traffic_visitor)) + 1;
        $expected_wait_time = ($max_traffic_visitor > 0) ? (floor($number_in_line / $max_traffic_visitor)) + 1 : 1;


        $data = [
            'maxTrafficVisitor' => $max_traffic_visitor,
            'roomId' => $id,
            'languages' => json_decode($languages),
            'expected_wait_time' => $expected_wait_time,
            'main_drop_time' => number_format($main_drop_time, 2),
            // 'main_drop_time' => $main_drop_time,
        ];

        return view('stats-room.statsEdit', $data);
    }



    public function updateMaxTrafficVisitor(Request $request, $id)
    {
        $request->validate([
            'max_traffic' => 'required|numeric', // Add any validation rules you need
        ]);

        $queueRoom = QueueRoom::findOrFail($id);
        $queueRoom->max_traffic_visitor = $request->max_traffic;
        $queueRoom->save();

        // Redirect back or wherever you need after the update
        return redirect()->back()->with('success', 'Max traffic visitor updated successfully');
    }

    // public function statFilter(Request $request)
    // {
    //     $requestData = $request->all();

    //     $filterValue = $requestData['filterValue'];
    //     $prUserId = Auth::user()->pr_user_id; // Assuming you are using Laravel's default authentication

    //     $queueRoomsQuery = QueueRoom::query()->select('id', 'queue_room_name', 'queue_room_icon', 'max_traffic_visitor')
    //         ->where('parent_user_id', $prUserId);

    //     if ($request->type == 1)
    //     {
    //         $queueRoomsQuery->where('queue_room_name', 'like', '%' . $request->search . '%');
    //     } else {
    //         if ($filterValue == 1) {
    //             // $queueRoomsQuery->where('is_started', 1);
    //             $queueRoomsQuery->where([['is_started', 1], ['is_ended', '!=', 1]]);
    //         } elseif ($filterValue == 2) {
    //             $queueRoomsQuery->where('is_ended', 1);
    //         } else {
    //             // $queueRoomsQuery->where('is_started', 0);
    //             $queueRoomsQuery->where([['is_started', 0], ['is_ended', 0]]);
    //         }
    //     }
        

    //     $filteredQueueRooms = $queueRoomsQuery->get()->toArray();

    //     foreach ($filteredQueueRooms as &$queuestatsRoom) {
    //         // Perform calculations
    //         $max_traffic_visitor = $queuestatsRoom['max_traffic_visitor'];

    //         $number_query = 'SELECT count(id) as count FROM queuetb_raw_queue_operations where ( room_id = ' . $queuestatsRoom['id'] . ' ) and (status = 3 OR status = 5)';
    //         $number_data = DB::selectOne($number_query);
    //         $number_in_line = $number_data ? $number_data->count : 0;

    //         $expected_wait_time = floor($number_in_line / $max_traffic_visitor);

    //         $number_query = 'SELECT sum(storage_occupied_number) as count FROM queue_serial_number_management where ( room_id = ' . $queuestatsRoom['id'] . ' ) and (cron_status = 0 )';
    //         $total_number_data = DB::select($number_query);
    //         $total_number = $total_number_data ? $total_number_data->count : 0;
    //         $temp_total = $total_number ? $total_number : 1;
    //         $per = 1 - (floor($number_in_line / $temp_total));

    //         // Append calculated values to the queuestatsRoom array
    //         $queuestatsRoom['number_in_line'] = $number_in_line;
    //         $queuestatsRoom['expected_wait_time'] = $expected_wait_time;
    //         $queuestatsRoom['total_number'] = $total_number;
    //         $queuestatsRoom['per'] = $per;
    //     }

    //     return response()->json($filteredQueueRooms);
    // }

    public function statFilter(Request $request)
    {
        $filterValue = $request->input('filterValue');
        $search = $request->input('search');
        $type = $request->input('type');
        $prUserId = Auth::user()->pr_user_id;

        $queueRoomsQuery = QueueRoom::select('id', 'queue_room_name',   'queue_room_icon', 'max_traffic_visitor')
            ->where('parent_user_id', $prUserId);

        if ($type == 1) {
            $queueRoomsQuery->where('queue_room_name', 'like', '%' . $search . '%');
        } else {
            switch ($filterValue) {
                case 1:
                    $queueRoomsQuery->where('is_started', 1)
                                    ->where('is_ended', '!=', 1);
                    break;
                case 2:
                    $queueRoomsQuery->where('is_ended', 1);
                    break;
                default:
                    $queueRoomsQuery->where('is_started', 0)
                                    ->where('is_ended', 0);
                    break;
            }
        }

        $filteredQueueRooms = $queueRoomsQuery->get();

        $filteredQueueRooms->transform(function ($queuestatsRoom) {
            // Calculations
            $max_traffic_visitor = $queuestatsRoom->max_traffic_visitor;

            $number_data = DB::table('queuetb_raw_queue_operations')
                ->where('room_id', $queuestatsRoom->id)
                ->whereIn('status', [3, 5])
                ->count();

            $number_in_line = $number_data;
            $expected_wait_time = floor($number_in_line / $max_traffic_visitor);

            $total_number_data = DB::table('queue_serial_number_management')
                ->where('room_id', $queuestatsRoom->id)
                ->where('cron_status', 0)
                ->sum('storage_occupied_number');

            $total_number = $total_number_data;
            $temp_total = $total_number ? $total_number : 1;
            $per = 1 - (floor($number_in_line / $temp_total));

            // Append calculated values to the queuestatsRoom
            $queuestatsRoom->number_in_line = $number_in_line;
            $queuestatsRoom->expected_wait_time = $expected_wait_time;
            $queuestatsRoom->total_number = $total_number;
            $queuestatsRoom->per = $per;

            return $queuestatsRoom;
        });

        return response()->json($filteredQueueRooms);
    }

}
