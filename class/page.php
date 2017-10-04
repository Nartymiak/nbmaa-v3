<?php

	abstract class Page {

		abstract protected function makeBody();
		abstract protected function makeHTMLElements($result);
		abstract protected function makeSubNav();

		protected $navCategories = array();
		protected $navCategoryLinks = array();
		protected $pageID;
		protected $metaOG = array('title' => '', 'description' =>'', 'img' => '' );

		// print an HTML header
		protected function HTMLheader($title = '') {
		?>
			<html>
				<!DOCTYPE html>
				<head>
					<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
					<meta name=viewport content="width=device-width, initial-scale=1"> 

					<title id="<?php echo str_replace(array("\r", "\n"), '',$title); ?>"> NBMAA | <?php echo str_replace(array("\r", "\n"), '',$title); ?></title>
					<!-- css -->
							<link href="<? echo $GLOBALS['rootDirectory'] ?>/css/nbmaa3.css?v=1.1" rel="stylesheet" type="text/css" />
					<!-- font -->
					<link href='http://fonts.googleapis.com/css?family=Lato:100,400,700,100italic,400italic,700italic' rel='stylesheet' type='text/css'>
					<link rel="stylesheet" href="<? echo $GLOBALS['rootDirectory'] ?>/css/font-awesome.min.css">
					<!-- jquery -->
					<script type="text/javascript" src="<?php echo $GLOBALS['rootDirectory']; ?>/js/jquery-1.11.1.js"></script>
					
					<?php if(!empty($this->metaOG['title']) && !empty($this->metaOG['description']) && !empty($this->metaOG['img'])) { ?>
						<!-- meta -->
						<meta property="og:type"          content="article" /> 
						<meta property="og:title"         content="<?php echo $this->metaOG['title']; ?>" /> 
						<meta property="og:site_name"     content="NBMAA" /> 
						<meta property="og:url"           content="<?php echo "http://" .$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI]; ?>" /> 
						<meta property="og:description"   content="<?php echo $this->metaOG['description']; ?>"/>
						<meta property="og:image"         content="<?php echo $this->metaOG['img']; ?>" />

					<?php } ?>
					<!-- google analytics -->
					<script>
						(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
						(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
						m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
						})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

						ga('create', 'UA-47285500-1', 'auto');
						ga('send', 'pageview');

					</script>

				</head>
			<body itemscope itemtype="http://schema.org/Museum">
				<div id="siteWrapper">
					<div class="wrapper"><div id="grid"></div></div>
					<!-- social media -->
					<div id="fb-root"></div>
						<script>(function(d, s, id) {
						  var js, fjs = d.getElementsByTagName(s)[0];
						  if (d.getElementById(id)) return;
						  js = d.createElement(s); js.id = id;
						  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.3";
						  fjs.parentNode.insertBefore(js, fjs);
						}(document, 'script', 'facebook-jssdk'));
						</script>
		<?
		}

		/** prints the nav header **/
		protected function nav(){
			
			$logoFile = "nbmaa-white-logo.png";
			$logoBGColor = "rgba(71, 61, 55, .7)";
			$exhibitionsResult = queryCalendarPageExhibitions();
			$exhibitionImage;
			$exhibitonGallery;
			$randomInt;
			$keywords;

			// get the links in the main Nav
			if($mainNav = queryNav()) {
				// retrieve the nav categories from the db
				foreach($mainNav as $title){
					if($temp = queryReference("NAV_CATEGORY", "NavTitle", $title['Title'])){
						array_push($this->navCategories, $temp);
					}
				}
			}

			// get all the categories for each of the links in the main Nav
			if($this->navCategories!=NULL){
				// retrieve the links in the categories
				foreach ($this->navCategories as $category) {
					foreach($category as $categoryLink) {
						if($temp = queryReference("NAV_CATEGORY_LINK", "NavCategoryID", $categoryLink['NavCategoryID'])){
							usort($temp, "nav_id_sort");
							array_push($this->navCategoryLinks, $temp);
						}
					}
				}
			}

			// get the image and gallery reference of the main exibition
			if($exhibitionsResult != NULL){
				// get random exhibition
				$size = sizeof($exhibitionsResult);
				$randomInt = rand(0, $size-1);
				$exhibitionImage 	= 	queryReference('ARTWORK', 'ArtworkID', $exhibitionsResult[$randomInt]['ArtworkReferenceNo']); 
				$exhibitonGallery 	=	queryReference('GALLERY', 'GalleryID', $exhibitionsResult[$randomInt]['GalleryReferenceNo']);
			}

			// keywords in the calendar query
			$keywords = $this->buildNavKeywords(queryKeywords());

			?>
			<header>
				<div id="header-content">					
					<div class="logo" style="background-color:<?php echo $logoBGColor ?>;">
					<a itemprop="url" href="<? echo $GLOBALS['rootDirectory'] ?>">
							<? echo $this->toImg("logos", $logoFile, "new britain museum of american art logo", "logo"); ?>
					</a>
					</div>
				<nav id="mainNav">
					<div class="menuItem visitLink">
						<a>VISIT</a>

						<div class="dropDown" aria-haspopup="true">
							<div class="logoSpace"><!-- place holder for under the logo --></div>
							<div class="left">
							
							<?php
								// write the nav category header
								if(sizeof($this->navCategories[0])!=0){		
									echo "<div class=\"navCategory\">" . $this->navCategories[0][0]["IconHTML"] ."".$this->navCategories[0][0]["Title"] ."</div>" ; 
								//write each link
									$this->writeNavLinks($this->navCategories[0][0]["NavCategoryID"]);
								}
							?>
							
							</div>
							<div class="middle">
							
							<?php
								// write the nav category header
								if(sizeof($this->navCategories[0])!=0){		
									echo "<div class=\"navCategory\">" .$this->navCategories[0][1]["IconHTML"]."".$this->navCategories[0][1]["Title"]. "</div>" ; 
								//write each link
									$this->writeNavLinks($this->navCategories[0][1]["NavCategoryID"]);
								}
							?>
							
							</div>
							<div class="right">
								
							<?php
								// write the nav category header
								if(sizeof($this->navCategories[0])!=0){		
									echo "<div class=\"navCategory\">" .$this->navCategories[0][2]["IconHTML"]."".$this->navCategories[0][2]["Title"] ."</div>" ; 
								//write each link
									$this->writeNavLinks($this->navCategories[0][2]["NavCategoryID"]);
								}
							?>
							
							</div>
							<div class="clear"></div>
						</div>
					</div>

					<div class="menuItem exhibitionLink">
						<a>EXHIBITION</a>

						<div class="dropDown" aria-haspopup="true">
							<div class="logoSpace"><!-- place holder for under the logo --></div>
							<div class="left">

							<?php
								// write the nav category header
								if(sizeof($this->navCategories[1])!=0){	

									echo "<div class=\"navCategory\">" .$this->navCategories[1][0]["IconHTML"]."".$this->navCategories[1][0]["Title"]. "</div>" ; 

									//get the image and data for the main exhibition, write the html
									if($exhibitionImage != NULL) { 

										echo 	"<div class=\"image\" style=\"background-image:url('" .$GLOBALS['rootDirectory']. "/images/exhibition-page-images/" .$exhibitionImage[0]['ImgFilePath']. "');\">

												</div>";
									}

									//write each link
									$this->writeNavLinks($this->navCategories[1][0]["NavCategoryID"]);
								
									// write the caption for the exhibition in a floating div
									if($exhibitionsResult[0] != NULL && $exhibitionImage != NULL) {

										echo 	"<a class=\"exhibitionCaption\" href=\"" .$GLOBALS['rootDirectory']. "/exhibition/" .$exhibitionsResult[$randomInt]['Link']. "\">
													<div class=\"inner\">
														<h6>" .$exhibitionsResult[$randomInt]['Title']. "</h6>
														<p class=\"title\">" .$exhibitonGallery[0]['NickName']. "</p>
													</div>
												</a>";
									}
								}
									
								
							?>
								<div class="clear"></div>
							</div>
							<div class="right">
								
							<?php
								// write the nav category header
								if(sizeof($this->navCategories[1])!=0){		
									echo "<div class=\"navCategory\">" .$this->navCategories[1][1]["IconHTML"]."".$this->navCategories[1][1]["Title"]. "</div>" ; 
								//write each link
									$this->writeNavLinks($this->navCategories[1][1]["NavCategoryID"]);
								}
							?>
							
							</div>
							<div class="clear"></div>
						</div>
					</div>
					
					<!-- calendar link -->
					<div class="menuItem calendarLink">
						<a>CALENDAR</a>
						
						<div class="dropDown" aria-haspopup="true">
							<div class="logoSpace"><!-- place holder for under the logo --></div>
							<div class="middle">
								<div class="calendarWrapper">
									<!-- javascript calendar -->
									<a class="leftArrow"></a>
									<div class="container">
										<div class="threeCalendars">
											<div class="oldCalendar">
											</div>
											<div class="calendar">
											</div>
											<div class="newCalendar">
											</div>
											<div class="clear"></div>
										</div>
									</div>
									<a class="rightArrow"></a>
									<div class="clear"></div>
								</div>
								<a id="selectMonth" <?php if($this->pageID != 'calendar'){ echo "href=\"".$GLOBALS['rootDirectory']. "/calendar/Today\""; }?>>Select entire month</a>
							</div>
							<div class="right keyword">
								<?php 

								// print the keywords from the query
								if(!$keywords){
									// handle error
								} else {

									if($this->pageID == 'calendar'){
										foreach($keywords as $keyword){ 
										?>
											<label><input class="calInput" type="checkbox" name="keyword[]" value="<?php echo $keyword['KeywordID'] ?>"><?php echo $keyword['Word'] ?></label>
										<?php
										}
									} else {
										foreach($keywords as $keyword){ 
										?>
											<a href="<?php echo $GLOBALS['rootDirectory']; ?>/calendar/<?php echo toLink($keyword['KeywordID']) ?>"><label><?php echo $keyword['Word'] ?></label></a>
										<?php
										}
									}
								}
								?>
									<a href="<?php echo $GLOBALS['rootDirectory']; ?>/calendar/Today"><label>All</label></a>
									<?php if($this->pageID == 'calendar'){ echo "<a id=\"calendarGo\"><label>Go</label></a>"; } ?>
								<div class="clear"></div>
								<a style="text-decoration:underline;" href="http://www.nbmaa.org/pdfs/nbmaa-calendar-winter-2016.pdf" target="_blank">Download Quarterly Calendar</a>
							</div>
							<div class="clear"></div>
						</div>
					</div>
					
					<div class="menuItem educationLink">
						<a>EDUCATION</a>

						<div class="dropDown" aria-haspopup="true">
							<div class="logoSpace"><!-- place holder for under the logo --></div>
							<div class="left">
							
							<?php
								// write the nav category header
								if(sizeof($this->navCategories[2])!=0){		
									echo "<div class=\"navCategory\">" .$this->navCategories[2][0]["IconHTML"]."".$this->navCategories[2][0]["Title"]. "</div>" ; 
								//write each link
									$this->writeNavLinks($this->navCategories[2][0]["NavCategoryID"]);
								}
							?>
							
							</div>
							<div class="middle">
							
							<?php
								// write the nav category header
								if(sizeof($this->navCategories[2])!=0){		
									echo "<div class=\"navCategory\">" .$this->navCategories[2][1]["IconHTML"]."".$this->navCategories[2][1]["Title"]. "</div>" ; 
								//write each link
									$this->writeNavLinks($this->navCategories[2][1]["NavCategoryID"]);
								}
							?>
							
							</div>
							<div class="right">
								
							<?php
								// write the nav category header
								if(sizeof($this->navCategories[2])!=0){		
									echo "<div class=\"navCategory\">" .$this->navCategories[2][2]["IconHTML"]."".$this->navCategories[2][2]["Title"]. "</div>" ; 
								//write each link
									$this->writeNavLinks($this->navCategories[2][2]["NavCategoryID"]);
								}
							?>
							
							</div>
							<div class="clear"></div>
						</div>
					</div>

					<div class="menuItem supportLink">
						<a>SUPPORT US</a>

						<div class="dropDown" aria-haspopup="true">
							<div class="logoSpace"><!-- place holder for under the logo --></div>
							<div class="left">
							
							<?php
								// write the nav category header
								if(sizeof($this->navCategories[3])!=0){		
									echo "<div class=\"navCategory\">" .$this->navCategories[3][0]["IconHTML"]."".$this->navCategories[3][0]["Title"]. "</div>" ; 
								//write each link
									$this->writeNavLinks($this->navCategories[3][0]["NavCategoryID"]);
								}
							?>
							
							</div>
							<div class="middle">
							
							<?php
								// write the nav category header
								if(sizeof($this->navCategories[3])!=0){		
									echo "<div class=\"navCategory\">" .$this->navCategories[3][1]["IconHTML"]."".$this->navCategories[3][1]["Title"]. "</div>" ; 
								//write each link
									$this->writeNavLinks($this->navCategories[3][1]["NavCategoryID"]);
								}
							?>
							
							</div>
							<div class="right">
								
							<?php
								// write the nav category header
								if(sizeof($this->navCategories[3])!=0){		
									echo "<div class=\"navCategory\">" .$this->navCategories[3][2]["IconHTML"]."".$this->navCategories[3][2]["Title"]. "</div>" ; 
								//write each link
									$this->writeNavLinks($this->navCategories[3][2]["NavCategoryID"]);
								}
							?>
							
							</div>
							<div class="clear"></div>
						</div>
					</div>
					<a href="http://nbmaashop.com/">SHOP</a>
				</nav>
				<div id="menuButton">MENU <i class="fa fa-bars"></i></div>
			</div>
			</header>
			<!-- comment out to hide special nav announce across top 
			<div id="navAnnouncement">
				<p><strong>Holiday Hours:</strong> New Year's Eve - close at 5 p.m. / Caf&eacute; close at 1 p.m., New Year's Day - closed</p>
			</div>
			-->
			<h1 itemprop="name">NEW BRITAIN MUSEUM OF AMERICAN ART</h1>
		<?
		}

		/** prints the footer **/
		protected function HTMLfooter(){
		
			// get the year and the month
			if(!$_POST['month']) {
				List($y,$m) = explode(" ", date("Y m"));
			} else {
				List($m,$y) = explode("-", $_POST['month']);
			}
		?>
				<footer>
					<div class="wrapper">
						<div class="column">
							<a href="<?php echo $GLOBALS['rootDirectory']; ?>/museum-of-american-art/about">About the Museum</a>
							<a href="<?php echo $GLOBALS['rootDirectory']; ?>/museum-of-american-art/contact-us">Contact</a>
							<a href="<?php echo $GLOBALS['rootDirectory']; ?>/museum-of-american-art/employment">Employment</a>
						</div>
						<div class="column right">
							<a href="<?php echo $GLOBALS['rootDirectory']; ?>/museum-of-american-art/opportunities-for-artists">Opportunities for Artists</a>
							<a href="<?php echo $GLOBALS['rootDirectory']; ?>/museum-of-american-art/rent-the-museum">Rent the Museum</a>
							<a href="<?php echo $GLOBALS['rootDirectory']; ?>/museum-of-american-art/accessibility">Accessibility</a>
						</div>
						<div class="columnTwo">
							<a href="<?php echo $GLOBALS['rootDirectory']; ?>/museum-of-american-art/giving">How to Donate to the Museum</a>
							<a href="<?php echo $GLOBALS['rootDirectory']; ?>/museum-of-american-art/annual-report">Annual Report</a>
							<a href="mailto:artymiakn@nbmaa.org">Questions about the new site?</a>
						</div>
						<div class="columnTwo">
							<a href="http://www.ctvisit.com/"><img src="<? echo $GLOBALS['rootDirectory']. "/images/sponsors/CT-Logo-DECD-Left-OOTA-RGB_R.jpg" ?>"></a>
						</div>
						<div class="clear"></clear>
					</div>
				</footer>
				<script type="text/javascript" src="<?php echo $GLOBALS['rootDirectory']; ?>/js/menu.js"></script>
				<script type="text/javascript" src="<?php echo $GLOBALS['rootDirectory']; ?>/js/calendar.js"></script>
				<script type="text/javascript" src="<?php echo $GLOBALS['rootDirectory']; ?>/js/color.js"></script>
				<script type="text/javascript" src="<?php echo $GLOBALS['rootDirectory']; ?>/js/adjure.js"></script>
				<script type="text/javascript" src="<?php echo $GLOBALS['rootDirectory']; ?>/js/Vibrant.js"></script>
				<script type="text/javascript" src="<?php echo $GLOBALS['rootDirectory']; ?>/js/easing.js"></script>
				<?php 
					if($this->pageID == 'index'){ 
					?>
						<script type="text/javascript" src="<?php echo $GLOBALS['rootDirectory']; ?>/js/slider.js"></script>
					<?php
					}
				?>
				<script type="text/javascript">

					$(document).ready(function() { 

						menu(); // menu.js
						makeCalendar(<?php echo $y. ", " .$m ?>); // calendar.js
						logo();
						adjure();

						$(document.getElementById("logoBg")).load(function() {

							//getAverageRGB(document.getElementById("logoBg"));
							var vibrant = new Vibrant(document.getElementById("logoBg"));
    						var swatches = vibrant.swatches();
							$('.logo').css('background-color', swatches['DarkVibrant'].getHex());
						});

						

					});

				</script>
				<!-- share on google plus -->
				<!-- Place this tag in your head or just before your close body tag. -->
				<script src="https://apis.google.com/js/platform.js" async defer></script>
			</div><!-- siteWrapper-->
		</body>
	</html>
		<?
		}

		/** prints cta in the middle of the page **/
		protected function cta($link){ ?>
			<div class="cta">
				<? 	echo "<a href=\"" .$link. "\">" ;
					echo $this->toImg("cta-images", "cta-ctArtTrailPass.jpg", "nbmaa-cta", NULL);
					echo "</a>\r\n"; 
				?>
			</div>
		<? 	echo "\r\n";
		}

		/** 
		* creates a string representing an img element
		* @param 	dir 		The directory of the image. It has to be found in the images directory.
		* @param 	fileName 	The filename of the image
		* @param 	alt 		The data for alt attribute
		* @return 	String 		String representing an img element
		*/
		protected function toImg($dir, $fileName, $alt, $itemprop){

			if($alt==NULL || $alt==""){	$alt = "The New Britain Museum of American Art";}
			if($itemprop == NULL || $itemprop ==""){  $writeItemProp = NULL; }
			else { $writeItemProp = "itemprop=\"" .$itemprop. "\""; }
			$string = "<img ".$writeItemProp." src=\"" .$GLOBALS['rootDirectory']. "/images/" .$dir. "/" .$fileName. "\" title=\"" .$alt. "\"> ";

			return $string;

		}

		protected function writeNavLinks($navCategoryID){
			//write each link
			echo "<div class=\"navCategoryLinks\">";
			foreach($this->navCategoryLinks as $linkSet){
				foreach($linkSet as $link){
					if($link['NavCategoryID'] == $navCategoryID) {
						echo "<a href=\"".$GLOBALS['rootDirectory']. "/" .$link['SubDirectory']. "/" .$link['Link']. "\">".$link['Title']. "</a>";
					}
				}
			}
			echo "</div>";
		}

		protected function buildTombstone($artistNames, $artworkQuery, $detail){

			$result;

			// buld tombstone
			if($artworkQuery['TombstoneOverride']){
				$result .= $artworkQuery['TombstoneOverride'];
			} else {
				if($artistNames){							$result  .= $artistNames; }
				if($artworkQuery['Title']){ 				$result  .= ", <i> " .$artworkQuery['Title']. "</i>"; }
				if($detail == TRUE){					$result  .= ", detail"; }
				if($artworkQuery['DateCreated']){ 			$result  .= ", " .$artworkQuery['DateCreated'];}
				if($artworkQuery['Medium']){ 				$result  .= ", " .ucfirst(strtolower($artworkQuery['Medium']));}
				if($artworkQuery['Dimensions']){ 			$result  .= ", " .$artworkQuery['Dimensions'];}
				if($artworkQuery['Location']){ 				$result  .= ", " .$artworkQuery['Location'];}
				if($artworkQuery['CourtesyStatement']){ 	$result  .= ", " .$artworkQuery['CourtesyStatement'];}
			}

			return $result;
		}

		// $keywords array
		protected function buildNavKeywords($keywords){

			$parentKeyword = array();

			if(!$keywords){
				// handle error
			} else {
				foreach ($keywords as $keyword){
					if($keyword['ParentKeywordID'] == NULL){
						array_push($parentKeyword, $keyword);
					}
				}

			return $parentKeyword;
				
			}

		}

		protected function formatExhibitions($exhibitionsResult, $firstExhibitionImage){

			$returnArray=array();

			// build the exhibitions array
			if(!$exhibitionsResult){
				// handle error

			} else {

				$count=0;

				foreach($exhibitionsResult as $tuple){

					$class = "";
					$topImg = "";

					//if its the first element, add the .top class and create the image element
					if($count==0){ 						
						$class = "showImg";
						$topImg = $this->toImg("exhibition-page-images", $firstExhibitionImage[0]['ImgFilePath'], $tuple['Title'], "image");
					}

					array_push($returnArray, "
				
						<div itemscope itemtype=\"http://schema.org/VisualArtsEvent\" class=\"calendarEventsWrapper\">
							<a itemprop=\"url\" href=\"" .$GLOBALS['rootDirectory']. "/exhibition/" .$tuple['Link']. "\">
								<div class=\"calendarExhibitionElement ".$class."\">
									".$topImg."
									<h3 itemprop=\"name\" >" .$tuple['Title']. "</h3>
									<p itemprop=\"description\">" .shortenText($tuple['BodyContent']). "</p>
									<meta itemprop=\"startdate\" content=\"".$tuple['StartDate']."\">
								</div>
							</a>
						</div>
					");
				
				$count++;	
				}
			}

			return $returnArray;
		}

		function makeConsistentLinks(){


			echo "		<h3>Extra</h3>";
			echo "		<a href=\"" .$GLOBALS['rootDirectory']. "/museum-of-american-art/membership\">";
			if($this->pageID == "exhibition"){
				echo "Members See It Free";
			} else {
				echo "Become a Member";
			}
			echo "		</a>\r\n";
			echo "		<a target=\"_blank\" href=\"http://eepurl.com/bgHJt1\">Join our Email List</a>\r\n";
			echo "		<div id=\"socialSection\">";
			echo "		<a id=\"shareLinks\">Share</a>\r\n";
			echo "		<div class=\"shareLinks\">";
							?>
							<ul>
								<li><div class="fb-share-button" data-href="<?php echo $_SERVER['REQUEST_URI'] ?>" data-layout="button_count"></div></li>
								<li><a href="https://twitter.com/share" class="twitter-share-button">Tweet</a>
									<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
								</li>
								<li><div class="g-plus" data-action="share"></div></li>
							</ul>
							<?php
			echo		"</div>";

			echo "		<a id=\"followLinks\">Follow Us</a>";
			echo "		<div class=\"followLinks\">\r\n
							<ul>
								<li><a target=\"_blank\" href=\"https://twitter.com/NBMAA\"><i class=\"fa fa-twitter\"></i></a></li>
								<li><a target=\"_blank\" href=\"https://www.facebook.com/NBMAA\"><i class=\"fa fa-facebook-official\"></i></a></li>
								<li><a target=\"_blank\" href=\"https://instagram.com/nbmaa56/\"><i class=\"fa fa-instagram\"></i></a></li>
								<li><a target=\"_blank\" href=\"http://nbmaa.tumblr.com/\"><i class=\"fa fa-tumblr\"></i></a></li>
								<li><a target=\"_blank\" href=\"https://plus.google.com/+NewBritainMuseumofAmericanArt/posts\"><i class=\"fa fa-google-plus\"></i></a></li>
							</ul>
						</div>";
			echo "		</div>";

		}

		function buildFeaturedEvent(){

			echo 	"<h5><i class=\"fa fa-heart\"> </i> Special Event</h5>
					<span itemprop = \"Event\" itemscope itemtype=\"http://schema.org/Event\">

						<a href=\"http://www.nbmaauncorked.org\" itemprop=\"url\">
							<div class=\"calendarExhibitionElement\">
								<img itemprop=\"image\" src=\"" .$GLOBALS['rootDirectory']. "/images/event-page-images/nbmaa-uncorked2.jpg\" title =\"NBMAA Uncorked: Cheer's to Sixteen Years\">
								<h3 itemprop=\"name\">NBMAA Uncorked</h3>
								<p>Enjoy a vast selection of wine and delicious bites from top chefs at our special fundraiser event.</p>
								<p style=\"color:blue;\"> Learn more.</p>
							</div>
						</a>
						<meta itemprop=\"startdate\" content=\"2015-10-15\">
						<span itemprop=\"location\" itemscope itemtype=\"http://schema.org/Place\">
							<meta itemprop=\"name\" content=\"New Britain Museum of American Art\">
							<address itemprop=\"address\" itemscope itemtype=\"http://schema.org/PostalAddress\">
								<span itemprop=\"name\">New Britain Museum of American Art</span>
								<span itemprop=\"streetAddress\">56 Lexington Street</span>
								<span itemprop=\"addressLocality\">New Britain</span>
								<span itemprop=\"addressRegion\">CT</span>, <span itemprop=\"addressCountry\">USA</span>
							</address>
						</span>

					</span>";
		}

	}

	class StaticPage extends Page{

		private $subNavLinksQuery=array();
		private $NavCategoryLinkQuery;

		private $title;
		private $primaryImage;
		private $imagePath;
		private $imageCaption;
		private $bodyContent;

		function __construct($url){
			$result = queryStaticPage($url);
			$this->HTMLheader($result['Title']);
			$this->makeHTMLElements($result);
			$this->makeBody();
			$this->HTMLfooter();
			
			
		}

		function makeBody(){ // STATIC PAGE function

			// write the page ID in the parent class
			$this->pageID = "static";

			//full-width background
			if($this->imagePath){ 					echo "	<div id=\"background\" style=\"background-image:url('" .$this->imagePath. "');\"></div>\r\n
													     	<img style=\"display:none;\" id=\"logoBg\" src=\"" .$this->imagePath. "?".microtime(). "\">\r\n" ;
			}

			// wrapper
			echo "		<div class=\"wrapper\" id=\"static\">\r\n";
			
			// header section
			$this->nav();

			//main section
			echo "		<div class=\"mainSection\">\r\n";
			
			//sub navigation section
			$this->makeSubNav();

			// right column
			echo "		<div class=\"rightColumn\">\r\n";
			if($this->title) {						echo "		<h2>" .$this->title. "</h2>\r\n"; } 
			if($this->primaryImage) { 				echo "		" .$this->primaryImage.  "\r\n";}
			if($this->imageCaption) {				echo "		<p class=\"tombstone\">" .$this->imageCaption. "</p>\r\n";}
			if($this->bodyContent) {				echo "		<div class=\"bodyContent\">" .$this->bodyContent. "		</div>\r\n";}
			echo "		</div><!-- end rightColumn -->\r\n";
			echo "		<div class=\"clear\"></div>\r\n";
			echo "		</div><!-- end mainSection -->\r\n";

			// call to action (CTA) section
			$this->cta("http://ctarttrail.org/");
			echo "		</div><!-- end wrapper -->\r\n";

		}

		function makeHTMLElements($result){ // STATIC PAGE function

			if(!$result){
				// handle error
			} else {

				$this->subNavLinksQuery = querySubNav($result['Link']);



				// build the html img element for the main image
				if(!$result['ImgFilePath']){

				// handle error
					$imgFile = array('nbmaa-museum-spring.jpg', 'nbmaa-museum-evening.jpg', 'nbmaa-museum-night.jpg', 'nbmaa-museum-day.jpg' );
					$randomInt = rand(0, sizeof($imgFile)-1);
					$this->imagePath = $GLOBALS['rootDirectory']. "/images/static-page-images/" .$imgFile[$randomInt];

				} else {
					//create HTML alt attribute text
					if($result['Title']){	$alt = $result['Title'];}
					//create primary image
					$this->primaryImage=$this->toImg("static-page-images", $result['ImgFilePath'], $alt, "primaryImageOfPage" );
					//create image path for background
					$this->imagePath = $GLOBALS['rootDirectory']. "/images/static-page-images/" .$result['ImgFilePath'];
				}

				// title
				if($result['Title']){ $this->title = nl2br($result['Title']);}

				// build the html img element for the main image
				if(!$result['ImgFilePath']){
				// handle error
				} else {
					//create HTML alt attribute text
					if($result['Title']){	$alt = $result['Title'];}
					//create primary image
					$this->primaryImage=$this->toImg("static-page-images", $result['ImgFilePath'], $alt, "primaryImageOfPage" );
					//create image path for background
					$this->imagePath = $GLOBALS['rootDirectory']. "/images/static-page-images/" .$result['ImgFilePath'];
				}

				// caption
				if($result['ImgCaption']) {	$this->imageCaption = nl2br($result['ImgCaption']);}

				// body content (called Body in STATIC_PAGE table)
				if($result['Body']){ $this->bodyContent = $result['Body'];}

			}

		}

		function makeSubNav(){ // STATIC PAGE function

			echo "		<nav class=\"subNav\">\r\n";

			// Quick Hack to add subpage to subpage. Affects "Individual Members Page only"
			if 	(
				$this->title == "How to Join" ||
				$this->title == "Basic Benefits for All" ||
				$this->title == "Basic Level Membership" ||
				$this->title == "Circle Level Membership" ||
				$this->title == "Premier Circle Level Membership" ||
				$this->title == "John Butler Talcott Society Level Membership" ||
				$this->title == "Young Adult Level Membership" ||
				$this->title == "What is NARM?"
			){
				
				echo "<h3 class=\"top\">Individual Membership</h3>\r\n";
				echo "<a href=\"" .$GLOBALS['rootDirectory']. "/museum-of-american-art/individual-members\">How to Join</a>\r\n";
				echo "<a href=\"" .$GLOBALS['rootDirectory']. "/museum-of-american-art/basic-benefits-for-all\">Basic Benefits for All</a>\r\n";
				echo "<a href=\"" .$GLOBALS['rootDirectory']. "/museum-of-american-art/what-is-narm\">What is NARM?</a>\r\n";
				echo "<h3 class=\"top\">Membership Types</h3>\r\n";
				echo "<a href=\"" .$GLOBALS['rootDirectory']. "/museum-of-american-art/basic-level-membership\">Basic Level Membership</a>\r\n";
				echo "<a href=\"" .$GLOBALS['rootDirectory']. "/museum-of-american-art/circle-level-membership\">Circle Level Membership</a>\r\n";
				echo "<a href=\"" .$GLOBALS['rootDirectory']. "/museum-of-american-art/premier-circle-level-membership\">Premier Circle Level Membership</a>\r\n";
				echo "<a href=\"" .$GLOBALS['rootDirectory']. "/museum-of-american-art/john-butler-talcott-society-level-membership\">John Butler Talcott Society Level Membership</a>\r\n";
				echo "<a href=\"" .$GLOBALS['rootDirectory']. "/museum-of-american-art/young-adult-level-membership\">Young Adult Level Membership</a>\r\n";

			// end Quick Hack
			} else {

				if($this->subNavLinksQuery){

					echo "		<h3 class=\"top\">More</h3>\r\n";

					foreach ($this->subNavLinksQuery as $link) {
						if($link['OutsideLink']=="0"){
							echo "		<a href=\"" .$GLOBALS['rootDirectory']. "/museum-of-american-art/" .$link['Link']. "\">" .$link['Title']. "</a>\r\n";
						} else {
							echo "		<a href=\"" .$link['Link']. "\">" .$link['Title']. "</a>\r\n";
						}
					}
				}
			} // end quick Hack else{}

			$this->makeConsistentLinks();

			echo "		</nav>\r\n";
		}
	}

	class LobbyPage extends Page{

		private $url;
		private $imagePath;
		private $calendarEvents = array();
		private $currentExhibitons = array();
		private $events;

		function __construct($url){

			//first, query the db
			$result = queryLobbyPage($url);
			$this->HTMLheader(linkToString($url));
			$this->url = $url;
			$this->makeHTMLElements($result);
			$this->makeBody();
			$this->HTMLfooter();
			

		}

		protected function makeBody(){ // LOBBY PAGE function
			
			// write the pageID in the parent class
			$this->pageID = "lobby";

			//full-width background
			if($this->imagePath){ 					echo "	<div id=\"background\" style=\"background-image:url('" .$this->imagePath. "');\"></div>\r\n
												<img style=\"display:none;\" id=\"logoBg\" src=\"" .$this->imagePath. "?".microtime(). "\">\r\n" ;
			}

			// wrapper
			echo "		<div class=\"wrapper\" id=\"lobby\">\r\n";

			// header section
			$this->nav();
			
			// main section
			echo "		<div class=\"mainSection\">\r\n";

			// left column
			$this->makeSubNav();

			//right column
			echo "		<div class=\"rightColumn\">\r\n";
			echo "		<h2>" .linkToString($this->url). " EXHIBITIONS</h2>";
			
			// print current exhibitions
			if(!$this->currentExhibitons){
				//handle error
			} else {

				// loop through the array to access the elements
				foreach ($this->currentExhibitons as $element) {
						
					echo $element;
				}
			}


			echo "		</div><!-- end right -->\r\n";
			echo "		<div class=\"clear\"></div>";
			echo "		</div><!-- end mainSection -->\r\n";
			// call to action (CTA) section
			$this->cta("http://ctarttrail.org/");
			echo "		</div><!-- end wrapper -->\r\n";

		}

		protected function makeHTMLElements($exhibitionsResult){ // LOBBY PAGE function

			$events = array();
			$exhibitions = array();
			$receptions;
			$classes;
			$firstExhibitionImage;
			$todaysDate = date('Y-m-d');

			//build the image
			if(!$exhibitionsResult[0]){
				//handle error

			} else {

				$firstExhibitionImage = queryReference('ARTWORK', 'ArtworkID', $exhibitionsResult[0]['ArtworkReferenceNo']);
				// build the html img element for the main image

				if(!$firstExhibitionImage[0]['ImgFilePath']){
				// handle error
				} else {
					//create HTML alt attribute text
					if($firstExhibitionImage[0]['Title']){	$alt = $firstExhibitionImage[0]['Title'];}

					//create image path for background
					$this->imagePath = $GLOBALS['rootDirectory']. "/images/exhibition-page-images/" .$firstExhibitionImage[0]['ImgFilePath'];
				}
			}
			// build the exhibitions array
			$this->currentExhibitons = $this->formatExhibitions($exhibitionsResult, $firstExhibitionImage);

		}

		protected function makeSubNav(){ // LOBBY PAGE function

			echo "		<nav class=\"subNav\">\r\n";

			if($this->subNavLinksQuery){

				echo "		<h3 class=\"top\">More</h3>\r\n";

				foreach ($this->subNavLinksQuery as $link) {
					echo "		<a href=\"" .$GLOBALS['rootDirectory']. "/museum-of-american-art/" .$link['Link']. "\">" .$link['Title']. "</a>\r\n";
				}
			}

			// write the nav category header
			if(sizeof($this->navCategories[1])!=0){		
				echo "<div class=\"navCategory\">" .$this->navCategories[1][0]["IconHTML"]."".$this->navCategories[1][0]["Title"]. "</div>" ; 
				//write each link
				$this->writeNavLinks($this->navCategories[1][0]["NavCategoryID"]);
			}

			// write the nav category header
			if(sizeof($this->navCategories[1])!=0){		
				echo "<div class=\"navCategory\">" .$this->navCategories[1][1]["IconHTML"]."".$this->navCategories[1][1]["Title"]. "</div>" ; 
				//write each link
				$this->writeNavLinks($this->navCategories[1][1]["NavCategoryID"]);
			}

			$this->makeConsistentLinks();
			
			echo "		</nav>\r\n";

		}
	}

	class CalendarPage extends Page{

		private $imagePath;
		private $calendarEvents = array();
		private $currentExhibitons = array();
		private $events;
		private $featuredEvent;

		function __construct($url){

			//first, query the db
			$result = queryCalendarPageEvents($url);
			$this->HTMLheader("Calendar " .linkToString($url));
			$this->makeHTMLElements($result);
			$this->makeBody();
			$this->HTMLfooter();
			

		}

		protected function makeBody(){ // CALENDAR PAGE function
			
			// write the pageID in the parent class
			$this->pageID = "calendar";

			//full-width background
			if($this->imagePath){ 					echo "	<div id=\"background\" style=\"background-image:url('" .$this->imagePath. "');\"></div>\r\n
												<img style=\"display:none;\" id=\"logoBg\" src=\"" .$this->imagePath. "?".microtime(). "\">\r\n" ;
			}

			// wrapper
			echo "		<div class=\"wrapper\" id=\"calendar\">\r\n";

			// header section
			$this->nav();
			
			// main section
			echo "		<div class=\"mainSection\">\r\n";

			// left column
			echo "		<div class=\"leftColumn\">\r\n";
			
			// featured event cta
			//$this->buildFeaturedEvent();

			// current exhibition section
			echo "		<div id=\"calendarCurrentExhibitions\">";
			echo "		<h5><i class=\"fa fa-eye\"> </i> Current Exhibitions</h5>";
			// print current exhibitions
			if(!$this->currentExhibitons){
				//handle error
			} else {

				// loop through the array to access the elements
				foreach ($this->currentExhibitons as $element) {
						
					echo $element;
				}
			}
			echo "		</div><!-- end calendarCurrentExhibitions -->\r\n";
			
			echo "		</div><!-- end left -->\r\n";

			//right column
			echo "		<div class=\"rightColumn\">\r\n";

			// print calendar events
			if(!$this->calendarEvents){
				//handle error
			} else {

				// loop through the array to access the elements
				foreach ($this->calendarEvents as $element) {
						
					echo $element;
				}
			}


			echo "		</div><!-- end right -->\r\n";
			echo "		<div class=\"clear\"></div>";
			echo "		</div><!-- end mainSection -->\r\n";
			// call to action (CTA) section
			$this->cta("http://ctarttrail.org/");
			echo "		</div><!-- end wrapper -->\r\n";

			//$this->makeXMLHttpRequestSection();
		}

		protected function makeHTMLElements($result){ // CALENDAR PAGE function

			$events = array();
			$exhibitions = array();
			$receptions;
			$classes;
			$firstExhibitionImage;
			// query for exhibitions
			$exhibitionsResult = queryCalendarPageExhibitions();
			$todaysDate = date('Y-m-d');

			// build the events array by querrying the EVENT table using
			// EVENT_DATE_TIMES array for reference
			if($result == null){
				array_push($this->calendarEvents, "	<div class=\"date\"><h5><i class=\"fa fa-calendar\"> </i> Today's Events</h5>
														<p>There are no events for the category you have selected</p>
													</div>");

			} else {

				$tempDate;

				// use these three variables to set up which element to show image
				$nextImage = false;
				$doNextImage = false;
				$alreadyHasImage = array();

				foreach($result as $tuple){

					$class = "";
					$topImg = "";
					$eventLink ="";
					

					if($doNextImage == true && !in_array($tuple['ImgFilePath'], $alreadyHasImage)){
						$nextImage = true;
						$doNextImage = false;
					}

					// set up outside link or not
					if($tuple['OutsideLink'] == true){
						$eventLink = $tuple['Link'];
					} else {
						$eventLink = "" .$GLOBALS['rootDirectory']. "/event/" .$tuple['Link']. "";
					}

					//print the date for each section
					if($tuple['StartDate'] != $tempDate){

						List($y,$m,$d) = explode("-",$tuple['StartDate']);
						$timestamp = mktime(0,0,0,$m,$d,$y);

						// check to see if the date section = today's date
						if($tuple['StartDate']==$todaysDate) {
							// write this instead of actual date
							array_push($this->calendarEvents, "	<div class=\"date\"><h5><i class=\"fa fa-calendar\"> </i> Today's Events</h5>
																	<div class=\"startDate\">" .$tuple['StartDate']. "</div>
																</div>");

						} else {

							array_push($this->calendarEvents, "	<div class=\"date\"><h5><i class=\"fa fa-calendar\"> </i> " .date("l, F d, Y", $timestamp). "</h5>
																	<div class=\"startDate\">" .$tuple['StartDate']. "</div>
																</div>");

						}
						$tempDate = $tuple['StartDate'];

						// check to see if image hasn't been used
						if(!in_array($tuple['ImgFilePath'], $alreadyHasImage)) {
							
							$nextImage = true;

						// if it has, print the next one
						} else {

							$doNextImage = true;
						}
					}

					// if its the first in the section and has not been used or the first one has already been used
					if( $tuple['ImgFilePath'] && $nextImage == true){	
						$topImg = $this->toImg("event-page-images", $tuple['ImgFilePath'], $tuple['Title'], "image"); 
						$class = "showImg";
						array_push($alreadyHasImage, $tuple['ImgFilePath']);
					
					// if for some reason there is no image file path
					} else if(!$tuple['ImgFilePath'] && $nextImage == true){
						$doNextImage = true;
					}

					// print each element
					array_push($this->calendarEvents, "

						<div class=\"calendarEventsWrapper\" itemprop = \"Event\" itemscope itemtype=\"http://schema.org/Event\">
							<a href=\"" .$eventLink. "\" itemprop=\"url\">
								<div class=\"calendarElement ".$class."\">
									".$topImg."
									<h3 itemprop=\"name\">" .$tuple['EventTitle']. "</h3>
									<h4>" .$tuple['TypeTitle']. "</h4>
									<p>" .date("g:i a", strtotime($tuple['StartTime'])). " to " .date("g:i a", strtotime($tuple['EndTime'])). "</p>
									<p itemprop=\"description\">" .shortenText($tuple['Description']). "</p>
									<meta itemprop=\"startdate\" content=\"".$tuple['StartDate']."T".$tuple['StartTime']."\"><time class=\"startDate\">" .$tuple['StartDate']. "</time>
									<span itemprop=\"location\" itemscope itemtype=\"http://schema.org/Place\">
										<meta itemprop=\"name\" content=\"New Britain Museum of American Art\">
										<address itemprop=\"address\" itemscope itemtype=\"http://schema.org/PostalAddress\">
											<span itemprop=\"name\">New Britain Museum of American Art</span>
											<span itemprop=\"streetAddress\">56 Lexington Street</span>,
											<span itemprop=\"addressLocality\">New Britain</span>,
											<span itemprop=\"addressRegion\">CT</span>, <span itemprop=\"addressCountry\">USA</span>
										</address>
									</span>
								</div>
							</a>
						</div>
					");

					$nextImage = false;
				}	

			}

			//build the image
			if(!$exhibitionsResult[0]){
				//handle error

			} else {

				$firstExhibitionImage = queryReference('ARTWORK', 'ArtworkID', $exhibitionsResult[0]['ArtworkReferenceNo']);
				// build the html img element for the main image

				if(!$firstExhibitionImage[0]['ImgFilePath']){
				// handle error
				} else {
					//create HTML alt attribute text
					if($firstExhibitionImage[0]['Title']){	$alt = $firstExhibitionImage[0]['Title'];}

					//create image path for background
					$this->imagePath = $GLOBALS['rootDirectory']. "/images/exhibition-page-images/" .$firstExhibitionImage[0]['ImgFilePath'];
				}
			}
			// build the exhibitions array
			$this->currentExhibitons = $this->formatExhibitions($exhibitionsResult, $firstExhibitionImage);

		}

		protected function makeSubNav(){ // CALENDAR PAGE function

		}

		protected function makeXMLHttpRequestSection(){ // CALENDAR PAGE function
			echo "
				<script language=\"Javascript\"></script>
			";
		}
	}

	class ClassroomPage extends Page{ 

		private $calendarEvents = array();
		private $currentExhibitons = array();
		private $events;
		private $title;
		private $primaryImage;
		private $imagePath;
		private $imageCaption;
		private $bodyContent;
		private $keywordDescription;
		private $subNavLinksQuery;

		function __construct($url){

			//first, query the db
			$result = queryClassroomPage($url);
			$this->makeHTMLElements($result);
			$this->HTMLheader($this->title);
			$this->makeBody();
			$this->HTMLfooter();
			

		}

		protected function makeBody(){ // CLASSROOM PAGE function
			
			// write the pageID in the parent class
			$this->pageID = "classroom";

			//full-width background
			if($this->imagePath){ 					echo "	<div id=\"background\" style=\"background-image:url('" .$this->imagePath. "');\"></div>\r\n
												<img style=\"display:none;\" id=\"logoBg\" src=\"" .$this->imagePath. "?".microtime(). "\">\r\n" ;
			}

			// wrapper
			echo "		<div class=\"wrapper\" id=\"classroom\">\r\n";
			
			// header section
			$this->nav();

			//main section
			echo "		<div class=\"mainSection\">\r\n";
			
			//sub navigation section
			$this->makeSubNav();

			// right column
			echo "		<div class=\"rightColumn\">\r\n";
			if($this->title) {						echo "		<h2>" .$this->title. "</h2>\r\n"; } 
			if($this->primaryImage) { 				echo "		" .$this->primaryImage.  "\r\n"; }
			if($this->imageCaption) {				echo "		<p class=\"tombstone\">" .$this->imageCaption. "</p>\r\n"; }
													echo "		<div class=\"bodyContent\">\r\n";
			if($this->bodyContent) {				echo "		" .$this->bodyContent; }
			if($this->keywordDescription) {			echo "		" .$this->keywordDescription; }
			if($this->events) {						echo "		<h3>Select a studio class below</h3> \r\n ";
													echo "		" .$this->makeClassroomEvents($this->events); }
			echo "		</div>\r\n";
			echo "		</div><!-- end rightColumn -->\r\n";
			echo "		<div class=\"clear\"></div>\r\n";
			echo "		</div><!-- end mainSection -->\r\n";

			// call to action (CTA) section
			$this->cta("http://ctarttrail.org/");
			echo "		</div><!-- end wrapper -->\r\n";


		}

		protected function makeHTMLElements($result){ // CLASSROOM PAGE function

			if(!$result){
				// handle error

			} else {

				// if result is of the main classroom page
				if($result[0] == "main-page"){

					// subnav links
					if($result['ClassroomPageID']){ $this->subNavLinksQuery = queryClassRoomKeywords($result['ClassroomPageID']); }

					// title
					if($result['Title']){ $this->title = nl2br($result['Title']);}

					// caption
					if($result['ImgCaption']) {	$this->imageCaption = nl2br($result['ImgCaption']);}

					// body content (called Body in STATIC_PAGE table)
					if($result['Body']){ $this->bodyContent = $result['Body'];}

				} else {

					// if the url is for listing events
					$this->subNavLinksQuery = queryClassRoomKeywords($result[1]['ClassroomPageID']);

					// keyword Description
					$this->keywordDescription = $result[1]['Description'];
					
					// title
					if($result[1]['Word']){ $this->title = $result[1]['Word'];}

					// events array
					if($result[2]) { $this->events = $result[2];}


				}


				// build the html img element for the main image
				if(!$result['ImgFilePath']){

				// handle error
					$imgFile = array('nbmaa-museum-spring.jpg', 'nbmaa-museum-evening.jpg', 'nbmaa-museum-night.jpg', 'nbmaa-museum-day.jpg' );
					$randomInt = rand(0, sizeof($imgFile)-1);
					$this->imagePath = $GLOBALS['rootDirectory']. "/images/static-page-images/" .$imgFile[$randomInt];

				} else {

					//create HTML alt attribute text
					if($result['Title']){	$alt = $result['Title'];}
					//create primary image
					$this->primaryImage=$this->toImg("classroom-page-images", $result['ImgFilePath'], $alt, "primaryImageOfPage" );
					//create image path for background
					$this->imagePath = $GLOBALS['rootDirectory']. "/images/classroom-page-images/" .$result['ImgFilePath'];

				}

			}

		}

		protected function makeSubNav(){ 
			echo "		<nav class=\"subNav\">\r\n";

			//var_dump($this->subNavLinksQuery);

			if($this->subNavLinksQuery){
				echo "		<h3 class=\"top\">About</h3>\r\n";
				echo "		<a href=\"".$GLOBALS['rootDirectory']. "/classroom/" .$this->subNavLinksQuery[0][0]['Link']. "\" id=\"" .$this->subNavLinksQuery[0][0]['Link']. "\">" .$this->subNavLinksQuery[0][0]['Title']. "</a>\r\n";
				echo "		<h3 class=\"top\">Programs</h3>\r\n";
				echo "		<div class=\"classLinks\">";
				foreach ($this->subNavLinksQuery as $link) {
					echo "		<a href=\"".$GLOBALS['rootDirectory']. "/classroom/" .$link[0]['KeywordID']. "\" id=\"" .$link[0]['KeywordID']. "\">" .$link[0]['Word']. "</a>\r\n";
				}
				echo "		</div>";
			}

			$this->makeConsistentLinks();

			echo "		</nav>\r\n";
		}

		/**
		*@param  an array of arrays (filled with events)
		*/
		protected function makeClassroomEvents($events){

			if(!$events){
				// handle error
			} else {

				foreach($events as $event){

					echo 	"<div class=\"calendarEventsWrapper\">";

					if($event['Link']){ 
						echo		"<a href=\"".$GLOBALS['rootDirectory']. "/event/" .$event['Link']. "\">\r\n"; 
						echo		"<div class=\"calendarElement\">";
						
						if($event['EventTitle']){ 			echo 	"<h3>" .$event['EventTitle']. "</h3>\r\n"; }
						if($event['StartDate']){
							if(strtotime(date('Y-m-d')) > strtotime($event['StartDate'])){
								echo 	"<p>Starts: " .date("F d, Y", strtotime($event['StartDate'])). "</p>\r\n";
							} else {
								echo 	"<p>Next class: " .date("F d, Y", strtotime($event['StartDate'])). "</p>\r\n"; 
							}
						}
						if($event['RegistrationEndDate']){	echo 	"<p class=\"registration\">Preregistration ends: " .date("F d, Y", strtotime($event['RegistrationEndDate'])). "</p>\r\n"; }
							else {							echo 	"<p class=\"registration\">Preregistration not required</p>"; }
						if($event['StartDate']){			echo 	"<div class=\"startDate\">" .$event['StartDate']. "</div>\r\n"; }
						echo 	"</div>\r\n
								</a>\r\n";
					}
					echo "</div>\r\n";
				}
			}
		}
	}


	class EventPage extends Page{

		private $imagePath;
		private $primaryImage;
		private $imageCaption;
		private $title;
		private $bodyContent;
		private $admissionCharge;
		private $eventType;
		private $eventArtistQuery;
		private $artistsQuery;
		private $exhibitionQuery;
		private $exhibitionLink;
		private $eventDateTimes;
		
		function __construct($url){

			//first, query the db
			$result = queryEventPage($url);
			$this->makeHTMLElements($result);
			$this->HTMLheader($result['Title']);
			$this->makeBody();
			$this->HTMLfooter();
		}

		protected function makeBody(){ // EVENT PAGE function

			// write the pageID in the parent class
			$this->pageID = "event";

			//full-width background
			if($this->imagePath){ 					echo "	<div id=\"background\" style=\"background-image:url('" .$this->imagePath. "');\"></div>\r\n
													     	<img style=\"display:none;\" id=\"logoBg\" src=\"" .$this->imagePath. "?".microtime(). "\">\r\n" ;
			}

			// wrapper
			echo "		<div class=\"wrapper\" id=\"event\">\r\n";
			
			// header section
			$this->nav();

			//main section
			echo "		<div class=\"mainSection\">\r\n";
			
			//sub navigation section
			$this->makeSubNav();

			// right column
			echo "		<div class=\"rightColumn\">\r\n";
			echo "		<div id=\"about\">\r\n";
			if($this->title) {						echo "		<h2>" .$this->title. "</h2>\r\n"; } 
			if($this->eventType) {					echo "		<h3>" .$this->eventType. "</h3>\r\n";}
			if($this->primaryImage) { 				echo "		" .$this->primaryImage.  "\r\n";}
			if($this->imageCaption) {				echo "		<p class=\"tombstone\">" .$this->imageCaption. "</p>\r\n";}
			if($this->bodyContent) {				echo "		<div class=\"bodyContent\">" .$this->bodyContent. "		</div>\r\n";}
			if($this->admissionCharge) {			echo "		<div class=\"admissionCharge\"><h3>Admission</h3><p>" .$this->admissionCharge. "</p></div>\r\n";}
			if($this->eventDateTimes) {				echo "		<div class=\"dateTimes\">";
													echo "			<h3>Schedule</h3>";
				foreach($this->eventDateTimes as $dateTime) {

					List($y,$m,$d) = explode("-",$dateTime['StartDate']);
					$timestamp = mktime(0,0,0,$m,$d,$y);

					echo " <p><span class=\"date\">" .date("l, F d, Y", $timestamp). "</span> " .date("g:i a", strtotime($dateTime['StartTime'])). " to " .date("g:i a", strtotime($dateTime['EndTime'])). "</p>";
				}

													echo "		</div>";
			}
			echo "		</div>\r\n";
			
			if($this->artistsQuery){
				echo "<div id=\"artists\">\r\n";
				foreach($this->artistsQuery as $artist){
					echo "<h2>".buildArtistName($artist). "</h2>\r\n";
					echo "<p>";
					echo $artist['Bio'];
					echo "</p>\r\n";
				}
				echo "</div>\r\n";
			}

			echo "		</div><!-- end rightColumn -->\r\n";
			echo "		<div class=\"clear\"></div>\r\n";
			echo "		</div><!-- end mainSection -->\r\n";

			// call to action (CTA) section
			$this->cta("http://ctarttrail.org/");
			echo "		</div><!-- end wrapper -->\r\n";
			
		}

		protected function makeSubNav(){ // EVENT PAGE function

			echo "		<nav class=\"subNav\">\r\n";

			if($this->artistsQuery){
				echo "		<h3 class=\"top\">About this Event</h3>\r\n";
				echo "		<div class=\"display\">\r\n";
				echo "		<a class=\"about current\">About</a>\r\n";

				if(count($this->artistsQuery)==1){ 				
					$artistName = buildArtistName($this->artistsQuery[0]);
					echo "		<a id=\"" .$this->artistsQuery[0]['ArtistID']. "\" class=\"artists\">" .$artistName. "</a>\r\n";
				} else {
					echo "		<a id=\"" .$this->artistsQuery[0]['ArtistID']. "\" class=\"artists\">Artists' Bio</a>\r\n";
				}

				echo "		</div>";
			}

			if($this->exhibitionQuery){
				
				echo "		<h3>Related Exhibition</h3>\r\n";
				echo "		<a href=\"" .$GLOBALS['rootDirectory']. "/exhibition/" .$this->exhibitionLink[0]['Link']. "\"> " .$this->exhibitionLink[0]['Title']. " </a>";

			}

			$this->makeConsistentLinks();
			
			echo "		</nav>\r\n";
		}

		protected function makeHTMLElements($result){ // EVENT PAGE function

			if(!$result){
				// handle error
			} else {

				// title
				if($result['Title']){ $this->title = nl2br($result['Title']);}

				// event type
				if($eventTypeQuery = queryReference('KEYWORD', 'KeywordID', $result['EventTypeID'])){
					$this->eventType = $eventTypeQuery[0]['Word'];
				}

				// build the html img element for the main image
				if(!$result['ImgFilePath']){
				// handle error
				} else {
					//create HTML alt attribute text
					if($result['Title']){	$alt = $result['Title'];}
					//create primary image
					$this->primaryImage=$this->toImg("event-page-images", $result['ImgFilePath'], $alt, "primaryImageOfPage" );
					//create image path for background
					$this->imagePath = $GLOBALS['rootDirectory']. "/images/event-page-images/" .$result['ImgFilePath'];
				}

				// caption
				if($result['ImgCaption']) {	$this->imageCaption = nl2br($result['ImgCaption']);}

				// body content (called Description in EVENT table)
				if($result['Description']){ $this->bodyContent = $result['Description'];}

				//admission charge
				if($result['AdmissionCharge']){ $this->admissionCharge = $result['AdmissionCharge']; }

				// build the EVENT_ARTISTS table query
				if($this->eventArtistQuery = queryReference('EVENT_ARTISTS', 'EventID', $result['EventID'])){

					// build the ARTIST query
					foreach($this->eventArtistQuery as $artist){
						if($this->artistsQuery = queryReference('ARTIST', 'ArtistID', $artist['ArtistID'])){
							// everything is gravy!
						}
					}
				}

				if($this->exhibitionQuery = queryReference('EXHIBITION_EVENTS', 'EventID', $result['EventID'])){
					$this->exhibitionLink = queryReference('EXHIBITION', 'ExhibitionID', $this->exhibitionQuery[0]['ExhibitionID']);
				}

				if($this->eventDateTimes = queryEventDateTimes($result['EventID'])){
					//everything is good!
				}
			}
			//  meta tags

			if($this->title && $this->eventType) {	$this->metaOG['title'] = $this->eventType. " | " .$this->title;  } 
			if($this->imagePath) { 					$this->metaOG['img'] = $this->imagePath; }
			if($this->bodyContent) {				$this->metaOG['description'] = shortenText($this->bodyContent); }	
		}
	}



	class ExhibitionPage extends Page{

		private $exhibitionID;
		private $artworkQuery;
		private $artistArtworkQuery;
		private $exhibitionArtworksQuery;
		private $artistsQuery;
		private $exhibitionEventsQuery;
		private $eventQuery;
		private $artistBiosQuery;
		private $lecturesArray;

		private $artistNames; // artists names are stored as a single string
		private $primaryImage;
		private $imagePath;
		private $tombstone;
		private $bodyContent;
		private $title;
		private $startDate;
		private $endDate;
		private $gallery;
		private $artistBios = array();


		function __construct($url){

			//first, query the db
			$result = queryExhibitionPage($url);

			$this->exhibitionID = $result['ExhibitionID'];
			$this->HTMLheader($result['Title']);
			$this->makeHTMLElements($result);
			$this->makeBody();
			$this->HTMLfooter();

		}

		// make html body element
		protected function makeBody(){

			// write the pageID in the parent class
			$this->pageID = "exhibition";

			//full-width background
			if($this->imagePath){ 					echo "	<div id=\"background\" style=\"background-image:url('" .$this->imagePath. "');\"></div>\r\n
													     	<img style=\"display:none;\" id=\"logoBg\" src=\"" .$this->imagePath. "?".microtime(). "\">\r\n" ;
			}

			// wrapper
			echo "		<div class=\"wrapper\" id=\"exhibition\">\r\n";
			
			// header section
			$this->nav();

			//main section
			echo "		<div class=\"mainSection\">\r\n";
			
			//sub navigation section
			$this->makeSubNav();

			// right column
			echo "		<div class=\"rightColumn\">\r\n";
			echo "		<div id=\"about\">";
			if($this->title) {						echo "		<h2>" .$this->title. "</h2>\r\n"; } 
			if($this->startDate && $this->endDate){ echo "		<p>" .$this->startDate. "&ndash;" .$this->endDate. "</p>\r\n";}
			if($this->gallery) {					echo "		<p class=\"gallery\">" .$this->gallery. "</p>\r\n";}
			if($this->primaryImage) { 				echo "		" .$this->primaryImage.  "\r\n";}
			if($this->tombstone) {					echo "		<p class=\"tombstone\">" .$this->tombstone. "</p>\r\n";}
			if($this->bodyContent) {				echo "		<div class=\"bodyContent\">" .$this->bodyContent. "		</div>\r\n";}
			echo "		</div>\r\n";

			if($this->exhibitionArtworksQuery) {
				echo "<div id=\"exhibitionArtworks\">";
				echo "<h2>Artwork</h2>";

				foreach($this->exhibitionArtworksQuery as $row){
					$art = queryReference('ARTWORK', 'ArtworkID', $row['ArtworkID']);
					echo "<img src =\"" .$GLOBALS['rootDirectory']. "/images/exhibition-page-images/" .$art[0]['ImgFilePath']. "\">";
				}

				echo "</div>\r\n";

			}

			if($this->artistBios){
				echo "<div id=\"artists\">";
				foreach($this->artistBios as $artist){
					echo "<h2>".buildArtistName($artist). "</h2>\r\n";
					echo "<p>";
					if($artist['Bio']) {	
						echo nl2p($artist['Bio']);
					}
					echo "</p>";
				}
				echo "</div>\r\n";
			}

			if($this->eventQuery){

				foreach($this->eventQuery as $event){

					$dateTime = queryReference('EVENT_DATE_TIMES', 'EventID', $event[0]['EventID']);


					List($y,$m,$d) = explode("-",$dateTime[0]['StartDate']);
					$timestamp = mktime(0,0,0,$m,$d,$y);

					$topImg = $this->toImg("event-page-images", $event[0]['ImgFilePath'], $event[0]['Title'], NULL); 
					$class = "showImg";
					
					echo "<div class=\"exhibitionEvent\" id=\"" .$event[0]['EventTypeID']. "\">";
					
					echo "	<div class=\"date\"><h5><i class=\"fa fa-calendar\"> </i> " .date("F d, Y", $timestamp). "</h5>
															<div class=\"startDate\">" .$dateTime[0]['StartDate']. "</div>
														</div>";

					echo "	<a href=\"" .$GLOBALS['rootDirectory']. "/event/" .$event[0]['Link']. "\">
								<div class=\"calendarElement ".$class."\">
										".$topImg."
										<h3>" .$event[0]['Title']. "</h3>
										<p>" .date("g:i a", strtotime($dateTime[0]['StartTime'])). " to " .date("g:i a", strtotime($dateTime[0]['EndTime'])). "</p>
										<p>" .shortenText($event[0]['Description']). "</p>
										<div class=\"startDate\">" .$event[0]['StartDate']. "</div>

								</div>
							</a>";

					echo "</div>";
				}

			}

			echo "		</div><!-- end rightColumn -->\r\n";
			echo "		<div class=\"clear\"></div>\r\n";
			echo "		</div><!-- end mainSection -->\r\n";

			// call to action (CTA) section
			$this->cta("http://ctarttrail.org/");
			echo "		</div><!-- end wrapper -->\r\n";
		}




		protected function makeSubNav(){

			
			echo "		<nav class=\"subNav\">\r\n";
			if($this->exhibitionArtworksQuery || $this->artistsQuery || $this->exhibitionVideoQuery || $this->exhibitionEventsQuery){
				echo "		<h3 class=\"top\">About the Exhibition</h3>\r\n";
				echo "		<div class=\"display\">";
				echo "		<a class=\"about current\">About</a>";
				if($this->exhibitionArtworksQuery){		echo "		<a id=\"" .$this->exhibitionArtworksQuery[0]['ExhibitionID']. "\" class=\"exhibitionArtworks\">Artwork</a>\r\n";}
				if(count($this->artistBios) == 1){ 		echo "		<a id=\"" .$this->artistsQuery[0]['ArtistID']. "\" class=\"artists\">Artist Bio</a>\r\n";}
				if(count($this->artistBios) > 1){ 		echo "		<a id=\"" .$this->artistsQuery[0]['ArtistID']. "\" class=\"artists\">Artists</a>\r\n";}
				if($this->exhibitionVideoQuery){ 		echo "		<a id=\"" .$this->artistsQuery[0]['ExhibitionID']. "\" class=\"videos\">Videos</a>\r\n";}
			

				if($this->exhibitionEventsQuery && $eventLinks = $this->makeEventLinks()){
					echo "		<h3>Related Events</h3>\r\n";
					if($eventLinks){ 						echo $eventLinks;}

				}

				echo "		</div>";
			}

			$this->makeConsistentLinks();

			echo "		</nav>\r\n";

		}

		/**
		* Prints out to html a list of event types that correspond with the exhibition
		**/
		protected function makeEventLinks(){

			$eventTypes = array();
			$returnString;
			// first query the KEYWORD table
			if($eventTypeQuery = get_table("SELECT * FROM KEYWORD")){
 
				// loop through result
				foreach($eventTypeQuery as $row){
					// loop through exhibition events
					foreach($this->eventQuery as $rowE){
						//if an event has an id that matches an id in the KEYWORD table
						if($row['KeywordID']==$rowE[0]['EventTypeID']){
							// check if its not already in the table to be printed
							if(!in_array($row['Word'], $eventTypes)){
								// add to the table
								$eventTypes[$row['KeywordID']] = $row['Word'];

							}
						}

					}
				}

				// print the links

				foreach( $eventTypes as $key => $el) {
					$returnString .= "		<a class=\"" .$key. "\"> " .$el. "</a>\r\n";
				}

			}

			return $returnString;
		}


		/**
		*@param  a result from a query on the EXHIBITIONS table
		*/
		protected function makeHTMLElements($result){

			if(!$result){
				// handle error
			}else{

				// build the exhibition artworks table query
				if($this->exhibitionArtworksQuery = queryReference('EXHIBITION_ARTWORKS', 'ExhibitionID', $result['ExhibitionID'])){
					//everything is good!
				}

				// build the videos query
				if($this->exhibitionVideoQuery = queryReference('EXHIBITION_VIDEOS', 'ExhibitionID', $result['ExhibitionID'])){
					//everything is gravy!
				}

				// build the events query
				if($this->exhibitionEventsQuery = queryReference('EXHIBITION_EVENTS', 'ExhibitionID', $result['ExhibitionID'])){

					$count = 0;
					// loop through exhibitionEvent query and get the matching event
					foreach($this->exhibitionEventsQuery as $row){
						// add to eventQuery
						if($this->eventQuery[$count] = queryReference('EVENT', 'EventID', $row['EventID'])){

							$count++;
						}
						
					}

				}

				// build main image artist name (by querying ARTWORK with artworkReferenceNo in the EXHIBITION table)
				if($this->artworkQuery = queryReference('ARTWORK', 'ArtworkID', $result['ArtworkReferenceNo'])){

					// check if artist_artwork table gets referenced
					if($this->artistArtworkQuery = queryReference('ARTIST_ARTWORKS', 'ArtworkID', $this->artworkQuery[0]['ArtworkID'])){

						// build the artistNames string
						$this->artistNames ="";

						// loop through the artwork query
						foreach($this->artistArtworkQuery as $artist){

							// query the ARTIST table with corresponding ID
							if($this->artistsQuery = queryReference('ARTIST', 'ArtistID', $artist['ArtistID'])){

								// build name and concatenate each return string
								$this->artistNames .= buildArtistName($this->artistsQuery[0]);

							}
						}

					}
				}

				//build gallery
				if ($this->gallery = queryReference('GALLERY', 'GalleryID', $result['GalleryReferenceNo'])[0]['NickName']){
					// then everything is grand!
				}

				// build the html img element for the main image
				if(!$this->artworkQuery[0]['ImgFilePath']){
				//handle error
				} else {
					$alt = $this->artistNames. " " .$this->artworkQuery[0]['Title'];
					$this->primaryImage=$this->toImg("exhibition-page-images", $this->artworkQuery[0]['ImgFilePath'], $alt, "primaryImageOfPage" );			
				
					$this->imagePath = $GLOBALS['rootDirectory']. "/images/exhibition-page-images/" .$this->artworkQuery[0]['ImgFilePath'];
				}

				// buld tombstone
				$this->tombstone = $this->buildTombstone($this->artistNames, $this->artworkQuery[0], FALSE);

				//body content
				if(!$result['BodyContent']){
					//handle error
				} else {
					$this->bodyContent = nl2p($result['BodyContent']);
				}
				//build title
				if($result['Title']){ $this->title = nl2br($result['Title']);}

				//build start date and end date
				if($result['StartDate'] && $result['EndDate']){ 
					$this->startDate = date('F d, Y', strtotime($result['StartDate']));
					$this->endDate = date('F d, Y', strtotime($result['EndDate']));
				}

				// build the artist bios section
				if($this->artistBiosQuery = queryReference('EXHIBITION_ARTISTS', 'ExhibitionID', $result['ExhibitionID'])){
					foreach($this->artistBiosQuery as $artist){
						$info = queryReference('ARTIST', 'ArtistID', $artist['ArtistID']);
						array_push($this->artistBios, $info[0]);
					}

				}				
			}
		}
	}

	class IndexPage extends Page{

		private $currentExhibitons = array();
		private $collectionArtwork = array();
		private $currentExhibitionArtwork = array();
		private $slides = array();
		private $miniCTAs = array();
		private $currentExhibitionMiniCTAs = array();
		private $specialAnnouncement;
		private $randomCollectionIndexes;
		private $randomCurrentExhibitionIndex;
		
		// number of images to display from our collection on the front page
		private $numberOfCollectionImages = 5;
		
		function __construct(){

			//first, query the db
			$result = queryFrontPageArtwork();

			$this->HTMLheader('The New Britain Museum of American Art');
			$this->makeHTMLElements($result);
			$this->makeBody();
			$this->HTMLfooter();
		}

		protected function makeBody(){ // INDEX PAGE function

			// write the pageID in the parent class
			$this->pageID = "index";

			// wrapper
			echo "		<div id=\"index\">\r\n";
			
			// header section
			$this->nav();

			//main section

			$this->makeSingleSlide();
			?>
				
				<!-- slider commented out for single slide grand opening announcement 
				<div id="slider">

					<?php 
				
					// count is used for the logo bg color.js
					//$count =0;
					//foreach($this->randomCollectionIndexes as $i){
						//$this->printSlide($count, $this->collectionArtwork[$i]['artwork'], $this->collectionArtwork[$i]['artists'], $this->miniCTAs[rand(0, count($this->miniCTAs)-1)]);
						//$count++;
					//}

					//$this->printSlide(5, $this->currentExhibitionArtwork[$this->randomCurrentExhibitionIndex]['artwork'], $this->currentExhibitionArtwork[$this->randomCurrentExhibitionIndex]['artists'], $this->currentExhibitionMiniCTAs[$this->randomCurrentExhibitionIndex]); 

					?>

					

				</div><!-- end slider -->

			<?php	
			echo "</div><!-- end index -->\r\n";
			
		}

		protected function makeSingleSlide(){
			?>
				<!-- logo bg -->
				<img style="display:none;" id="logoBg" src="http://www.nbmaa.org/images/front-page-images/janet-lage-trashed-dyslexic-love.jpg?<?php  echo microtime() ?>">

				<div id="communityDayNutcracker">
					<div class="wrapper">
						
						<div id="communityDayNutcrackerText">
							<p>Panel Discussion</p>
							<h3>An Evening With... Director of Five Points Gallery Judith McElhone</h3>
							<p style="margin-top:10px;">Thursday, Feb. 11 | 5:30-8:00 p.m.</p>
							<a id="communityDayButton" href="http://www.nbmaa.org/event/panel-discussion-with-director-of-five-points-gallery-judith-mcelhone">Find out more</a>
							<div id="communityDayNutcrackerEvent1">
								<p>Wed@1</p>
								<h3>Nor’Easter Gallery Talk with Exhibiting Artists</h3>
								<p>Jan. 13, Jan.20, &amp; Jan. 28</p>
							</div>
							<a id="communityDayButton" href="http://www.nbmaa.org/event/noreaster-gallery-talk-with-exhibiting-artists">Find out more</a>
							<p style="font-size:.7em;padding:0 8px 0 0;border-top: 1px solid #ffffff;">Background artwork: First place winner: Janet Lage, <i>Trashed-Dyslexic Love,</i> Oil, graphite, ink on linen, 22 x 30 in.</p>
						</div>
						
					</div>
				</div>

				<script>

					$(document).ready(function() { 

						$('footer').css('top', 0);

					});

				</script>

			<?php
		}

		protected function makeSubNav(){ // INDEX PAGE function

		}

		protected function makeHTMLElements($result){ // INDEX PAGE function

			$this->specialAnnouncement = "

							<a href=\"" .$GLOBALS['rootDirectory']. "/classroom/family-programs\">
								<div class=\"infoContent\">
									<div class=\"left\">
										<h2>Studio Class Members</h2>
										<p>Due to construction, all studio classes have temporarily been moved from our studio to the Lindquist Building</p>
									</div>
									<div class=\"right\">
										<i class=\"fa fa-hand-o-up\"></i><p>Find out more about the move</p>
									</div>
									<div class=\"clear\"></div>
								</div>
							</a>

			";

			if(!$result){
				// handle error
			}else{

				// build $collectionArtwork array
				foreach($result as $art){	array_push($this->collectionArtwork, queryArtworkArtistInfo($art)); }

				// build $currentExhibitionArtwork and  $currentExhibitionMiniCTAs
				if($this->currentExhibitons = queryFrontPageExhibitions()){
					
					// use query
					foreach($this->currentExhibitons as $art){	

						// for storing the next query
						$artworkArtistInfo="";

						if($artworkArtistInfo = queryArtworkArtistInfo($art)){

							//append exhibition-page-images folder name for exhibition images
							$artworkArtistInfo['artwork'][0]['ImgFilePath'] = "exhibition-page-images/" .$artworkArtistInfo['artwork'][0]['ImgFilePath']. "";
							
							// push final product onto array
							array_push($this->currentExhibitionArtwork, $artworkArtistInfo );
						}
					}

					// build $currentExhibitionMiniCTAs
					foreach($this->currentExhibitons as $exhibition){
						array_push($this->currentExhibitionMiniCTAs, array(
							"icon" => "<img src=\"" .$GLOBALS['rootDirectory']. "/images/misc/building-white.png\">",
							"text" => "<p>Want to see more?<br><i>" .$exhibition['Title']. "</i></p>",
							"link" => $GLOBALS['rootDirectory']. "/exhibition/" .$exhibition['Link'],
							"target" => "_self"
							)
						);
					}
				}

				// build the randomCollectionIndexes array
				for($i = 0; $i < $this->numberOfCollectionImages; $i++ ){
					$this->randomCollectionIndexes[$i] = rand( 0, (count($this->collectionArtwork)-1));
				}

				// select a random currentCollectionImage
				$this->randomCurrentExhibitionIndex = rand(0, count($this->currentExhibitionArtwork)-1);

				//build $miniCTA's
				$this->miniCTAs = array(

					0 => array(
						"icon" => "<img src=\"" .$GLOBALS['rootDirectory']. "/images/misc/building-white.png\">",
						"text" => "<p>Planning a wedding or event?<br>Rent the Museum</p>",
						"link" => "" .$GLOBALS['rootDirectory']. "/museum-of-american-art/rent-the-museum",
						"target" => "_self"
					),


					1 => array(
						"icon" => "<i class=\"fa fa-facebook-official\"></i>",
						"text" => "<p>Like our page?<br>Follow us on facebook</p>",
						"link" => "https://www.facebook.com/NBMAA",
						"target" => "_blank"
					),

					2 => array(
						"icon" => "<i class=\"fa fa-envelope-o\"></i>",
						"text" => "<p>Curious about upcoming events?<br>Join our e-mail list</p>",
						"link" => "http://eepurl.com/bgHJt1",
						"target" => "_blank"
					),

					3 => array(
						"icon" => "<i class=\"fa fa-users\"></i>",
						"text" => "<p>Visit us often?<br>Become a member</p>",
						"link" => "" .$GLOBALS['rootDirectory']. "/museum-of-american-art/membership",
						"target" => "_self"
					),

					4 => array(
						"icon" => "<img src=\"" .$GLOBALS['rootDirectory']. "/images/misc/building-white.png\">",
						"text" => "<p>Want to make a donation?<br>Support the Art &amp; Education Expansion Project</p>",
						"link" => "http://www.nbmaa.org/expansion/index.html",
						"target" => "_self"
					)
				);


			}
		}

		protected function printSlide($sliderindex, $artwork, $artists, $cta){ // INDEX PAGE function

			$tombstone = $this->buildTombstone($artists, $artwork[0], TRUE);

		 ?>
					<div class="slide" style="background-image:url('images/<?php echo $artwork[0]['ImgFilePath'] ?>');">
						<img style="display:none;" id="logoBg<?php echo $sliderindex ?>" src="images/<?php echo $artwork[0]['ImgFilePath']. "?" .microtime() ?>">
						<div class="info">

							<a href="<?php echo $cta['link'] ?>" target ="<?php echo $cta['target'] ?>">
								<div class="infoContent">
									<div class="left">
										<h2>New Britain Museum of American Art</h2>
										<p><?php echo $tombstone ?></p>
									</div>
									<div class="right">
										<?php echo $cta['icon'] ?>
										<?php echo $cta['text'] ?>
									</div>
									<div class="clear"></div>
								</div>
							</a>

							<?php echo $this->specialAnnouncement; ?>

						</div>
					</div>
		<?php
		}
	}

?>