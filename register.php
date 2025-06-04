<?php
// register.php
session_start();
require_once 'functions.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $employee_id = trim($_POST['employee_id']);
    $department = trim($_POST['department']);
    $contact_number = trim($_POST['contact_number']);
    $qurban_type = trim($_POST['qurban_type']);
    $willing_deduction = isset($_POST['willing_deduction']);

    if (!empty($name) && !empty($employee_id) && !empty($department) && !empty($qurban_type) && $willing_deduction) {
        $participants = read_json(PARTICIPANTS_DB);
        
        $new_participant = [
            'id' => generate_unique_id('p_'),
            'name' => $name,
            'employee_id' => $employee_id,
            'department' => $department,
            'contact_number' => $contact_number,
            'qurban_type' => $qurban_type,
            'registration_date' => date('Y-m-d H:i:s'),
            'willing_deduction' => true
        ];
        
        $participants[] = $new_participant;
        write_json(PARTICIPANTS_DB, $participants);
        
        $message = '<p style="color:green;">Pendaftaran berhasil! Terima kasih telah mendaftar.</p>';
    } else {
        $message = '<p style="color:red;">Semua field wajib diisi dan Anda harus menyetujui pemotongan gaji.</p>';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pendaftaran Peserta Qurban - UNP Peduli</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Formulir Pendaftaran Peserta Qurban</h1>
        <p>Silakan isi data diri Anda untuk mengikuti program "Berqurban Bersama UNP Peduli". [cite: 3]</p>
    </header>
    <nav><a href="index.php">🏠 Kembali ke Homepage</a></nav>
    <main>
        <?php echo $message; ?>
        <form action="register.php" method="POST">
            <p>
                <label for="name">Nama Lengkap:</label><br>
                <input type="text" id="name" name="name" required>
            </p>
            <p>
                <label for="employee_id">NIP / ID Karyawan:</label><br>
                <input type="text" id="employee_id" name="employee_id" required>
            </p>
            <p>
                <label for="department">Unit Kerja/Fakultas/Departemen:</label><br>
                <input type="text" id="department" name="department" required>
            </p>
            <p>
                <label for="contact_number">Nomor HP (WhatsApp Aktif):</label><br>
                <input type="tel" id="contact_number" name="contact_number" required>
            </p>
            <p>
                <label for="qurban_type">Jenis Qurban:</label><br>
                <select id="qurban_type" name="qurban_type" required>
                    <option value="">-- Pilih Jenis Qurban --</option>
                    <option value="1 Kambing">1 Kambing</option>
                    <option value="1/7 Sapi">1/7 Sapi</option>
                    <option value="1 Sapi">1 Sapi (Kolektif atas nama sendiri)</option>
                </select>
            </p>
            <p>
                <input type="checkbox" id="willing_deduction" name="willing_deduction" required>
                <label for="willing_deduction">Saya bersedia gaji saya dipotong setiap bulan selama 1 tahun ke depan untuk biaya qurban. [cite: 3]</label>
            </p>
            <p>
                <button type="submit">Daftar Qurban</button>
            </p>
        </form>
    </main>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Pengantar Coding - Universitas Negeri Padang</p>
    </footer>
</body>
</html>