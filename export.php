<?php
/**
 * Author: wangfeng
 * Date: 2018/11/9
 * Time: 15:19
 */

$dbh = new PDO('mysql:host=10.139.22.181;dbname=hackathon', 'dhuser', 'dhdev123');
$dbh->exec('set names utf8');
$sql = 'select title,content from articles where id>1979';

$data = [];
foreach($dbh->query($sql) as $row){
    $data[] = ['title'=>$row['title'], 'content'=>$row['content']];
}

file_put_contents('/tmp/articles.json', json_encode($data, JSON_UNESCAPED_UNICODE));
