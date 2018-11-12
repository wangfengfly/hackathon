<?php

set_time_limit(0);

define('APP_NAME','crawler');
require_once("/search/nginx/html/superphp/SOSO/SOSO.php");
require 'vendor/autoload.php';

use PHPHtmlParser\Dom;
use JonnyW\PhantomJs\Client;

$soso = new SOSO();


function work($begin, $end){
    $conn = new SOSO_Base_Data_Connection();
    $dom = new Dom();

    $dbh = new PDO('mysql:host=10.139.22.181;dbname=hackathon', 'dhuser', 'dhdev123');

    for($page=$begin; $page<=$end; $page++){
        //$url = "http://db.auto.sina.com.cn/search/?search_txt=%E8%87%AA%E5%8A%A8%E9%A9%BE%E9%A9%B6%E6%94%BF%E7%AD%96&page=$page";
        $url = "http://www.qianjia.com/MSAdmin/News/GetNewsByClassIDlist?pageindex=$page&pagesize=14&ClassIDlist=1384";
	$json = $conn->get($url);
	$json = json_decode($json, true);
	$obj = $json['obj'];
	foreach($obj as $item){
		$title = $item['Title'];
		$url = $item['url'];
		$summary = $item['Summary'];
		$html = $conn->get($url);
		$dom->load($html);
		$cont = $dom->find('.m-article');
		$statement = $dbh->prepare('set names utf8; insert into policies(title,summary,url,content) VALUES (:title,:summary,:url,:content)');
                        $row = array(
                             'title'=>$title,
                            'summary'=>$summary, 'url'=>$url,
                            'content'=>$cont,
                          
                        );
                        $statement->execute($row);
	}
    }
}

work(1,10);



