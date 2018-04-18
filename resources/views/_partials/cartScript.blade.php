<script type="text/javascript">
// Chart.js scripts
// -- Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#292b2c';
// -- Area Chart Example
var ctx = document.getElementById("myAreaChart");
var myLineChart = new Chart(ctx, {
type: 'line',
data: {
labels: <?php echo $dates ?>,
datasets: [{
label: "Sales",
lineTension: 0.3,
backgroundColor: "rgba(2,117,216,0.2)",
borderColor: "rgba(2,117,216,1)",
pointRadius: 5,
pointBackgroundColor: "rgba(2,117,216,1)",
pointBorderColor: "rgba(255,255,255,0.8)",
pointHoverRadius: 5,
pointHoverBackgroundColor: "rgba(2,117,216,1)",
pointHitRadius: 20,
pointBorderWidth: 2,
data: <? echo $sales ?>,
}],
},
options: {
scales: {
xAxes: [{
time: {
unit: 'date'
},
gridLines: {
display: false
},
ticks: {
maxTicksLimit: 15
}
}],
yAxes: [{
ticks: {
min: 0,
max: 500000,
maxTicksLimit: 10
},
gridLines: {
color: "rgba(0, 0, 0, .125)",
}
}],
},
legend: {
display: false
}
}
});
// -- Bar Chart Example
var ctx = document.getElementById("myBarChart");
var myLineChart = new Chart(ctx, {
type: 'bar',
data: {
labels: <? echo $branches ?>,
datasets: [{
label: "Sales",
backgroundColor: "rgba(2,117,216,1)",
borderColor: "rgba(2,117,216,1)",
data: <? echo $branchSales ?>,
}],
},
options: {
scales: {
xAxes: [{
time: {
unit: 'month'
},
gridLines: {
display: false
},
ticks: {
maxTicksLimit: 6
}
}],
yAxes: [{
ticks: {
min: 0,
max: 3000000,
maxTicksLimit: 5
},
gridLines: {
display: true
}
}],
},
legend: {
display: false
}
}
});
// -- Pie Chart Example
var ctx = document.getElementById("myPieChart");
var myPieChart = new Chart(ctx, {
type: 'pie',
data: {
labels: <? echo $branches ?>,
datasets: [{
data: <? echo $branchSales2 ?>,
backgroundColor: ['#007bff', '#dc3545', '#ffc107', '#28a745'],
}],
},
});
</script>