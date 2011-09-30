<!DOCTYPE html>
<html lang="en">
<head>
<title><?=@container('title')?></title>

<!-- HTML5 shim, for IE6-8 support of HTML elements -->
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<link href="css://less/bootstrap.less" rel="stylesheet/less">
<link href="css://theme.css" rel="stylesheet">
<script src="js://less.js"></script>
<script src="js://jquery.js"></script>
<script src="js://tabs.js"></script>
<script src="js://dropdown.js"></script>
</head>
<body>
    <header class="topbar">
        <div class="fill">
            <div class="container">
                <h3><a class="brand" href="index.php">Shyfted</a></h3>

                <?=@container('top-navigation')?>

                <form action="" class="pull-right">
                    <input class="input-small" type="text" placeholder="Username">
                    <input class="input-small" type="password" placeholder="Password">
                    <button class="btn" type="submit">Sign in</button>
                </form>
            </div>
        </div>
    </header>

    <div class="container layout">

        <!--layout-->
        <div class="content">
            <div class="page-header">
                <h1>Page name <small>Supporting text or tagline</small></h1>
            </div>
            <div class="row">
                <div class="span3">
                    <?=@container('left', 'wrapper')?>
                </div>
                <div class="span13">
                    <?=@container('page')?>
                </div>
            </div>
        </div>
        <!--/endlayout-->

        <footer>
            <p>&copy; 2011 Israel D. Canasa</p>
        </footer>

    </div>

</body>
</html>
