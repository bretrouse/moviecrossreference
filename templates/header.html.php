<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
	<meta name="description" content="Movie Cross Reference is a site designed to help you reasearch movies. Given two films, two actors, or an actor and a film the site will find the commonalities between them.">
	<meta name="keywords" content="movie cross reference, movie research, actor, movie, film research, movie actor, actor from movie, who that guy">
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
	
	<!-- FACEBOOK -->
	<meta property="og:title" content="<?php echo isset($title) ? $title : "Movie Cross Reference"; ?>">
	<meta property="og:description" content="Given two films, two actors, or an actor and a film the site will find the commonalities between them.">
	<meta property="og:image" content="http://moviexref.com/images/mcr_facebook.jpg">
	<!-- END FACEBOOK -->
		
	<link rel="stylesheet" type="text/css" href="css/reset.css">
	<link rel="stylesheet" type="text/css" href="css/base.css">
	<link rel="stylesheet" type="text/css" href="css/jquery.autocomplete.css">
	
	<!--[if lt IE 8]>
		<link rel="stylesheet" type="text/css" href="css/ie.css">
	<![endif]-->
	
	<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
	
	<script type="text/javascript">
	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-18646888-2']);
	  _gaq.push(['_trackPageview']);

	  (function() {
	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();
	</script>
	
	<title><?php echo isset($title) ? $title : "Movie Cross Reference"; ?></title>
</head>

<body>
	<div id="content" class="container">
		<div id="title">
			Movie Cross Reference
			<div id="what-box">
				<a id="home" href="/"></a>
				<!--<a id="what" href="#what">what is this?</a>-->
			</div>
		</div>
		<div id="search">
			<div id="what-text"<?php echo isset($matches) ? ' style="display:none;"' : ''; ?>>
				<a id="close-what" href="close-what">x</a>
				Ever wonder who that guy was that was in those movies?  Now you can find out.  Enter in two movies and we'll show you who was in both of them.  Give us two people and we'll show you the movies they've done together.  You can even match a person with a movie should you want to find out if they had a role in that film.  Enjoy!
			</div>

