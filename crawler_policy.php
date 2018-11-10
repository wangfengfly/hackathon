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
        //$url = "https://36kr.com/api/search-column/219?per_page=20&page=$page";
        $url = "http://zhengce.beijing.gov.cn/library/192/33/50/438650/index_$page.html";
        $html = $conn->get($url);
        $dom->load($html);
        $accbgs = $dom->find('ul li');
        foreach($accbgs as $accbg){
            $innerHtml = $accbg->innerHtml;
            $dom->load($innerHtml);
            $a = $dom->find('a');
            $title = $a->text;
            $href = 'http://zhengce.beijing.gov.cn'.$a->href;
            $matches = [];
            preg_match("/<span>(.*)<\/span>/", $innerHtml, $matches);
            $published_at = strtotime($matches[1]);

            $html = $conn->get($href);
            $dom->load($html);
            $content = $dom->find('.doc-text');
            $statement = $dbh->prepare('set names utf8; insert into policies(title,url,content,published_at) VALUES (:title,:url,:content,:published_at)');
                            $row = array(
                                'title'=>$title,
                                'url'=>$href,
                                'content'=>$content,
                                'published_at'=>$published_at
                            );
            $statement->execute($row);
        }
    }
}

work(2,300);



