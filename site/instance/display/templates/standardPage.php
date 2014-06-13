<? Display::getPageLogic($thisTemplate)?>
<!DOCTYPE html>
<html xml:lang="en" lang="en">
	<head>
		<title><?=$page->headTitle ? $page->headTitle : end(RequestHandler::$urlTokens)?></title>
		<meta content="text/html; charset=UTF-8"/>
		<meta name="description" content="<?=$page->description?>"/>
		<meta name="keywords" content="<?=$page->keywords?>"/>
		<link rel="icon" type="image/png" href="/public/img/favicon.png">
		<?=Display::getCss(array('modDate'=>$page->resourceModDate))?>
		<!-- Thoughtpush js object variable used by tp js tools-->
		<script type="text/javascript">var tp = {};</script>
		<?=Display::getJson()?>
		<?=Display::getTopJs(array('modDate'=>$page->resourceModDate))?>
	</head>
	<body id="<?=implode('-',RequestHandler::$urlTokens)?>-body" class="<?=$page->section?>">
		<div id="header">
			<div id="headerLogo">
				<a href="/">
					<img id="headerImg" src="/public/img/logo.png"/>
				</a>
			</div>
			<div id="topSideLinks">
				<a href="/entity/search">Search</a> |
<?	if($_SESSION['userId']){?>
				<a href="/user/">User Home</a> | <a href="/user/logout">Log Out</a>
<?	}else{?>
				<a href="/user/login">Login</a> | <a href="/user/signup">Sign Up</a>
<?	}?>
			</div>
		</div>
		<div id="body">
			<div id="content">
				<div id="defaultMsgBox" class="msgBox"></div>
				<?=$input?>
			</div>
		</div>
		<div id="footer">
			<div>
				&copy; Deemit | <a href="/about">About</a> | <a href="/user/tou">Terms of Use</a>
			</div>
		</div>
		<?=Display::getBottomJs(array('modDate'=>$page->resourceModDate))?>
	</body>
</html>
