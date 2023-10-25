<!DOCTYPE html>
<html>
<head>
<title><?= $title ?></title>
<meta name = 'viewport' content = 'width = device-width, initial-scale = 1'>
<meta http-equiv = 'Content-Type' content = 'text/html;
charset = utf-8' />
<script type = 'application/x-javascript'> addEventListener( 'load', function() {
    setTimeout( hideURLbar, 0 );
}
, false );

function hideURLbar() {
    window.scrollTo( 0, 1 );
}
</script>
<!-- Custom Theme files -->
<link href = 'style.css' rel = 'stylesheet' type = 'text/css' media = 'all' />
<!-- //Custom Theme files -->
<!-- web font -->
<link href = '//fonts.googleapis.com/css?family = Roboto:300, 300i, 400, 400i, 700, 700i' rel = 'stylesheet'>
<!-- //web font -->
</head>
<body>
	<!-- main -->
	<div class="main-w3layouts wrapper">
		<h1><?= $title ?></h1>
		<div class="main-agileinfo">
		<?php if($erreur): ?>
        	<p id="kli"><?= $erreur ?></p>
    	 <?php endif; ?>
		  <?php if($succes): ?>
        	<p id="erreur"><?= $succes ?></p>
    	 <?php endif; ?>
			<div class="agileits-top">