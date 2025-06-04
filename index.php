<?php
// index.php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berqurban Bersama UNP Peduli</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Selamat Datang di "Berqurban Bersama UNP Peduli"</h1>
        <p>Program qurban tahunan Universitas Negeri Padang untuk dosen dan karyawan.</p>
    </header>
    <nav>
        <ul>
            <li><a href="register.php">📝 Daftar Qurban</a></li>
            <li><a href="payroll_deduction.php">💰 Input Potongan Gaji (Payroll)</a></li>
            <li><a href="payroll_report.php">📊 Laporan Qurban 1447 H (Payroll)</a></li>
        </ul>
    </nav>
    <main>
        <p>Website ini bertujuan untuk memfasilitasi proses pendaftaran dan pengelolaan dana qurban bagi dosen dan karyawan UNP yang berniat untuk berqurban. [cite: 2, 3]</p>
        <p>Silakan pilih menu di atas untuk melanjutkan.</p>
    </main>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Pengantar Coding - Universitas Negeri Padang</p>
    </footer>
</body>
</html>