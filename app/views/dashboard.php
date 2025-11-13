<?php
// Tệp: app/views/dashboard.php (Bản NÂNG CẤP "Tuyệt hơn")
require 'app/views/layout/header.php'; 

// LƯU Ý: Hiện tại, các thẻ và biểu đồ đang dùng DỮ LIỆU MẪU (DEMO DATA).
// Sau khi layout này chạy, chúng ta sẽ kết nối nó với CSDL của bạn.

// Dữ liệu mẫu cho 4 thẻ
$card1_title = "Tổng số Nhóm";
$card1_value = "4"; // (Dữ liệu mẫu)
$card1_icon = "fa-layer-group";
$card1_color = "primary";

$card2_title = "Tổng số Task";
$card2_value = "25"; // (Dữ liệu mẫu)
$card2_icon = "fa-tasks";
$card2_color = "success";

$card3_title = "Task Cần Làm";
$card3_value = "12"; // (Dữ liệu mẫu)
$card3_icon = "fa-clipboard-list";
$card3_color = "info";

$card4_title = "Lời mời chờ";
$card4_value = "2"; // (Dữ liệu mẫu)
$card4_icon = "fa-envelope";
$card4_color = "warning";
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Bảng điều khiển</h1>
</div>

<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-<?php echo $card1_color; ?> shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-<?php echo $card1_color; ?> text-uppercase mb-1">
                            <?php echo $card1_title; ?></div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $card1_value; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas <?php echo $card1_icon; ?> fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-<?php echo $card2_color; ?> shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-<?php echo $card2_color; ?> text-uppercase mb-1">
                            <?php echo $card2_title; ?></div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $card2_value; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas <?php echo $card2_icon; ?> fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-<?php echo $card3_color; ?> shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-<?php echo $card3_color; ?> text-uppercase mb-1">
                            <?php echo $card3_title; ?></div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $card3_value; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas <?php echo $card3_icon; ?> fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-<?php echo $card4_color; ?> shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-<?php echo $card4_color; ?> text-uppercase mb-1">
                            <?php echo $card4_title; ?></div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $card4_value; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas <?php echo $card4_icon; ?> fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> <div class="row">
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Hoạt động (7 ngày qua)</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="myAreaChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Tình trạng Task</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4">
                    <canvas id="myPieChart"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    <span class="mr-2"><i class="fas fa-circle text-secondary"></i> Backlog</span>
                    <span class="mr-2"><i class="fas fa-circle text-info"></i> In Progress</span>
                    <span class="mr-2"><i class="fas fa-circle text-success"></i> Done</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Chờ 1 chút để Chart.js được tải xong (từ footer.php)
document.addEventListener("DOMContentLoaded", function() {

    // Set font mặc định cho Chart.js
    Chart.defaults.global.defaultFontFamily = 'Poppins', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#858796';

    function number_format(number, decimals, dec_point, thousands_sep) {
      number = (number + '').replace(',', '').replace(' ', '');
      var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function(n, prec) {
          var k = Math.pow(10, prec);
          return '' + Math.round(n * k) / k;
        };
      s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
      if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
      }
      if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
      }
      return s.join(dec);
    }

    // 1. BIỂU ĐỒ ĐƯỜNG (Area Chart) - Dữ liệu Mẫu
    var ctxArea = document.getElementById("myAreaChart");
    var myLineChart = new Chart(ctxArea, {
      type: 'line',
      data: {
        // Dữ liệu mẫu (7 ngày)
        labels: ["Ngày 1", "Ngày 2", "Ngày 3", "Ngày 4", "Ngày 5", "Ngày 6", "Ngày 7"],
        datasets: [{
          label: "Số Task Mới",
          lineTension: 0.3,
          backgroundColor: "rgba(95, 77, 238, 0.05)", // Màu tím nhạt
          borderColor: "#5f4dee", // Màu tím
          pointRadius: 3,
          pointBackgroundColor: "#5f4dee",
          pointBorderColor: "#5f4dee",
          pointHoverRadius: 3,
          pointHoverBackgroundColor: "#5f4dee",
          pointHoverBorderColor: "#5f4dee",
          pointHitRadius: 10,
          pointBorderWidth: 2,
          data: [0, 2, 1, 3, 2, 5, 4], // Dữ liệu mẫu
        }],
      },
      options: {
        maintainAspectRatio: false,
        layout: { padding: { left: 10, right: 25, top: 25, bottom: 0 } },
        scales: {
          xAxes: [{ gridLines: { display: false, drawBorder: false }, ticks: { maxTicksLimit: 7 } }],
          yAxes: [{
            ticks: { maxTicksLimit: 5, padding: 10, callback: function(value) { return number_format(value); } },
            gridLines: { color: "rgb(234, 236, 244)", zeroLineColor: "rgb(234, 236, 244)", drawBorder: false, borderDash: [2], zeroLineBorderDash: [2] }
          }],
        },
        legend: { display: false },
        tooltips: {
          callbacks: {
            label: function(tooltipItem, chart) {
              var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
              return datasetLabel + ': ' + number_format(tooltipItem.yLabel);
            }
          }
        }
      }
    });


    // 2. BIỂU ĐỒ TRÒN (Pie Chart / Doughnut) - Dữ liệu Mẫu
    var ctxPie = document.getElementById("myPieChart");
    var myPieChart = new Chart(ctxPie, {
      type: 'doughnut',
      data: {
        // Dữ liệu mẫu (Tình trạng Task)
        labels: ["Backlog", "In Progress", "Done"],
        datasets: [{
          data: [5, 3, 10], // Dữ liệu mẫu
          backgroundColor: ['#858796', '#36b9cc', '#1cc88a'],
          hoverBackgroundColor: ['#6c6e7e', '#2c9faf', '#17a673'],
          hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
      },
      options: {
        maintainAspectRatio: false,
        tooltips: {
          backgroundColor: "rgb(255,255,255)",
          bodyFontColor: "#858796",
          borderColor: '#dddfeb',
          borderWidth: 1,
          xPadding: 15,
          yPadding: 15,
          displayColors: false,
          caretPadding: 10,
        },
        legend: { display: false },
        cutoutPercentage: 80,
      },
    });

});
</script>


<?php
// 2. Gọi Footer
require 'app/views/layout/footer.php'; 
?>