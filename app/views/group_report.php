<?php
// Tệp: app/views/group_report.php (ĐÃ SỬA LỖI KÝ TỰ VÔ HÌNH)
require 'app/views/layout/header.php'; 

// Controller cung cấp các biến:
// $group, $members, $taskProgressData, $contributionData
// (MỚI) Các biến filter hiện tại (để giữ giá trị trên form)
$current_user_id = $_GET['user_id'] ?? '';
$current_date_from = $_GET['date_from'] ?? '';
$current_date_to = $_GET['date_to'] ?? '';

// --- Chuẩn bị dữ liệu cho biểu đồ CỘT (Dùng $taskProgressData) ---
$task_status_labels = []; 
$task_status_counts = []; 
$task_status_colors = []; 

// (SỬA LỖI) Đảm bảo mảng này sạch
$status_map = [
    'backlog' => ['label' => 'Backlog', 'count' => $taskProgressData['backlog'] ?? 0, 'color' => '#858796'],
    'in_progress' => ['label' => 'In Progress', 'count' => $taskProgressData['in_progress'] ?? 0, 'color' => '#f6c23e'],
    'review' => ['label' => 'Review', 'count' => $taskProgressData['review'] ?? 0, 'color' => '#36b9cc'],
    'done' => ['label' => 'Done', 'count' => $taskProgressData['done'] ?? 0, 'color' => '#1cc88a']
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

<!-- (MỚI) FORM LỌC -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter"></i> Tùy chọn Lọc Báo cáo</h6>
    </div>
    <div class="card-body">
        <form action="index.php" method="GET">
            <input type="hidden" name="page" value="group_report">
            <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
            
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="user_id">Lọc theo thành viên:</label>
                    <select name="user_id" id="user_id" class="form-control">
                        <option value="">-- Tất cả thành viên --</option>
                        <?php foreach ($members as $member): ?>
                            <option value="<?php echo $member['user_id']; ?>" <?php echo ($member['user_id'] == $current_user_id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($member['username']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="date_from">Lọc từ ngày:</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" value="<?php echo htmlspecialchars($current_date_from); ?>">
                </div>
                <div class="form-group col-md-3">
                    <label for="date_to">Đến ngày:</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" value="<?php echo htmlspecialchars($current_date_to); ?>">
                </div>
                <div class="form-group col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mr-2">Lọc</button>
                    <a href="index.php?page=group_report&group_id=<?php echo $group['group_id']; ?>" class="btn btn-secondary">Xóa</a>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- KẾT THÚC FORM LỌC -->


<div class="row">
    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Tiến độ công việc (Kết quả lọc)</h6>
            </div>
            <div class="card-body">
                <div class="chart-area" style="height: 320px;">
                    <canvas id="taskStatusBarChart"></canvas>
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
                <h6 class="m-0 font-weight-bold text-primary">Bảng đóng góp (Kết quả lọc)</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>👤 Thành viên</th>
                                <th>📈 Tổng số Task</th>
                                <th>✅ Task Hoàn Thành</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($contributionData)): ?>
                                <tr><td colspan="3" class="text-center text-muted">Không có dữ liệu cho bộ lọc này</td></tr>
                            <?php else: ?>
                                <?php foreach ($contributionData as $member): ?>
                                    <tr>
                                        <td class="font-weight-bold"><?php echo htmlspecialchars($member['username']); ?></td>
                                        <td class="text-center">
                                            <?php echo $member['total_tasks'] ?? 0; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php 
                                            // (SỬA) Hiển thị số task hoàn thành
                                            echo $member['completed_tasks'] ?? 0;
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="note mt-3 p-3 bg-light rounded small">
                    <p class="mb-1">💡 <strong>Tổng số Task</strong>: Tổng số task được giao cho thành viên (khớp với bộ lọc).</p>
                    <p class="mb-0">✅ <strong>Task Hoàn Thành</strong>: Số task ở cột "Done" (khớp với bộ lọc).</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- (Code JS cho biểu đồ không đổi, nó sẽ tự động lấy dữ liệu mới) -->
<script src="public/vendor/chart.js/Chart.min.js"></script>
<script>
// Đảm bảo script chỉ chạy sau khi DOM đã tải
document.addEventListener("DOMContentLoaded", function() {
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
        if (s[0].length > 3) { s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep); }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
    }

    var ctx = document.getElementById("taskStatusBarChart");
    if (ctx) { // Chỉ chạy nếu có canvas
        var taskStatusBarChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo $js_task_status_labels; ?>, // Lấy từ $taskProgressData
                datasets: [{
                    label: "Số lượng Task",
                    backgroundColor: <?php echo $js_task_status_colors; ?>, // Lấy từ $taskProgressData
                    data: <?php echo $js_task_status_counts; ?>, // Lấy từ $taskProgressData
                }],
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    xAxes: [{ gridLines: { display: false }, maxBarThickness: 50 }],
                    yAxes: [{
                        ticks: { min: 0, maxTicksLimit: 5, padding: 10, callback: function(value) { if (Number.isInteger(value)) { return number_format(value); } } },
                        gridLines: { color: "rgb(234, 236, 244)", zeroLineColor: "rgb(234, 236, 244)", drawBorder: false, borderDash: [2], zeroLineBorderDash: [2] }
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
    }
});
</script>

<?php
// 2. Gọi Footer
require 'app/views/layout/footer.php'; 
?>