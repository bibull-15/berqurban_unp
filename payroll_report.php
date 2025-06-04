<?php
// payroll_report.php
session_start();
require_once 'functions.php';

$message = '';
$participants = read_json(PARTICIPANTS_DB);
$deductions_data = read_json(DEDUCTIONS_DB);
$existing_report = read_json(REPORT_DB);

// Calculate total deductions per participant from deductions.json for reference
$calculated_totals = [];
if (!empty($deductions_data)) {
    foreach ($deductions_data as $deduction) {
        $p_id = $deduction['participant_id'];
        if (!isset($calculated_totals[$p_id])) {
            $calculated_totals[$p_id] = 0;
        }
        // Assuming deductions are for Qurban 1447H (collected over the preceding year)
        // This simple sum might need refinement based on specific qurban year logic
        $calculated_totals[$p_id] += $deduction['deduction_amount'];
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_report'])) {
    $final_participant_reports = [];
    $overall_total_input = (float)$_POST['overall_total_deductions_1447H'];
    $report_payroll_staff_id = trim($_POST['report_payroll_staff_id']);

    foreach ($participants as $participant) {
        $p_id = $participant['id'];
        if (isset($_POST['final_cost'][$p_id])) {
            $final_cost = (float)$_POST['final_cost'][$p_id];
            $final_participant_reports[] = [
                'participant_id' => $p_id,
                'name' => $participant['name'], // Denormalized for easy report display
                'employee_id' => $participant['employee_id'], // Denormalized
                'final_individual_deduction_cost_1447H' => $final_cost
            ];
        }
    }

    if (!empty($final_participant_reports) && $overall_total_input > 0 && !empty($report_payroll_staff_id)) {
        $report_data_to_save = [
            'report_generated_date' => date('Y-m-d H:i:s'),
            'payroll_staff_id_reporter' => $report_payroll_staff_id,
            'participants_report' => $final_participant_reports,
            'overall_total_deductions_1447H' => $overall_total_input
        ];
        
        write_json(REPORT_DB, $report_data_to_save);
        $existing_report = $report_data_to_save; // Update current view
        $message = '<p style="color:green;">Laporan final Qurban 1447 H berhasil disimpan.</p>';
    } else {
        $message = '<p style="color:red;">Pastikan semua data final dan ID staf pelapor diisi dengan benar.</p>';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Peserta Qurban 1447 H - Payroll</title>
    <link rel="stylesheet" href="style.css">
    <style>
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <header>
        <h1>Laporan Peserta Qurban untuk Lebaran Haji 1447 H</h1>
        <p>Halaman ini untuk Bagian Penggajian UNP untuk menginput dan melihat laporan final. [cite: 4]</p>
    </header>
    <nav><a href="index.php">🏠 Kembali ke Homepage</a></nav>
    <main>
        <?php echo $message; ?>

        <h2>Input Data Final Laporan Qurban 1447 H</h2>
        <p>Mohon input biaya potongan final per anggota dan total potongan keseluruhan untuk qurban 1447 H.</p>
        <form action="payroll_report.php" method="POST">
            <table>
                <thead>
                    <tr>
                        <th>Nama Peserta</th>
                        <th>NIP/ID Karyawan</th>
                        <th>Total Potongan Terkumpul (Referensi)</th>
                        <th>Input Biaya Potongan Anggota Final 1447 H (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($participants)): ?>
                        <?php foreach ($participants as $p): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($p['name']); ?></td>
                                <td><?php echo htmlspecialchars($p['employee_id']); ?></td>
                                <td>Rp <?php echo number_format($calculated_totals[$p['id']] ?? 0, 0, ',', '.'); ?></td>
                                <td>
                                    <input type="number" name="final_cost[<?php echo htmlspecialchars($p['id']); ?>]" step="1000" min="0" 
                                           value="<?php 
                                                    // Pre-fill if report already exists
                                                    if (!empty($existing_report['participants_report'])) {
                                                        foreach ($existing_report['participants_report'] as $pr) {
                                                            if ($pr['participant_id'] == $p['id']) {
                                                                echo htmlspecialchars($pr['final_individual_deduction_cost_1447H']);
                                                                break;
                                                            }
                                                        }
                                                    }
                                                  ?>" 
                                           required>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4">Belum ada peserta terdaftar.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <p>
                <label for="overall_total_deductions_1447H">Input Total Potongan Keseluruhan Final 1447 H (Rp):</label><br>
                <input type="number" id="overall_total_deductions_1447H" name="overall_total_deductions_1447H" step="1000" min="0" 
                       value="<?php echo htmlspecialchars($existing_report['overall_total_deductions_1447H'] ?? ''); ?>" 
                       required>
            </p>
            <p>
                <label for="report_payroll_staff_id">ID Staf Penggajian (Pelapor):</label><br>
                <input type="text" id="report_payroll_staff_id" name="report_payroll_staff_id" 
                       value="<?php echo htmlspecialchars($existing_report['payroll_staff_id_reporter'] ?? ''); ?>" 
                       required>
            </p>
            <p>
                <button type="submit" name="save_report">Simpan Laporan Final 1447 H</button>
            </p>
        </form>

        <hr>

        <h2>Data Laporan Final Qurban 1447 H (Tersimpan)</h2>
        <?php if (!empty($existing_report) && isset($existing_report['participants_report'])): ?>
            <p><strong>Tanggal Laporan Dibuat/Disimpan:</strong> <?php echo htmlspecialchars($existing_report['report_generated_date']); ?></p>
            <p><strong>Dilaporkan oleh Staf Penggajian ID:</strong> <?php echo htmlspecialchars($existing_report['payroll_staff_id_reporter']); ?></p>
            <table>
                <thead>
                    <tr>
                        <th>Nama Peserta</th>
                        <th>NIP/ID Karyawan</th>
                        <th>Biaya Potongan Anggota Final 1447 H</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($existing_report['participants_report'] as $report_item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($report_item['name']); ?></td>
                            <td><?php echo htmlspecialchars($report_item['employee_id']); ?></td>
                            <td>Rp <?php echo number_format($report_item['final_individual_deduction_cost_1447H'], 0, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2" style="text-align:right;">Total Potongan Keseluruhan Final 1447 H:</th>
                        <th>Rp <?php echo number_format($existing_report['overall_total_deductions_1447H'], 0, ',', '.'); ?></th>
                    </tr>
                </tfoot>
            </table>
        <?php else: ?>
            <p>Belum ada data laporan final yang disimpan untuk Qurban 1447 H.</p>
        <?php endif; ?>
    </main>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Pengantar Coding - Universitas Negeri Padang</p>
    </footer>
</body>
</html>