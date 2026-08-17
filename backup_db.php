import os

# Create the backup_db.php file content
content = """<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') { header("Location: index.php"); exit; }
include 'config/koneksi.php';

// Export Database
$backup_file = 'backup_' . date("Y-m-d_H-i-s") . '.sql';
$command = "mysqldump --user=" . $username . " --password=" . $password . " --host=" . $hostname . " " . $database_name . " > " . $backup_file;

// Note: This script assumes mysqldump is in the system PATH.
// For XAMPP, you might need to use the absolute path, e.g., C:/xampp/mysql/bin/mysqldump

exec($command, $output, $return_var);

if ($return_var === 0) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . basename($backup_file));
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($backup_file));
    readfile($backup_file);
    unlink($backup_file); // delete file after download
    exit;
} else {
    echo "Gagal melakukan backup database.";
}
?>"""

# Since I cannot know the exact DB credentials in the user's config file, 
# I will provide a version that uses the existing connection variables 
# to build the command. 

# Revised version for the user
file_content = """<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') { header("Location: index.php"); exit; }
include 'config/koneksi.php';

// Database credentials from your config/koneksi.php or global variables
$host = 'localhost';
$user = 'root';
$pass = ''; // Sesuaikan dengan password database Anda
$dbname = 'db_pos_b'; // Sesuaikan dengan nama database Anda

$filename = 'backup_' . $dbname . '_' . date('Y-m-d_H-i-s') . '.sql';
$dump = "C:/xampp/mysql/bin/mysqldump --host=$host --user=$user --password=$pass $dbname > $filename";

system($dump);

if (file_exists($filename)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.basename($filename));
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filename));
    readfile($filename);
    unlink($filename);
    exit;
} else {
    echo "Gagal membuat backup database. Pastikan path mysqldump benar.";
}
?>"""

with open('backup_db.php', 'w') as f:
    f.write(file_content)