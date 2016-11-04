<?php
set_time_limit(0);
// example of how to use advanced selector features
include('simple_html_dom.php');
include('url_to_absolute.php');


//http://casiowatches.bg/index.php?route=product/search&search=casio&limit=100&page=20
// -----------------------------------------------------------------------------
// nested selector


$items= array();
for ($i=1;$i<=20;$i++){
    $url='http://casiowatches.bg/index.php?route=product/search&search=casio&limit=100&page='.$i ;
    $html = file_get_html($url);
    foreach($html->find('div.category-products div.item-img-info a ') as $cena) {

        array_push( $items,$cena->href);


    }


}
//=====================================================================


for ($i=0;$i<count($items);$i++){
    innerCrawl($items[$i]);
}




function innerCrawl($url){
    $inerUrl=$url;
    $inerUrl = preg_replace('/\s+/', '', $inerUrl);
    $price='';
    $innerHtml=file_get_html($inerUrl);
//echo $innerHtml;
    $name ='';
    foreach ($innerHtml->find('div.product-name h1') as $nameTag){
        $name=$nameTag;
        $pattern ='/<[^>]*>/';
        $name = preg_replace($pattern,"",$name);
    }
    foreach ($innerHtml->find('div.price-box p')as $priceTag){
        $price=$priceTag;
    }
    foreach ($innerHtml->find('div.price-box p span')as $priceTag){
        $price=$price.$priceTag;
    }
//tab-content product-tabs-content-inner clearfix tabcontent
//$price= $innerHtml->find('div.price-box p');
    $description= '';
    foreach ( $innerHtml->find('div.product-tabs-content-inner p span') as $desc){
        $description=$description.str_replace('&nbsp;','',$desc);
        //$pattern ='/&amp;#\d+;/';
        $pattern ='/<[^>]*>/';
        $description = preg_replace($pattern,"",$description);


    }
    $imagesSrc=array();
    $imagesNames=array();

    /// echo($innerHtml);
    foreach ($innerHtml->find('#etalage li img') as $image ){
        array_push($imagesNames,(end(explode("/", $image->src))));
        array_push($imagesSrc,$image->src);

    }

    $imagesString = '';
    foreach ($imagesNames as $img ){
        $imagesString .= $img." ";

    }
    echo($name.'<br>' );
    echo ($description.'<br>');
    $fs = fopen("mydataText.csv","a");
    fwrite($fs,$name . "©" . $description . "©" . $price . "©" .$imagesString ."\n");
    fclose($fs);
//    $imageFolder ='images/';
//    for ($i=0;$i<count($imagesSrc);$i++){
//        downloadFile($imagesSrc[$i],$imageFolder.$imagesNames[$i]);
//    }


}



//downloadFile('http://casiowatches.bg/image/cache/data/New_Casio_2015/EFR-541SBDB-1AVF-228x228.jpg','images/1.jpg');
function downloadFile($url, $path)
{
    $in=    fopen($url, "rb");
    $out=   fopen($path, "wb");
    while ($chunk = fread($in,8192))
    {
        fwrite($out, $chunk, 8192);
    }
    fclose($in);
    fclose($out);

}