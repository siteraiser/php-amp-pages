html {background: transparent radial-gradient(ellipse at center center , #868686 0%, #828C95 36%, #28343B 100%) repeat fixed 0% 0%;}
body{
font-family:roboto,verdana,arial,sans-serif;
padding:5px 10px;
background-image: linear-gradient(rgba(0,130,180,.2) 2px, transparent 2px),
linear-gradient(90deg, rgba(0,130,180,.2) 2px, transparent 2px),
linear-gradient(rgba(80,80,80,.3) 1px, transparent 1px),
linear-gradient(90deg, rgba(80,80,80,.3) 1px, transparent 1px);
background-size:40px 40px, 80px 80px, 20px 20px, 20px 20px;

}


/*
background: transparent url(/images/backmin.png) no-repeat center center fixed; -webkit-background-size: cover;-moz-background-size: cover;-o-background-size: cover;background-size: cover;
*/


h1{background: rgba(40, 200, 255, 0.7);padding:5px;}
.container{
	margin:0 auto;
	max-width:1000px;
	padding:10px 20px;
	background: rgba(40, 200, 255, 0.5);
	box-shadow: inset 0 6px  15px -4px rgba(31, 73, 125, 0.8), inset 0 -6px  8px -4px rgba(31, 73, 125, 0.8);
	border-radius: 5px;
}
.clearfix:after{content:"";display:table;clear:both}
#authimg{max-width:<?php echo$amp->author->image_size[0];?>px;}
.article-details{width:100%;padding-bottom:10px;display:inline-block;}
@media screen and (min-width: 800px) {
.article-details{
width:<?php echo (730 - $amp->author->image_size[0]);?>px;}
.author{width:<?php echo$amp->author->image_size[0];?>px;height:<?php echo$amp->author->image_size[1];?>px;float:right;
}
}
.logo{max-width: <?php echo$details['publisher']['logo']['width'];?>px;}
div pre{overflow-x: auto;padding:3px;}
div > pre { box-shadow: 0px 0px 10px #888888;
}