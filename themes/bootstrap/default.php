<!DOCTYPE html>
<html lang="en">
<head>
<title><?=@container('title')?></title>

<!-- HTML5 shim, for IE6-8 support of HTML elements -->
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<link href="css://bootstrap.css" rel="stylesheet">
<link href="css://theme.css" rel="stylesheet">
<script src="js://jquery.js"></script>
<script src="js://tabs.js"></script>
</head>

<body>

    <header class="topbar">
        <div class="fill">
            <div class="container">
                <h3><a class="brand" href="index.php">Shyfted</a></h3>
                <nav>
                    <ul class="nav">
                        <li class="active"><a href="index.php">Home</a></li>
                        <li><a href="index.php?com=pages">Pages</a></li>
                        <li><a href="index.php?com=widgets">Widgets</a></li>
                        <li><a href="index.php?com=content">Contents</a></li>
                        <li><a href="index.php?com=forms">Forms</a></li>
                    </ul>
                </nav>

                <form action="" class="pull-right">
                    <input class="input-small" type="text" placeholder="Username">
                    <input class="input-small" type="password" placeholder="Password">
                    <button class="btn" type="submit">Sign in</button>
                </form>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="content">
            <div class="page-header">
                <h1>Page name <small>Supporting text or tagline</small></h1>
            </div>
            <div class="row">
                <div class="span4">
                    <?=@container('left', 'wrapper')?>
                </div>
                <div class="span10">
                    <?=@container('page')?>
                </div>
            </div>
        </div>


        <footer>
            <p>&copy; 2011 Israel D. Canasa</p>
        </footer>
    </div><!--/container-->

</body>
</html>
