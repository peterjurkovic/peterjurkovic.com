<?php include_once dirname(__FILE__).'/inc/init.php'; ?>
<!DOCTYPE HTML>
<html>
	<head>
		<title><?php  echo $meta["title_${lang}"]; ?></title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="description" content="<?php  echo $meta["header_${lang}"]; ?>" />
		<meta name="keywords" content="java developer, Android developer, Peter Jurkovic, freelance" />
		<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0"/>
		<meta name="robots"   content="index,follow"/>

		<meta property="og:title" content="<?php  echo $meta["title_${lang}"]; ?>" />
		<meta property="og:site_name" content="www.peterjurkovic.com" />
		<meta property="og:type" content="website" />
		<meta property="og:url" content="http://www.peterjurkovic.com" />
		<meta property="og:description"  content="<?php  echo $meta["header_${lang}"]; ?>" /> 
		<meta property="og:image" content="http://www.peterjurkovic.com/img/sc.png" />
		<meta property="fb:app_id"  content="148300471890790" /> 
		
		<link rel="author" href="https://plus.google.com/115805190454646553254/about"  title="Peter Jurkovič"/>
		<link rel="shortcut icon" href="/img/icon.png" />
		<link rel="stylesheet" href="css/style.css" />

		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
		<script src="js/jquery.transit.min.js"></script>
		<script src="js/scripts.min.js"></script>
		<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
		  ga('create', 'UA-7418246-1', 'peterjurkovic.com');ga('send', 'pageview');
		</script>
	</head>
	<body class="homepage remodal-bg" data-lang="<?php echo $lang; ?>">
		
		<!-- NAVIGATION / SOCIAL -->
		<nav>
			<div class="page-wrapp">
				<h1 id="logo">
					<span class="blind">Web, mobile, JavaEE developer Peter Jurkovič</span>
				</h1>
				<ul class="pj-social">
					<li>
						<a class="pj-github" title="Peter Jurkovic's GitHub" target="_blank" href="https://github.com/peterjurkovic">
							<span class="circle"></span>
						</a>
					</li>
					<li>
						<a class="pj-facebook" target="_blank" title="Peter Jurkovic's Facebook" href="https://www.facebook.com/peter.jurkovic">
							<span class="circle"></span>
						</a>
					</li>
					<li>	
						<a class="pj-plus" target="_blank" title="Peter Jurkovic's Google plus" href="https://plus.google.com/u/0/+PeterJurkovi%C4%8D1/posts">
							<span class="circle"></span>
						</a>
					</li>
					<li>
						<a class="pj-linkedin" title="Peter Jurkovic's LinkedIn" target="_blank" href="http://www.linkedin.com/in/peterjurkovic">
							<span class="circle"></span>
						</a>
					</li>
				</ul>
			</div>
		</nav>

		<div class="pj-border"></div>

		<!-- ABOUT / SKILLS -->
		<section id="about">
			<div class="page-wrapp clear">
				<div class="pj-text">
					<strong class="pj-title">
						<span class="left-bg"></span>
						<span><span class="web">W</span>eb 
						<span class="pj-font-small">&&</span>
						<span class="mobile">M</span>obile 
						<span class="pj-font-small">&&</span> Java<span class="java">EE</span></span>
						<span class="pj-block">Developer</span>
					</strong>
					<?php printContent(); ?>
				</div>
				<div class="pj-skills">
					<div id="skill-wrapp">
						<span>SKILLS</span>
						<?php echo printSkills(); ?>
					</div>
				</div>	
			</div>
		</section>

		<div class="pj-border"></div>
		
		<!-- PROJECTS -->
		<section id="projects">
			<div class="page-wrapp">
				<h2><?php echo printPageTitle(34); ?></h2>
				
				<div id="pj-project-wrapp" class="pj-projects">
					<?php echo printProjects(); ?>
				</div>
				<div class="clearfix"></div>
				<a href="" class="pj-asyncload" data-lang="<?php echo $lang; ?>">Load other</a>
			</div>
		</section>


		<div class="pj-border"></div>


		<!-- CONTACT -->
		<section id="contact">
			<div class="page-wrapp">
				<h2><?php echo printPageTitle(48); ?></h2>

				<div class="clear">
					<?php echo printContent(48); ?>
				</div>

			</div>
		</section>	

		
		<!-- FOOTER -->
		<footer>
			<div class="page-wrapp">
				<p>&copy; Peter Jurkovič 2014</p>
				<div id="pj-share">
					<div class="addthis_toolbox addthis_default_style addthis_32x32_style">
					<a class="addthis_button_facebook"></a>
					<a class="addthis_button_twitter"></a>
					<a class="addthis_button_linkedin"></a>
					<a class="addthis_button_google_plusone_badge"></a>
					<a class="addthis_button_compact"></a>
					<a class="addthis_counter addthis_bubble_style"></a>
					</div>
					<script type="text/javascript">var addthis_config = {"data_track_addressbar":true};</script>
					<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-4f7805435b56a706"></script>
				
				</div>
			</div>
		</footer>
		
		
	</body>
</html>