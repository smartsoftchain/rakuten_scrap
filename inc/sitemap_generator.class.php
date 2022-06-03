<?php
class SitemapGenerator{
  private $sitemap;
  private $urlset;
   
  function __construct(){
    $this->sitemap = new DOMDocument('1.0', 'UTF-8');
    $this->sitemap->preserveWhiteSpace = false;
    $this->sitemap->formatOutput = true;
     
    $this->urlset = $this->sitemap->appendChild( $this->sitemap->createElement("urlset") );
    $this->urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
  }
   
  function set_url($params){
    $url = $this->urlset->appendChild( $this->sitemap->createElement('url') );
    foreach($params as $key => $value){
      if(strlen($value)){
        $url->appendChild( $this->sitemap->createElement($key, $value) );
      }
    }
  }
   
  function generate($urlset=array(), $file=null){
    if(!empty($urlset)){
      foreach($urlset as $url){
        $this->set_url($url);
      }
    }
   
    if( is_null($file) ) {
      //header("Content-Type: text/xml; charset=utf-8");
      return $this->sitemap->saveXML();
    } else {
      $this->sitemap->save( $file );
    }
  }
 
}