<?php



use Illuminate\Support\Facades\DB;

?>

@extends('common.layouts')



@section('content')



@include('common.sidebar')

@include('common.header')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chartjs-chart-sankey"></script>

<link rel="stylesheet" href="{{ asset('asset/css/statRoom.css') }}">

<style type="text/css">

    a.btn.editinline {

        background: #952FBF;

        padding: 9px 30px;

        border-radius: 23px;

        height: 40px;

        color: white;

        font-size: 18px;

        font-weight: 400;

        border: none;

        line-height: 18px;

    }



    select#chartFilter {

        padding: 9px 30px;

        width: max-content;

        margin-right: 8px;

    }

</style>

<main id="main" class="bgmain">



    <!-- Section -->

    <section class="SectionPadding">

        <div class="container">

            <div class="row mb-3">

                <div class="col-xl-8 col-md-12 stathomeicon">

                    <div aria-label="breadcrumb ">

                        <nav aria-label="breadcrumb" class="QueueBreadCrumb StatsQueueRoom">

                            <ol class="breadcrumb">

                                <li class="breadcrumb-item Homebreadcrumb"><a href="{{ url('dashboard') }}"><i class="fa fa-home" aria-hidden="true"></i></a></li>

                                <li class="breadcrumb-item "><a href="{{ url('stats-room-view') }}">Stats</a></li>

                                <li class="breadcrumb-item "><a href="{{ url('stats-room-view') }}">Stat Room Edit</a></li>

                            </ol>

                        </nav>

                    </div>

                </div>

                <div class="col-md-12 col-xs-12 col-sm-6">

                    <div class="d-flex align-items-center">



                        <select class="ChartFilter" id="chartFilter">

                            <option value="LAST_HOUR">LAST HOUR</option>

                            <option value="LAST_DAY">LAST DAY</option>

                            <option value="TOTAL">TOTAL</option>

                            <option value="LIVE">LIVE</option>

                        </select>





                        <!-- <a >EDIT INLINE</a> -->

                        <select class="ChartFilter" id="editinline" onchange="location = this.value;">

                            <option value="#">Select Language for EDIT InLIne </option>

                            <?php

                            if($languages) {

                                // Provide a default option if no languages are available

                                $defaultUrl = url('edit-inline-room/' . $roomId . '/en');

                                echo "<option value=\"$defaultUrl\">English</option>";

                            } else {

                                // Collect all language codes for the query

                                $placeholders = implode(',', array_fill(0, count($languages), '?'));

                                $query = "SELECT code, name FROM languages WHERE code IN ($placeholders)";

                                $langdata = DB::select($query, $languages);



                                // Use an associative array to map language codes to names

                                $langMap = [];

                                foreach ($langdata as $data) {

                                    $langMap[$data->code] = $data->name;

                                }



                                // Generate dropdown options

                                foreach ($languages as $language) {

                                    $langName = $langMap[$language] ?? 'Unknown'; // Fallback to 'Unknown' if not found

                                    $url = url('edit-inline-room/' . $roomId . '/' . $language);

                                    echo "<option value=\"$url\">$langName</option>";

                                }

                            }

                            ?>

                        </select>



                    </div>



                </div>



                <!-- Chart -->

                <div class="card card-body cardborder mt-3 mb-3">

                    <div class="row">

                        <div class="col-xl-12 col-md-12 col-sm-12 col-xs-12">

                            <h6 class="textheading">Traffic over time</h6>

                        </div>

                        <div class="col-xl-12 col-md-12 col-sm-12 col-xs-12">

                            <canvas id="chLine" height="300" class="ChartDiagram"></canvas>

                        </div>

                    </div>

                </div>



            </div>



            <!-- Second card -->

            <div class="row mt-5">

                <div class="col-xl-4 col-md-4 col-sm-3 col-xs-3 mb-2">

                    <div class="card card-body cardborder">

                        <p class="heading p-0 mb-0">Wait time</p>

                        <h6 class="fs-1">{{ $expected_wait_time }}<span class="fs-4 ms-2">minutes</span></h6>

                    </div>

                </div>

                <div class="col-xl-3 col-md-3 col-sm-3 col-xs-3 mb-2">

                    <div class="card card-body cardborder">

                        <p class="heading p-0 mb-0">Drop out rate</p>

                        <h6 class="fs-1"><?php echo $main_drop_time; ?><span class="fs-1">%</span></h6>

                    </div>

                </div>

                <div class="col-xl-5 col-md-5 col-sm-3 col-xs-3">

                    <div class="card card-body cardborder">

                        <p class="heading p-0 mb-0">Queue rate</p>

                        <div class="d-flex">

                            <h6 class="fs-1">{{ $maxTrafficVisitor }}<span class="fs-5 ms-2">visitors /minutes</span></h6>

                            <a href="#!" class="text-decoration-none editicon mt-3 ms-3" data-bs-toggle="modal" data-bs-target="#editsquare">

                                <span class="material-symbols-outlined">edit_square</span>

                            </a>

                        </div>

                    </div>

                </div>

            </div>



            <!-- Sanky plot -->

            <div class="card card-body cardborder mt-3 mb-3">

                <div class="row">

                    <div class="col-xl-12 col-md-12 col-sm-12 col-xs-12">

                        <h6 class="textheading">Traffic distribution</h6>

                    </div>

                </div>

                <div class="chart">

                    <canvas id="chartData"></canvas>

                </div>

            </div>



        </div>

    </section>

</main>

<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

<script>

    Chart.register(ChartDataLabels);

    document.addEventListener("DOMContentLoaded", function() {

        var colors = ['#952FBF', '#159AA1', '#FF5733', '#33FF57', '#3357FF', '#FF33E6', '#FF5733'];

        var chLine = document.getElementById("chLine").getContext('2d');

        var lineChart = new Chart(chLine, {

            type: 'line',

            data: {

                labels: [],

                datasets: []

            }

        });



        function updateChartWithData(apiData) {

            if (!apiData || typeof apiData !== 'object') {

                console.error('Invalid API response format');

                return;

            }

            // var labels = JSON.parse(apiData.labels);
            var labels = apiData.labels;

            var datasets = apiData.datasets;

            lineChart.data.datasets = [];

            for (var label in datasets) {

                if (datasets.hasOwnProperty(label)) {

                    var data = datasets[label].map(Number);

                    lineChart.data.datasets.push({

                        label: label,

                        data: data,

                        backgroundColor: 'transparent',

                        borderColor: colors[lineChart.data.datasets.length],

                        borderWidth: 4,

                        pointBackgroundColor: colors[lineChart.data.datasets.length],

                        hidden: false

                    });

                }

            }

            lineChart.data.labels = labels;

            lineChart.update();

        }



        function fetchDataFromAPI(selectedValue) {


           
            var url = window.location.href;

            var urlObject = new URL(url);

            var room_id = urlObject.pathname.split('/').pop();

            // fetch(`https://queuing.lambetech.com/api/dashbaord-graph-data?selectedValue=${selectedValue}&room_id=${room_id}`)
            fetch(`<?php echo env('APP_URL');?>api/dashbaord-graph-data?selectedValue=${selectedValue}&room_id=${room_id}`)

                .then(response => response.json())

                .then(data => {

                    updateChartWithData(data);

                })

                .catch(error => {

                    console.error('Error fetching data:', error);

                });

        }



        function handleDropdownChange() {

            var selectedValue = this.value;

            fetchDataFromAPI(selectedValue);
 
            //fetchSankeyData(selectedValue);

        }



        document.getElementById("chartFilter").addEventListener("change", handleDropdownChange);

        var defaultSelectedValue = 'LAST_HOUR';

        fetchDataFromAPI(defaultSelectedValue);


    });

</script>

<script>

document.addEventListener("DOMContentLoaded", function() {

    var ctx = document.getElementById("chartData").getContext("2d");

    var colors = {
        "Traffic": "#952FBF",
        "Enter queue room": "#1458C2",
        "Visitors": "#159AA1",
        "Abandonqueue": "#CA5426"
    };

    function getColor(name) {
        return colors[name] || "#1458C2"; // Default color
    }

    function fetchSankeyData(selectedValue) {
        var url = window.location.href;
        var urlObject = new URL(url);
        var room_id = urlObject.pathname.split('/').pop();
        
        fetch(`<?php echo env('APP_URL');?>api/dashbaord-sanky-data?selectedValue=${selectedValue}&room_id=${room_id}`)
            .then(response => response.json())
            .then(data => {
                renderSankeyChart(data);
            })
            .catch(error => {
                console.error('Error fetching Sankey data:', error);
            });
    }

    function renderSankeyChart(data) {
        // Clear existing chart if it exists
        if (window.chart) {
            window.chart.destroy();
        }

        var datasets = data.datasets.map(dataset => ({
            data: dataset.data.map(item => ({
                from: item.from,
                to: item.to,
                flow: item.flow.reduce((acc, val) => acc + val, 0) // Simplified handling of flow
            })),
            colorFrom: (c) => getColor(c.dataset.data[c.dataIndex].from),
            colorTo: (c) => getColor(c.dataset.data[c.dataIndex].to),
            borderWidth: 2,
        }));

        console.log(datasets);

        window.chart = new Chart(ctx, {
            type: "sankey",
            data: {
                datasets: datasets,
                colorMode: 'gradient',
            },
            options: {
                layout: {
                    padding: {
                        top: 20,
                        bottom: 20,
                        left: 30,
                        right: 30,
                    }
                },
                plugins: {
                    datalabels: {
                        display: true,
                        formatter: (value, context) => {
                            return context.chart.data.datasets[0].data[context.dataIndex].from + ':\n' + context.chart.data.datasets[0].data[context.dataIndex].flow;
                        },
                        align: 'end',  // align to the end of the element
                        anchor: 'end', // anchor to the end of the element
                        offset: 20,   // move 20px to the left
                        color: '#fff',
                        backgroundColor: 'rgba(0, 0, 0, 0.7)',
                        borderRadius: 3,
                        font: {
                            weight: 'bold'
                        }
                    },
                },
            },
        });
    }

    function handleDropdownChanged() {
        var selectedValue = this.value;
        fetchSankeyData(selectedValue);
    }

    document.getElementById("chartFilter").addEventListener("change", handleDropdownChanged);

    var defaultSelectedValue = 'LAST_HOUR';
    fetchSankeyData(defaultSelectedValue);
});

    </script>



<!-- Modal -->

<div class="modal fade" id="editsquare" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title" id="staticBackdropLabel">Edit Max Traffic Visitor</h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>

            <form action="{{ route('update.max_traffic_visitor', $roomId) }}" method="POST">

                @csrf

                <div class="modal-body">

                    <div class="col-md-12 ps-0 pt-2 pb-5">

                        <div class="LeftGreenborder ps-4">

                            <div class="TotalVisitortxt">

                                I want to allow <span><input type="number" class="TotalVisitor" name="max_traffic" placeholder="300" value="{{ $maxTrafficVisitor }}"></span> visitors to enter the protected site per minute.

                            </div>

                            @if ($errors->has('max_traffic'))

                            <span class="text-danger">{{ $errors->first('max_traffic') }}</span>

                            @endif

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                    <button type="submit" class="btn btn-primary">Update</button>

                </div>

            </form>

        </div>

    </div>

</div>



@endsection