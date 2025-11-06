<?php
// app/controllers/ReportController.php

require_once 'app/models/Report.php';
require_once 'app/models/Group.php'; // Cần để lấy thông tin nhóm

class ReportController {
    private $db;
    private $reportModel;
    private $groupModel;

    public function __construct($db) {
        $this->db = $db;
        $this->reportModel = new Report($this->db);
        $this->groupModel = new Group($this->db);
    }

    /**
     * Hiển thị trang Báo cáo
     */
    public function show() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }

        $group_id = (int)$_GET['group_id'];
        
        // 1. Lấy thông tin nhóm
        $group = $this->groupModel->getGroupById($group_id);
        if (!$group) {
            header('Location: index.php?page=groups'); exit;
        }

        // 2. Lấy dữ liệu tiến độ Task
        $taskProgressData = $this->reportModel->getTaskProgress($group_id);
        
        // 3. Lấy dữ liệu điểm đóng góp
        $contributionData = $this->reportModel->getContributionScores($group_id);

        // 4. Tạo URL cho biểu đồ
        $chartUrl = $this->generateProgressChartUrl($taskProgressData);
        
        // Tải view và truyền dữ liệu
        require 'app/views/group_report.php';
    }

    /**
     * Hàm trợ giúp: Tạo URL biểu đồ từ QuickChart.io
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
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    title: { display: true, text: 'Tiến độ Công việc' }
                }
            }
        }";
        
        // Mã hóa URL để gửi đi
        return "https://quickchart.io/chart?c=" . urlencode($chartConfig);
    }
}
?>