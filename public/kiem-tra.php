<?php
/**
 * File chan doan tam thoi - XOA NGAY SAU KHI DUNG XONG.
 *
 * Mo bang: https://waco.anhminhmedia.vn/kiem-tra.php?ma=waco2026
 */

if (($_GET['ma'] ?? '') !== 'waco2026') {
    http_response_code(404);
    exit;
}

header('Content-Type: text/plain; charset=UTF-8');

$goc = dirname(__DIR__);

function ket($dieuKien, $dat = 'OK', $hong = 'HONG')
{
    return $dieuKien ? $dat : $hong;
}

echo "== MOI TRUONG ==\n";
echo "PHP           : " . PHP_VERSION . "  (can >= 8.1)\n";
echo "Thu muc goc   : $goc\n\n";

echo "== FILE BAT BUOC ==\n";
$autoload = $goc . '/vendor/autoload.php';
echo "vendor/autoload.php : " . ket(is_file($autoload), 'CO', 'THIEU  <-- chua chay composer install') . "\n";

$env = $goc . '/.env';
echo ".env                : " . ket(is_file($env), 'CO', 'THIEU  <-- chua tao .env') . "\n";

if (is_file($env)) {
    $noiDung = file_get_contents($env);
    preg_match('/^APP_KEY=(.*)$/m', $noiDung, $k);
    $key = trim($k[1] ?? '');
    echo "APP_KEY             : " . ket($key !== '', 'DA DAT', 'TRONG  <-- chay php artisan key:generate') . "\n";

    preg_match('/^APP_URL=(.*)$/m', $noiDung, $u);
    $url = trim($u[1] ?? '');
    echo "APP_URL             : " . ($url === '' ? 'TRONG' : $url);
    if ($url !== '' && substr($url, -1) !== '/') {
        echo "   <-- THIEU DAU / O CUOI";
    }
    echo "\n";
}

echo "\n== QUYEN GHI ==\n";
foreach (['storage', 'storage/logs', 'storage/framework/views', 'storage/framework/cache', 'storage/framework/sessions', 'bootstrap/cache'] as $d) {
    $p = $goc . '/' . $d;
    if (!is_dir($p)) {
        printf("%-32s %s\n", $d, 'THIEU THU MUC');
        continue;
    }
    printf("%-32s %s\n", $d, ket(is_writable($p), 'ghi duoc', 'KHONG GHI DUOC'));
}

echo "\n== PHAN MO RONG PHP ==\n";
foreach (['pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'fileinfo', 'curl', 'gd'] as $e) {
    printf("%-12s %s\n", $e, ket(extension_loaded($e), 'co', 'THIEU'));
}

echo "\n== THU KHOI DONG LARAVEL ==\n";
if (!is_file($autoload)) {
    echo "Bo qua - khong co vendor/autoload.php\n";
    exit;
}

try {
    require $autoload;
    $app = require_once $goc . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle(Illuminate\Http\Request::create('/', 'GET'));
    echo "Khoi dong OK - ma tra ve: " . $response->getStatusCode() . "\n";
} catch (Throwable $e) {
    echo "LOI THAT SU:\n";
    echo get_class($e) . "\n";
    echo $e->getMessage() . "\n";
    echo "tai " . $e->getFile() . ":" . $e->getLine() . "\n\n";
    echo "5 dong dau cua vet goi:\n";
    foreach (array_slice(explode("\n", $e->getTraceAsString()), 0, 5) as $dong) {
        echo "  $dong\n";
    }
}
