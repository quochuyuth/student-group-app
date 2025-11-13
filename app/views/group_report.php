<?php
// Tệp: app/views/group_report.php (Bản SỬA LỖI BIẾN)
require 'app/views/layout/header.php'; 

// CONTROLLER CỦA BẠN CUNG CẤP CÁC BIẾN NÀY:
// $group
// $taskProgressData (array: ['backlog' => 0, 'in_progress' => 1, ...])
// $contributionData (array of arrays: [ ['username' => 'tuong', 'total_task_points' => 13.8, 'avg_rubric_score' => 4.0], ... ])

// --- Chuẩn bị dữ liệu cho biểu đồ CỘT (Dùng $taskProgressData) ---
$task_status_labels = []; // Tên các trạng thái
$task_status_counts = []; // Số lượng task
$task_status_colors = []; // Màu sắc

// Sắp xếp lại dữ liệu từ $taskProgressData
$status_map = [
    'backlog' => ['label' => 'Backlog', 'count' => $taskProgressData['backlog'] ?? 0, 'color' => '#858796'], // Grey
    'in_progress' => ['label' => 'In Progress', 'count' => $taskProgressData['in_progress'] ?? 0, 'color' => '#f6c23e'], // Yellow
    'review' => ['label' => 'Review', 'count' => $taskProgressData['review'] ?? 0, 'color' => '#36b9cc'], // Blue (teal)
    'done' => ['label' => 'Done', 'count' => $taskProgressData['done'] ?? 0, 'color' => '#1cc88a'] // Green
];

foreach ($status_map as $data) {
    $task_status_labels[] = $data['label'];
    $task_status_counts[] = $data['count'];
    $task_status_colors[] = $data['color'];
}

// Chuyển PHP array sang JSON để dùng trong JavaScript
$js_task_status_labels = json_encode($task_status_labels);
$js_task_status_counts = json_encode($task_status_counts);
$js_task_status_colors = json_encode($task_status_colors);
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">📊 Báo cáo Nhóm: <?php echo htmlspecialchars($group['group_name']); ?></h1>
    <a href="index.php?page=group_details&id=<?php echo $group['group_id']; ?>" class="btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay lại nhóm
    </a>
</div>

<div class="row">

    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Tiến độ công việc</h6>
            </div>
            <div class="card-body">
                <div class="chart-area" style="height: 320px;"> <canvas id="taskStatusBarChart"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    <?php foreach ($status_map as $data): ?>
                        <span class="mr-2"><i class="fas fa-circle" style="color:<?php echo $data['color']; ?>;"></i> <?php echo $data['label']; ?> (<?php echo $data['count']; ?>)</span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Bảng điểm Đóng góp</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>👤 Thành viên</th>
                                <th>📈 Điểm Task</th>
                                <th>⭐ Điểm ĐG</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($contributionData)): ?>
                                <tr><td colspan="3" class="text-center text-muted">Chưa có dữ liệu</td></tr>
                            <?php else: ?>
                                <?php foreach ($contributionData as $member): ?>
                                    <tr>
                                        <td class="font-weight-bold"><?php echo htmlspecialchars($member['username']); ?></td>
                                        <td class="text-center">
                                            <?php echo number_format($member['total_task_points'] ?? 0, 1); ?>
                                        </td>
                                        <td class="text-center">
                                            <?php 
                                            // Controller của bạn đã gộp điểm Rubric vào đây
                                            if (!empty($member['avg_rubric_score'])) {
                                                $score = round($member['avg_rubric_score'], 2);
                                                echo '<span class="text-warning"><i class="fas fa-star"></i></span> ' . $score;
                                            } else {
                                                echo '<em class="text-muted small">N/A</em>';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="note mt-3 p-3 bg-light rounded small">
                    <p class="mb-1">💡 <strong>Điểm Task</strong>: Tính theo % hoàn thành task (Done 100%, Review 60%...).</p>
                    <p class="mb-0">⭐ <strong>Điểm ĐG</strong>: Điểm trung bình Rubric (thang 4).</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="http://localhost/StudentGroupApp/public/vendor/chart.js/Chart.min.js"></script>
<script>
// Đoạn code JS này không đổi, nó sẽ lấy dữ liệu PHP ở trên để vẽ
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

var ctx = document.getElementById("taskStatusBarChart");
var taskStatusBarChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: <?php echo $js_task_status_labels; ?>, // Lấy từ $taskProgressData
    datasets: [{
      label: "Số lượng Task",
      backgroundColor: <?php echo $js_task_status_colors; ?>, // Lấy từ $taskProgressData
      hoverBackgroundColor: <?php echo $js_task_status_colors; ?>,
      borderColor: "#4e73df", // Màu này không quan trọng lắm ở Bar chart
      data: <?php echo $js_task_status_counts; ?>, // Lấy từ $taskProgressData
    }],
  },
  options: {
    maintainAspectRatio: false,
    layout: { padding: { left: 10, right: 25, top: 25, bottom: 0 } },
    scales: {
      xAxes: [{
        gridLines: { display: false, drawBorder: false },
        ticks: { maxTicksLimit: 6 },
        maxBarThickness: 50,
      }],
      yAxes: [{
        ticks: {
          min: 0, 
          maxTicksLimit: 5,
          padding: 10,
          callback: function(value) { if (Number.isInteger(value)) { return number_format(value); } } // Chỉ hiện số nguyên
        },
        gridLines: {
          color: "rgb(234, 236, 244)",
          zeroLineColor: "rgb(234, 236, 244)",
          drawBorder: false,
          borderDash: [2],
          zeroLineBorderDash: [2]
        }
      }],
    },
    legend: { display: false },
    tooltips: {
      callbacks: {
        label: function(tooltipItem, chart) {
          var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
          return datasetLabel + ': ' + number_format(tooltipItem.yLabel) + ' Task';
        }
      }
    },
  }
});
</script>

<?php
// 2. Gọi Footer
require 'app/views/layout/footer.php'; 
?>