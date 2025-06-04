<?php
// payroll_deduction.php
session_start();
require_once 'functions.php';

$message = '';
$participants = read_json(PARTICIPANTS_DB);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $participant_id = $_POST['participant_id'];
    $year = (int)$_POST['year'];
    $month = (int)$_POST['month'];
    $deduction_amount = (float)$_POST['deduction_amount'];
    $payroll_staff_id = trim($_POST['payroll_staff_id']); // Assuming staff ID is manually entered

    if (!empty($participant_id) && $year > 2000 && $month >= 1 && $month <= 12 && $deduction_amount > 0 && !empty($payroll_staff_id)) {
        $deductions = read_json(DEDUCTIONS_DB);
        
        $new_deduction = [
            'deduction_id' => generate_unique_id('d_'),
            'participant_id' => $participant_id,
            'year' => $year,
            'month' => $month,
            'deduction_amount' => $deduction_amount,
            'payroll_staff_id' => $payroll_staff_id,
            'input_date' => date('Y-m-d H:i:s')
        ];
        
        $deductions[] = $new_deduction;
        write_json(DEDUCTIONS_DB, $deductions);
        
        $message = '<p style="color:green;">Data potongan gaji berhasil disimpan.</p>';
    } else {
        $message = '<p style="color:red;">Semua field wajib diisi dengan benar.</p>';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Input Potongan Gaji Qurban - Payroll</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Input Potongan Gaji Bulanan Peserta Qurban</h1>
        <p>Halaman ini untuk Bagian Penggajian UNP. [cite: 4]</p>
    </header>
    <nav><a href="index.php">🏠 Kembali ke Homepage</a></nav>
    <main>
        <?php echo $message; ?>
        <form action="payroll_deduction.php" method="POST">
            <p>
                <label for="participant_id">Pilih Peserta Qurban:</label><br>
                <select id="participant_id" name="participant_id" required>
                    <option value="">-- Pilih Peserta --</option>
                    <?php if (!empty($participants)): ?>
                        <?php foreach ($participants as $p): ?>
                            <option value="<?php echo htmlspecialchars($p['id']); ?>">
                                <?php echo htmlspecialchars($p['name']) . ' (' . htmlspecialchars($p['employee_id']) . ')'; ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="" disabled>Belum ada peserta terdaftar</option>
                    <?php endif; ?>
                </select>
            </p>
            <p>
                <label for="year">Tahun Potongan:</label><br>
                <input type="number" id="year" name="year" min="<?php echo date("Y")-1; ?>" max="<?php echo date("Y")+2; ?>" value="<?php echo date("Y"); ?>" required>
            </p>
            <p>
                <label for="month">Bulan Potongan:</label><br>
                <select id="month" name="month" required>
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?php echo $m; ?>" <?php if ($m == date('n')) echo 'selected'; ?>>
                            <?php echo date('F', mktime(0, 0, 0, $m, 10)); ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </p>
            <p>
                <label for="deduction_amount">Jumlah Potongan (Rp):</label><br>
                <input type="number" id="deduction_amount" name="deduction_amount" step="1000" min="0" required>
                <small>Ini adalah potongan per bulan untuk biaya qurban. [cite: 3]</small>
            </p>
             <p>
                <label for="payroll_staff_id">ID Staf Penggajian:</label><br>
                <input type="text" id="payroll_staff_id" name="payroll_staff_id" required>
            </p>
            <p>
                <button type="submit">Simpan Data Potongan</button>
            </p>
        </form>
    </main>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Pengantar Coding - Universitas Negeri Padang</p>
    </footer>
</body>
</html>