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
		<link rel="author" href="https://plus.google.com/115805190454646553254/about"  title="Peter Jurkovič"/>
		<link rel="shortcut icon" href="/img/icon.png" />
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
		<script src="js/jquery.transit.min.js"></script>
		<script src="js/scripts.js"></script>
		<link rel="stylesheet" href="css/style.css" />
	</head>
	<body class="homepage">
		
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
					<!--
					<p>
						My name is Peter<span class="blind"> Jurkovič</span>, I am 26 years old and I am a web, mobile and javaEE developer from Slovakia. Ever since I was a child I have been interested in web technologies. Last three years I’ve been  focusing on Java platform. In my free time I play with  Android SDK.
					</p>
					-->
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
				
				<div class="pj-projects">
					<?php echo printProjects(); ?>
				</div>
				<div class="clearfix"></div>
			
			</div>
		</section>


		<div class="pj-border"></div>

		<section id="contact">
			<div class="page-wrapp">
				<h2>Contact</h2>

				<div class="clear">
					<div class="pj-box">
						<ul>
							<li><span>name:</span><strong><span class="blind">java developer</span> Peter Jurkovič</strong></li>
							<li><span>email:</span><a href="" class="pj-email"></a></li>
							<li><span>phone:</span><strong class="pj-phone">+421 904 938 419</strong></li>
						</ul>
					</div>
					<div class="pj-box">
						<p>You can either shoot me an email using this form:</p>
						<form>
							<input type="text" placeholder="name" />
							<input type="text" placeholder="e-mail" />
							<textarea placeholder="message"></textarea>
							<a href="#send">send</a>
						</form>
					</div>
				</div>

			</div>
		</section>	

		

		<footer>
			<div class="page-wrapp">
				<p>&copy; Peter Jurkovič 2014</p>

				<div id="pj-share">
					<!-- AddThis Button BEGIN -->
				<!--
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
				-->
				</div>
			</div>
		</footer>
	</body>
</html>