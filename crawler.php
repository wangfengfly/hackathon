<?php

set_time_limit(0);

define('APP_NAME','crawler');
require_once("/search/nginx/html/superphp/SOSO/SOSO.php");
require 'vendor/autoload.php';

use PHPHtmlParser\Dom;
use JonnyW\PhantomJs\Client;

$soso = new SOSO();
$conn = new SOSO_Base_Data_Connection();
$dom = new Dom();

$dbh = new PDO('mysql:host=10.139.22.181;dbname=hackathon', 'dhuser', 'dhdev123');

for($page=1; $page<=200; $page++){
    //$url = "https://36kr.com/api/search-column/219?per_page=20&page=$page";
    $url = "https://36kr.com/api//search/entity-search?page=$page&per_page=40&keyword=%E6%97%A0%E4%BA%BA%E9%A9%BE%E9%A9%B6&entity_type=post";
    $res = $conn->get($url);
    $res = json_decode($res,true);
    $items = $res['data']['items'];
    if(count($items) == 0){
        break;
    }
    foreach($items as $item){
        $id = $item['id'];
        $title = $item['title'];
        $summary = $item['summary']??'';
        $published_at = strtotime($item['published_at']);
        $item_url = "https://36kr.com/p/$id.html";

        $client = Client::getInstance();
        $client->getEngine()->setPath('/search/odin/phantomjs/bin/phantomjs');
        $request = $client->getMessageFactory()->createRequest();
        $response = $client->getMessageFactory()->createResponse();
        $request->setMethod('GET');
        $request->setUrl($item_url);
        $client->send($request, $response);
        if($response->getStatus() === 200) {
            $html = $response->getContent();
            preg_match("/<script>var props=(.*)<\/script>/", $html, $matches);
            if(isset($matches[1])) {
                preg_match('/"content"\:(.*)<\/p>/', $matches[1], $matches);
                if(isset($matches[1])){
                    $statement = $dbh->prepare('set names utf8; insert into articles(aid,title,summary,url,content,published_at) VALUES (:aid,:title,:summary,:url,:content,:published_at)');
                    $row = array(
                        'aid'=>$id, 'title'=>$title,
                        'summary'=>$summary, 'url'=>$item_url,
                        'content'=>$matches[1],
                        'published_at'=>$published_at
                    );
                    $statement->execute($row);
                }
            }
        }
    }
}
