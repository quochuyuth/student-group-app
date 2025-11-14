<?php
// Tệp: app/views/dashboard.php (ĐÃ THÊM LINK VÀO CÁC THẺ)
require 'app/views/layout/header.php'; 

// LƯU Ý: Các biến $card1_title, $card1_value... và $pieChartData
// giờ đã được index.php (controller logic) cung cấp khi tải trang này.
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Bảng điều khiển</h1>
</div>

<div class="row">
    <!-- Thẻ 1 (ĐÃ THÊM LINK) -->
    <div class="col-xl-3 col-md-6 mb-4">
        <!-- (MỚI) Bọc thẻ <a> bên ngoài -->
        <a href="index.php?page=groups" class="text-decoration-none">
            <div class="card border-left-<?php echo $card1_color; ?> shadow h-100 py-2 card-hover">
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
        </a>
    </div>
    
    <!-- Thẻ 2 (ĐÃ THÊM LINK) -->
    <div class="col-xl-3 col-md-6 mb-4">
        <!-- (MỚI) Bọc thẻ <a> bên ngoài -->
        <a href="index.php?page=groups" class="text-decoration-none">
            <div class="card border-left-<?php echo $card2_color; ?> shadow h-100 py-2 card-hover">
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
        </a>
    </div>
    
    <!-- Thẻ 3 (ĐÃ THÊM LINK) -->
    <div class="col-xl-3 col-md-6 mb-4">
        <!-- (MỚI) Bọc thẻ <a> bên ngoài -->
        <a href="index.php?page=pending_tasks" class="text-decoration-none">
            <div class="card border-left-<?php echo $card3_color; ?> shadow h-100 py-2 card-hover">
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
        </a>
    </div>
    
    <!-- Thẻ 4 (ĐÃ THÊM LINK) -->
    <div class="col-xl-3 col-md-6 mb-4">
        <!-- (MỚI) Bọc thẻ <a> bên ngoài -->
        <a href="index.php?page=groups" class="text-decoration-none">
            <div class="card border-left-<?php echo $card4_color; ?> shadow h-100 py-2 card-hover">
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
        </a>
    </div>
</div> 

<div class="row">
    <!-- Biểu đồ Đường (Vẫn là Dữ liệu mẫu) -->
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

    <!-- Biểu đồ Tròn (Đã cập nhật Dữ liệu thật) -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Tình trạng Task (Của bạn)</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4">
                    <canvas id="myPieChart"></canvas>
                </div>
                <!-- (SỬA LỖI) BỔ SUNG CHÚ THÍCH "REVIEW" -->
                <div class="mt-4 text-center small">
                    <span class="mr-2"><i class="fas fa-circle text-secondary"></i> Backlog</span>
                    <span class="mr-2"><i class="fas fa-circle text-info"></i> In Progress</span>
                    <span class="mr-2"><i class="fas fa-circle text-warning"></i> Review</span>
                    <span class="mr-2"><i class="fas fa-circle text-success"></i> Done</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- (MỚI) Thêm CSS cho hiệu ứng Hover -->
<style>
.card-hover {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}
.card-hover:hover {
    transform: translateY(-5px); /* Hiệu ứng nhấc lên */
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important; /* Đổ bóng rõ hơn */
}
</style>

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

    // 1. BIỂU ĐỒ ĐƯỜNG (Area Chart) - Dữ liệu Mẫu (Không đổi)
    var ctxArea = document.getElementById("myAreaChart");
    var myLineChart = new Chart(ctxArea, {
      type: 'line',
      data: {
        labels: ["Ngày 1", "Ngày 2", "Ngày 3", "Ngày 4", "Ngày 5", "Ngày 6", "Ngày 7"],
        datasets: [{
          label: "Số Task Mới",
          lineTension: 0.3,
          backgroundColor: "rgba(95, 77, 238, 0.05)",
          borderColor: "#5f4dee",
          pointRadius: 3,
          pointBackgroundColor: "#5f4dee",
          pointBorderColor: "#5f4dee",
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


    // 2. BIỂU ĐỒ TRÒN (Pie Chart / Doughnut) - (SỬA LỖI DỮ LIỆU)
    var ctxPie = document.getElementById("myPieChart");
    var myPieChart = new Chart(ctxPie, {
      type: 'doughnut',
      data: {
        // (SỬA) Lấy dữ liệu từ PHP
        labels: ["Backlog", "In Progress", "Review", "Done"],
        datasets: [{
          // (SỬA) Dùng biến $pieChartData từ index.php
          data: [
            <?php echo $pieChartData['backlog'] ?? 0; ?>, 
            <?php echo $pieChartData['in_progress'] ?? 0; ?>,
            <?php echo $pieChartData['review'] ?? 0; ?>,
            <?php echo $pieChartData['done'] ?? 0; ?>
          ], 
          // (SỬA) Thêm màu cho Review
          backgroundColor: ['#858796', '#36b9cc', '#f6c23e', '#1cc88a'], 
          hoverBackgroundColor: ['#6c6e7e', '#2c9faf', '#dda20a', '#17a673'],
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