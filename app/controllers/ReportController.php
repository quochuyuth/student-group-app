<?php
// app/controllers/ReportController.php (ĐÃ SỬA LỖI KÝ TỰ VÔ HÌNH)

require_once 'app/models/Report.php';
require_once 'app/models/Group.php'; 

class ReportController {
    // --- ĐÃ SỬA LỖI THỤT ĐẦU DÒNG Ở ĐÂY ---
    private $db;
    private $reportModel;
    private $groupModel;

    public function __construct($db) {
        $this->db = $db;
        $this->reportModel = new Report($this->db);
        $this->groupModel = new Group($this->db); 
    }

    /**
     * (SỬA ĐỔI) Hiển thị trang Báo cáo (Thêm logic Filter)
     */
    public function show() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }

        $group_id = (int)$_GET['group_id'];
        
        // (MỚI) Lấy các tham số filter
        $filter_user_id = $_GET['user_id'] ?? null;
        $filter_date_from = $_GET['date_from'] ?? null;
        $filter_date_to = $_GET['date_to'] ?? null;

        // 1. Lấy thông tin nhóm
        $group = $this->groupModel->getGroupById($group_id);
        if (!$group) {
            header('Location: index.php?page=groups'); exit;
        }
        
        // (MỚI) Lấy danh sách thành viên để lọc
        $members = $this->groupModel->getMembersByGroupId($group_id);

        // 2. Lấy dữ liệu tiến độ Task (đã lọc)
        $taskProgressData = $this->reportModel->getTaskProgress(
            $group_id, $filter_user_id, $filter_date_from, $filter_date_to
        );
        
        // 3. Lấy dữ liệu điểm đóng góp (đã lọc)
        // (Đã cập nhật logic để lấy total_tasks và completed_tasks)
        $contributionData = $this->reportModel->getContributionScores(
            $group_id, $filter_user_id, $filter_date_from, $filter_date_to
        );

        // 4. Tạo URL cho biểu đồ (QuickChart không còn dùng nữa, 
        //    nhưng chúng ta giữ lại logic tạo chart bên view)
        
        // Tải view và truyền dữ liệu
        require 'app/views/group_report.php';
    }

    /**
     * Hàm trợ giúp: Tạo URL biểu đồ từ QuickChart.io
     * (Hàm này không còn được dùng ở view mới, nhưng giữ lại)
     */
    private function generateProgressChartUrl($data) {
        $labels = "'Backlog', 'In Progress', 'Review', 'Done'";
        $values = implode(',', [
            $data['backlog'],
            $data['in_progress'],
            $data['review'],
            $data['done']
        ]);

        $chartConfig = "{
            type: 'doughnut',
            data: {
                labels: [{$labels}],
                datasets: [{
                    data: [{$values}],
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']
                }]
            },
            options: {
                responsive: true
            }
        }";
        
        // Mã hóa URL để gửi đi
        return "https://quickchart.io/chart?c=" . urlencode($chartConfig);
    }
}
?>