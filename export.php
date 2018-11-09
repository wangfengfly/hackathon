<?php
/**
 * Author: wangfeng
 * Date: 2018/11/9
 * Time: 15:19
 */

$dbh = new PDO('mysql:host=10.139.22.181;dbname=hackathon', 'dhuser', 'dhdev123');
$dbh->exec('set names utf8');
$sql = 'select id,url,title,content from articles where id>1979';

$filename = '/tmp/articles.json';
@unlink($filename);

foreach($dbh->query($sql) as $row){
    $r = ['id'=>$row['id'], 'url'=>$row['url'], 'title'=>$row['title'], 'content'=>$row['content']];
    $r = json_encode($r, JSON_UNESCAPED_UNICODE).PHP_EOL;
    file_put_contents($filename, $r, FILE_APPEND);
}
