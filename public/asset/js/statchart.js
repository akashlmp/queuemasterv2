document.addEventListener("DOMContentLoaded", function () {
    var colors = ['#952FBF', '#159AA1', '#FF5733', '#33FF57', '#3357FF', '#FF33E6', '#FF5733'];
    var chLine = document.getElementById("chLine").getContext('2d');
    var chartData = {
        datasets: [{
                            label: 'Traffic',
                            data: [],
                            backgroundColor: 'transparent',
                            borderColor: colors[0],
                            borderWidth: 4,
                            pointBackgroundColor: colors[0],
                            // hidden: true 
                        },
                        {
                            label: 'Enter queue room',
                            data: [],
                            backgroundColor: 'transparent',
                            borderColor: colors[1],
                            borderWidth: 4,
                            pointBackgroundColor: colors[1],
                            hidden: true
                        },
                        {
                            label: 'URL bypass',
                            data: [],
                            backgroundColor: 'transparent',
                            borderColor: colors[2],
                            borderWidth: 4,
                            pointBackgroundColor: colors[2],
                            // hidden: true 
                        },
                        {
                            label: 'No traffic',
                            data: [],
                            backgroundColor: 'transparent',
                            borderColor: colors[3],
                            borderWidth: 4,
                            pointBackgroundColor: colors[3],
                            hidden: true 
                        },
                        {
                            label: 'Finished queue',
                            data: [],
                            backgroundColor: 'transparent',
                            borderColor: colors[4],
                            borderWidth: 4,
                            pointBackgroundColor: colors[4],
                            hidden: true 
                        },
                        {
                            label: 'Visitors',
                            data: [],
                            backgroundColor: 'transparent',
                            borderColor: colors[5],
                            borderWidth: 4,
                            pointBackgroundColor: colors[5],
                            hidden: true 
                        },
                        {
                            label: 'Abandon queue',
                            data: [],
                            backgroundColor: 'transparent',
                            borderColor: colors[6],
                            borderWidth: 4,
                            pointBackgroundColor: colors[6],
                            hidden: true 
                        }
                    ]
                };

    var lineChart = new Chart(chLine, {
        type: 'line',
        data: chartData
    });
    // Function to update labels based on dropdown selection
    function updateLabels(selectedValue) {
        var labels = [];
        var datasets = chartData.datasets;
        switch (selectedValue) {
            case 'LAST_HOUR':
                labels = ["9:00 AM", "9:30 AM", "10:00 AM", "10:30 AM", "11:00 AM", "11:30 AM", "12:00 PM"];
                // Update datasets accordingly
                datasets.forEach(function(dataset) {
                    dataset.data = [120, 500, 200, 330, 250, 680, 700];
                    dataset.hidden = false; // Make all datasets visible
                });
                break;

            case 'LAST_DAY':
                labels = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
                // Update datasets accordingly
                datasets.forEach(function(dataset) {
                    datasets[0].data = [100, 200, 300, 400, 500, 600, 700];
                    dataset.hidden = false; // Make all datasets visible
                });
                break;

            case 'TOTAL':
                labels = ["January", "February", "March", "April", "May", "June", "July"];
                // Update datasets accordingly
                datasets.forEach(function(dataset) {
                    dataset.data = [50, 210, 630, 200, 100, 600, 700];
                    dataset.hidden = false; // Make all datasets visible
                });
                break;

            case 'LIVE':
                labels = ["Now", "+5min", "+10min", "+15min", "+20min", "+25min", "+30min"];
                // Update datasets accordingly
                datasets.forEach(function(dataset) {
                    dataset.data = [200, 500, 350, 400, 500, 100, 700];
                    dataset.hidden = false; // Make all datasets visible
                });
                break;

            default:
                labels = [];
        }
        lineChart.data.labels = labels;
        lineChart.update();
    }

   // Event listener for dropdown change
   document.getElementById("chartFilter").addEventListener("change", function () {
    var selectedValue = this.value;
    updateLabels(selectedValue);
});
});
