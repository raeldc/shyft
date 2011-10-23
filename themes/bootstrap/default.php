<!DOCTYPE html>
<html lang="en">
<head>
<title>Shyfted - Cutting Edge CMS for Modern Websites</title>

<!-- HTML5 shim, for IE6-8 support of HTML elements -->
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<link href="css://bootstrap.css" rel="stylesheet">
<link href="css://theme.css" rel="stylesheet">
<script src="base://media/lib_koowa/js/mootools.js"></script>
<script src="base://media/lib_koowa/js/koowa.js"></script>
<script src="js://jquery.js"></script>
<script src="js://tabs.js"></script>
<script src="js://dropdown.js"></script>
<script src="js://modal.js"></script>
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
            <div class="row">
                <div class="span4">
                    <?=@container('left', 'wrapper')?>
                </div>
                <div class="span12">
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
