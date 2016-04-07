<?php 


class Amp {
	public $base_url;
	public $doc_root;
	public $req_url;
	public $pdo;	
	public $json;
	public $array;
	public $title;
	public $image;
	public $content;
	public $category;
	public $cononical;
	public $author;
	public $display_image;
	
	public function __construct()
	{
		$this->author = new stdClass;
	}
	public function get_connection(){
	include_once('../system/config.inc.php');	
		if(!is_object($this->pdo)){
		require('../'.DB);
			$this->pdo=get_dbconn();			
		}
	}
	public function getUser($usr,$prop){
		$query="SELECT username,urls FROM users WHERE id = ?;";
		$stmt=$this->pdo->prepare($query);
		$r=$stmt->execute(array($usr)); 	
		//try fto fetch the results
		 if($r){
			$stmt->setFetchMode(PDO::FETCH_ASSOC);
			$user = $stmt->fetch();
		}
			
		//Store user in session, redirect...
		if($user){
			if($prop == 'username'){
				return $user['username'];		
			}
			else if($prop == 'urls'){
				$urls= explode(',', $user['urls']);
				
				return $urls;		
			}		
		}
	}
	public function getContent(){
	
	
		$this->get_connection();
	
		$this->base_url    = 'http://www.siteraiser.com/';
		$this->doc_root    = $_SERVER['DOCUMENT_ROOT'].'/';
		
		$this->req_url     = parse_url(urldecode($_SERVER['REQUEST_URI']), PHP_URL_PATH);
		
	
		$this->output="";	
		
		$this->path = trim($this->req_url, '/');    // Trim leading slash(es)
	
		
	
			
		$this->url_segments = explode('/', urldecode($this->path));			
		$this->path=parse_url($this->path, PHP_URL_PATH);	//echo path here	
		
		
	
		$this->cononical='/blog/'.$this->url_segments[1].'/'.$this->url_segments[2];
		
		$stmt=$this->pdo->prepare("SELECT * FROM blog WHERE published = 1  AND link = :article;");
		$stmt->execute(array(':article'=>$this->cononical)); 	
		
		$i=0; $count=$stmt->rowCount($result);
		if ($count > 0) {
			$output='';
			
			while (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
				$i+=1;
				$this->articleId=$row["id"];
				$headline=$row["headline"];
				$description='Site Raiser\'s Web Blog - Article: '.$headline.' - ' .$row["Category"];
				$category=$row["Category"];$type = $row["type"];
				
				
				
				$time_in_12 = date("g:i a", strtotime(substr($row["date"], 0, -3)));
				$time_in_12=substr($row["date"], 0, -8).$time_in_12;
				if($row["lastUpdate"]!=''){
					$time_in_122 = date("g:i a", strtotime(substr($row["lastUpdate"], 0, -3)));
					$time_in_122=substr($row["lastUpdate"], 0, -8).$time_in_122;
				
				
				$this->category=$category;
				$str=$row["content"];

				
				$breaker = $str;
				$breaker=strip_tags($breaker,'<p><br></p><div></div><span></span><pre></pre><code></code><style></style><a></a><h2></h2><h3></h3><h4></h4><ul></ul><li></li><img>');//img not safe for public use
				
				$output.=$social.'';
							



				$this->author->link=$this->getUser($row["user"],'urls')[0];
				
				$this->author->image_link=BASE_URL.'/applications/blog/images/user-images/'.str_replace(' ', '-', $this->getUser($row["user"],'username')).'.jpg'; //virtual images available, not used
				$this->author->image_size = getimagesize($this->author->image_link);	
				
				
				
				if($row["image"]!=''){
					
					$size = getimagesize(BASE_URL.'/images/banners/th_'.@$row["image"]);					
					
					$img_url=BASE_URL.'/images/banners/th_'.@$row["image"];
   					$img_width=$size['0'];
    					$img_height=$size['1'];
    					
    					

				}

				
				
				$output.= '<span class="blog-content entry-content" itemprop="articleBody">'.$breaker.'</span></div></article>';
				$this->content=$output;
			}	
	
				 
				
$json = array(
 "@context"=>"http://schema.org",
 "@type"=>"BlogPosting",
 "mainEntityOfPage"=>array(
    "@type"=>"WebPage",
    "@id"=>'/amp/'.$this->url_segments[1].'/'.$this->url_segments[2]
  ),
  "headline"=> $headline,
  
  "datePublished"=> $time_in_12,
  "dateModified"=> $time_in_122,
  "author"=> array(
    "@type"=> "Person",
    "name"=> $this->getUser($row["user"],'username')
  ),
  "publisher"=> array(
   "@type"=> "Organization",
   "name"=> "Site Raiser",
   "logo"=> array(
     "@type"=> "ImageObject",
     "url"=> BASE_URL.'/images/siteraiser60.png',
     "width"=> 281,
     "height"=> 60
   )
 ),
 "description"=> $description
);
if($img_url != ''){
$this->display_image = true;
$json['image'] = array(
    "@type"=> "ImageObject",
    "url"=> $img_url,
    "height"=> $img_height,
    "width"=> $img_width
  );
}else{
//use author image
$this->display_image = false;
$json['image'] = array(
    "@type"=> "ImageObject",
    "url"=>  BASE_URL.'/images/bigsiteraiserwizard.png',
    "height"=> 700,
    "width"=> 1400
  );
}

$this->array =$json;
$this->json = '<script type="application/ld+json">'.
json_encode($json).
'</script>';


}
		}else{header("HTTP/1.0 404 Not Found");echo$fullValue.'Page Not Found <a href="http://www.siteraiser.com">Site Raiser Home</a>';exit;}
	$this->content = $output; 
		
	}
		
	
}
$amp = new Amp();

$amp->getContent();

$details=$amp->array;



$html=$amp->content;

$doc = new DOMDocument();
@$doc->loadHTML($html);

$doc->removeChild($doc->doctype); 
$doc->replaceChild($doc->firstChild->firstChild->firstChild, $doc->firstChild);
//fix images
$imgs = $doc->getElementsByTagName('img');	
$total=$imgs->length;


for ($i = 0; $i < $total; $i++) {
$name = 'img'.$i;
$$name  = $imgs->item($i);
}
	
for ($i = 0; $i < $total; $i++) {
$name = 'img'.$i;
$src= $$name->getAttribute('src'); 
$parsed = parse_url($src);
if (empty($parsed['scheme'])) {
    $src = BASE_URL.$src;
}
$size = getimagesize($src);					
$width=$size['0'];
$height=$size['1'];
//Grab alt value too
$alt= $$name->getAttribute('alt'); 


$a=$doc->createElement('amp-img');
$a->setAttribute('src',$src);
$a->setAttribute('alt',$alt);
$a->setAttribute('width',$width);
$a->setAttribute('height',$height);
$a->setAttribute('layout','responsive');

//Keep Id if there is one, else make one
if($$name->getAttribute('id')){
	$imgids[$i]['id'] = $$name->getAttribute('id');  
}else{
	$imgids[$i]['id']=$i;
}
$imgids[$i]['width']=$width;

//Create a div wrapper for sizing
$d=$doc->createElement('div');
//Set s + number for id
$d->setAttribute('id','s'.$imgids[$i]['id']); 
//Append amp to div 
$d->appendChild($a);
//Replace image object with amp-img
$$name->parentNode->replaceChild($d,$$name);
}        

//reomve content atts from pre & code elements
$pres = $doc->getElementsByTagName('pre');	
 
for ($i = 0; $i < $pres->length; $i++) {
        $pre = $pres->item($i);
        //remove target attribute       
        $pre->removeAttribute('content');
        $pre->removeAttribute('itemprop');
}       
$codes = $doc->getElementsByTagName('code');	
 
for ($i = 0; $i < $codes->length; $i++) {
        $code = $codes->item($i);
        //remove target attribute       
        $code->removeAttribute('content');
        $code->removeAttribute('itemprop');
}     

$html=$doc->saveHTML();




function minify( $css, $comments ){
/* minify css function from sitepoint.com */
    // Normalize whitespace
    $css = preg_replace( '/\s+/', ' ', $css );
 
    // leaving and empty /**/ will break the it!!!! Remove comment blocks, everything between /* and */, unless preserved with /*! ... */
    if( !$comments ){
        $css = preg_replace( '/\/\*[^\!](.*?)\*\//', '', $css );
    }//if
     
    // Remove ; before }
    $css = preg_replace( '/;(?=\s*})/', '', $css );
 
    // Remove space after , : ; { } */ >
   $css = preg_replace( '/(,|:|;|\{|}|\*\/|>) /', '$1', $css );
  /* Breaks search media query */
    // Remove space before , ; { } ( ) >
    $css = preg_replace( '/ (,|;|\{|}|>)/', '$1', $css ); 

    // Strips leading 0 on decimal values (converts 0.5px into .5px)
    $css = preg_replace( '/(:| )0\.([0-9]+)(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}.${2}${3}', $css );

    // Strips units if value is 0 (converts 0px to 0)
    $css = preg_replace( '/(:| )(\.?)0(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}0', $css );

    // Converts all zeros value into short-hand
    $css = preg_replace( '/0 0 0 0/', '0', $css );

    // Shortern 6-character hex color codes to 3-character where possible
   // $css = preg_replace( '/#([a-f0-9])\\1([a-f0-9])\\2([a-f0-9])\\3/i', '#\1\2\3', $css );
  
    return trim( $css );
}//minify

	


ob_start();
include( 'ampcss.php' );
$css = ob_get_clean();
$css= minify( $css, $comments = 0);







?><!doctype html>
<html amp lang="en">
  <head>
    <meta charset="utf-8">
    <title><?php echo$details['headline'];?></title>
    <link rel="canonical" href="<?php echo BASE_URL.$amp->cononical;?>" />
    <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">

<?php
echo$amp->json;
?>

<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto">
<style amp-custom><?php 
echo$css;

if(isset($imgids)){
foreach($imgids as $key => $value){
echo '#s'.$value['id'].'{max-width:'.$value['width'].'px;}';
}
}


?></style>

<style>body {opacity: 0}</style><noscript><style>body {opacity: 1}</style></noscript>

    <script async src="https://cdn.ampproject.org/v0.js"></script><script async custom-element="amp-analytics" src="https://cdn.ampproject.org/v0/amp-analytics-0.1.js"></script>
  </head>
  <body>


  <header id="top">
     <div class="container clearfix">
 
  <div class="details"> 
   <h1><?php echo$details['headline'];?></h1>
  <div class="article-details"> 
  
   By: <?php echo$details['author']['name'];?>
  <br />
  Published: <?php echo$details['datePublished'];?>
   <br />Last Updated: <?php echo$details['dateModified'];?><br />
   Category: <a class="internal" href="/blog/<?php echo strtolower(str_replace(' ', '-', $amp->category))?>"><?php echo $amp->category?></a>
   <br />
    <br />Presented by: <br /><br />
    <div class="logo">
    <a href="<?php echo BASE_URL;?>"><amp-img id="logo" src="<?php echo$details['publisher']['logo']['url'];?>" width=<?php echo$details['publisher']['logo']['width'];?> height=<?php echo$details['publisher']['logo']['height'];?> layout="responsive"></amp-img></a>
       </div>
    </div>
   <div class="author">
    <a href="<?php echo $amp->author->link;?>"><amp-img id="authimg" src="<?php echo$amp->author->image_link;?>" width=<?php echo$amp->author->image_size[0];?> height=<?php echo$amp->author->image_size[1];?>  layout="responsive"></amp-img></a>
   </div>
  
   </div>
  </div>
</header>

<section>
   <div class="container">
<?php if($amp->display_image){?>
    <amp-img src="<?php echo$details['image']['url'];?>" width=<?php echo$details['image']['width'];?> height=<?php echo$details['image']['height'];?> layout="responsive"></amp-img>
<?php
}

echo $html; 

/*
<span id="test1" class="box">
  Click here to generate an analytics event...
</span>
*/
?>  </div>
</section>  



<amp-pixel src="https://ssl.google-analytics.com/collect?v=1&tid=UA-40076253-1&t=pageview&cid=CLIENT_ID(google-analytics)&dt=<?php echo$details['headline'];?>&dl=<?php echo BASE_URL.'/amp/'.$amp->url_segments[1].'/'.$amp->url_segments[2];?>&z=RANDOM">
</amp-pixel>


</body>
</html>
<?php
/*
<script async custom-element="amp-analytics" src="https://cdn.ampproject.org/v0/amp-analytics-0.1.js"></script>
<amp-analytics type="google-analytics">
{
  "vars": { "account_id": "UA-40076253-1"}
  "triggers": {[{
    "selectors": "a",
    "on": "CLICK",
    "vars": {
      "event_category": "All",
      "event_label": "outbound links",
      "event_action": "click"
    },
    "request": "event"
  }, {
    "on": "LOAD",
    "request": "pageview"
  }]}
}

</amp-analytics>
<amp-analytics type="googleanalytics" id="analytics2">
<script type="application/json">
{
  
  "vars": {
    "account": "UA-40076253-1"
  },
  "triggers": {
    "default pageview": {
      "on": "visible",
      "request": "pageview",
      "vars": {
        "title": "<?php echo $details['headline'];?>"
      }
    },
    "click on #test1 trigger": {
      "on": "click",
      "selector": "#test1",
      "request": "event",
      "vars": {
        "eventCategory": "examples",
        "eventAction": "clicked-test1"
      }
    },
    "click on #authimg trigger": {
      "on": "click",
      "selector": "#authimg",
      "request": "event",
      "vars": {
        "eventCategory": "navigated-away",
        "eventAction": "clicked-author-image"
      }
    },
    "click on #top trigger": {
      "on": "click",
      "selector": "#top",
      "request": "event",
      "vars": {
        "eventCategory": "examples",
        "eventAction": "clicked-header"
      }
    }
  }
}
</script>
</amp-analytics>
*/