<!DOCTYPE html>
<html lang="en">

    <head>
        <title>Unikia Dashboard</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="images/unikiaicon.ico">
        <link href="CSS/bootstrap.min.css" rel="stylesheet">
        <link href="CSS/index.css" rel="stylesheet">
        <script src="javascript/analytics_functions.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

        <style>
            #dvLoading{
                background:#000 url(unikia_loading.gif) no-repeat center center;
                position: fixed;
                left: 0px;
                top: 0px;
                width: 100%;
                height: 100%;
                z-index: 9999;
                opacity: 0.9;
                background-color: #fff;
            }
        </style>
    </head>

    <body>
        <?php
        session_start();
        if (!$_SESSION["login"]) {
            Header("location: login.php");
        }
        ?>
        <div id="dvLoading"></div>
        <nav class="navbar navbar-toggleable-md navbar-inverse fixed-top bg-inverse">
            <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <img id="unikiaicon" src="images/unikia-link.png">
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="analyticsdashboard.php">Google Analytics</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Facebook</a>
                        <div class="dropdown-menu" aria-labelledby="dropdown01">
                            <a class="dropdown-item" id ="menuLinks" href="facebookone.php">UnikiaNorge</a>
                            <a class="dropdown-item" id="menuLinks" href="facebooktwo.php">UnikiaInnovation</a>
                            <a class="dropdown-item" id="menuLinks" href="facebookthree.php">Barnas Designlab</a>
                            <a class="dropdown-item" id="menuLinks" href="facebook.php">Facebook Archive</a>
                        </div>
                    </li>
                    <?php
                    if ($_SESSION["admin"]) {
                        ?>
                        <li class="nav-item">
                            <a class="nav-link" href="admin.php">Admin</a>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
        </nav>
        <script>
            (function (w, d, s, g, js, fs) {
                g = w.gapi || (w.gapi = {});
                g.analytics = {q: [], ready: function (f) {
                        this.q.push(f);
                    }};
                js = d.createElement(s);
                fs = d.getElementsByTagName(s)[0];
                js.src = 'https://apis.google.com/js/platform.js';
                fs.parentNode.insertBefore(js, fs);
                js.onload = function () {
                    g.load('analytics');
                };
            }(window, document, 'script'));

            var id;
            $(window).resize(function () {
                clearTimeout(id);
                id = setTimeout(doneResizing, 500);

            });


            var windowSize = $(window).width();

            function doneResizing() {
                windowSize = $(window).width();

                skriv_graf(windowSize);
            }
        </script>
        <div id="main">
            <div id="analyticsPart">
                <div id="embed-api-auth-container"></div>
                <p class="text-muted">Number of pageviews for www.Unikia.no</p>

                <div id="view-selector-container"></div>
                <div id="data-chart-1-container"></div>
                <div id="date-range-selector-1-container"></div>

                <div id="data-chart-2-container"></div>
                <div id="date-range-selector-2-container"></div>

                <div id="view-name"></div>

                <script src="javascript/view-selector2.js"></script>
                <script src="javascript/active-users.js"></script>
                <script src="javascript/date-range-selector.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
                <link rel="stylesheet" href="chartjs-visualizations.css">
                <script>
            skriv_graf(windowSize);
                </script>
            </div>
        </div>

        <div id="facebooksection">
            <?php
            require_once __DIR__ . '/src/Facebook/autoload.php';

            $fb = new Facebook\Facebook([
                'app_id' => '232799497183653',
                'app_secret' => '462516f7993b1c50e81e4cb438a6c8b9',
                'default_graph_version' => 'v2.5'
            ]);
            $helper = $fb->getRedirectLoginHelper();
            define('APP_URL', 'http://www.unikiadashboard.com');
            $permissions = ['user_posts', 'user_photos'];

            try {
                if (isset($_SESSION['facebook_access_token'])) {
                    $accessToken = $_SESSION['facebook_access_token'];
                } else {
                    $accessToken = $helper->getAccessToken();
                }
            } catch (Facebook\Exceptions\FacebookResponseException $e) {
                // When Graph returns an error
                echo 'Graph returned an error: ' . $e->getMessage();
                exit;
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                // When validation fails or other local issues
                echo 'Facebook SDK returned an error: ' . $e->getMessage();
                exit;
            }
            if (isset($accessToken)) {
                if (isset($_SESSION['facebook_access_token'])) {
                    $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
                } else {
                    // getting short-lived access token
                    $_SESSION['facebook_access_token'] = (string) $accessToken;
                    // OAuth 2.0 client handler
                    $oAuth2Client = $fb->getOAuth2Client();
                    // Exchanges a short-lived access token for a long-lived one
                    $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);
                    $_SESSION['facebook_access_token'] = (string) $longLivedAccessToken;
                    // setting default access token to be used in script
                    $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
                }
                try {
                    $user = $fb->get('/me');
                    $user = $user->getGraphNode()->asArray();
                } catch (Facebook\Exceptions\FacebookResponseException $e) {
                    // When Graph returns an error
                    echo 'Graph returned an error: ' . $e->getMessage();
                    session_destroy();
                    // if access token is invalid or expired you can simply redirect to login page using header() function
                    exit;
                } catch (Facebook\Exceptions\FacebookSDKException $e) {
                    // When validation fails or other local issues
                    echo 'Facebook SDK returned an error: ' . $e->getMessage();
                    exit;
                }

                $howManyPosts = 1; 
                $getLatestPost = $fb->get('unikianorge/posts?likes.limit(0)&limit=' . $howManyPosts);
                $getLatestPost = $getLatestPost->getGraphEdge()->asArray();

                foreach ($getLatestPost as $key) {
                    if (isset($key['id'])) {
                        $post = $key['id'];
                        $date = $key['created_time'];
                        $dateformat = $date->format('d-m-Y');
                        $linkAddress = 'http://www.facebook.com/' . $post;
                        
                        $likesResponse = $fb->get('/' . $key['id'] . '/likes?limit=0&summary=true');
                        $getLikeCount = $likesResponse->getGraphEdge();
                        $currentLikeCount = $getLikeCount->getTotalCount();
                        
                        $sharesLastPost = $fb->get('/' . $post . '?fields=shares');
                        $sharesLastPost = $sharesLastPost->getGraphNode()->asArray();
                        if (isset($sharesLastPost["shares"]["count"])) {
                            $sharesCount = $sharesLastPost["shares"]["count"];
                        } else {
                            $sharesCount = 0;
                        }
                        $commentsMostLikes = $fb->get('/' . $post . '/comments?limit=0&summary=true');
                        $getCommentsCount = $commentsMostLikes->getGraphEdge();
                        $currentCommentCount = $getCommentsCount->getTotalCount();

                        if (isset($key['message']) && $key['message']) {
                            $messageLatestPost = $key['message'];
                        } else {
                            $messageLatestPost = "No Message";
                        }
                    }
                }

                echo '<div class="scaleZoom">';
                echo '<table class="latestPostTable" border="2">';
                echo '<th colspan="2">';
                echo "<a href='" . $linkAddress . "' id='whiteLink'>Latest UnikiaNorge Post</a>";
                echo '</th><th>Message</th><tr><td>Date</td>';
                echo '<td>' . $dateformat . '</td><td rowspan="4">' . $messageLatestPost . '</td>';
                echo '</tr><tr><td>Likes</td>';
                echo '<td class="likesSettings">' . $currentLikeCount . '</td>';
                echo '</tr><tr><td >Shares</td>';
                echo'<td class="sharesSettings">' . $sharesCount . '</td>';
                echo '</tr><tr><td>Comments</td>';
                echo'<td class="commentsSettings">' . $currentCommentCount . '</td>';
                echo '</tr></table>';
                echo '</div>';

                $getTotalLikesNorge = $fb->get('unikianorge?fields=fan_count');
                $getTotalLikesNorge = $getTotalLikesNorge->getGraphNode()->asArray();
                $likesNorge = $getTotalLikesNorge['fan_count'];

                $getTotalLikesInnovation = $fb->get('unikiainnovation?fields=fan_count');
                $getTotalLikesInnovation = $getTotalLikesInnovation->getGraphNode()->asArray();

                $likesInnovation = $getTotalLikesInnovation['fan_count'];

                $getTotalLikesBarnas = $fb->get('barnasdesignlab?fields=fan_count');
                $getTotalLikesBarnas = $getTotalLikesBarnas->getGraphNode()->asArray();

                $likesBarnasdesignlab = $getTotalLikesBarnas['fan_count'];
                $totalLikesFb = $likesNorge + $likesInnovation + $likesBarnasdesignlab;
                echo '<div id="Likes">';
                echo '<table class="countryTable"border ="2">';
                echo '<th id="thTotalLikes">Facebook</th><th id="thTotalLikes">Likes</th>';
                echo '<tr><td>UnikiaNorge</td><td class="likesSettings">' . $likesNorge . '</td></tr>';
                echo '<tr><td>UnikiaInnovation</td><td class="likesSettings">' . $likesInnovation . '</td></tr>';
                echo '<tr><td>Barnas Designlab</td><td class="likesSettings">' . $likesBarnasdesignlab . '</td></tr>';
                echo '<tr><td>Total Likes</td><td class="likesSettings">' . $totalLikesFb . '</td></tr>';
                echo '</table>';
                echo '</div>';
            } else {
                $loginUrl = $helper->getLoginUrl('www.unikiadashboard.com/index.php', $permissions);
                echo '<a id="linkBlackColor" href="' . $loginUrl . '">Log in with Facebook!</a>';
            }
            ?>
        </div>
        <div id="twittersection">
            <a class="twitter-timeline" data-height="20rem" data-chrome="nofooter, noheader" href="https://twitter.com/unikiadotcom"></a>
            <script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>
        </div>

        <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
        <script>window.jQuery || document.write('<script src="javascript/jquery.min.js"><\/script>')</script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
        <script src="javascript/bootstrap.min.js"></script>
        <script src="javascript/ie10-viewport-bug-workaround.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <script type="text/javascript">
            $(window).load(function () {
                $("#dvLoading").hide();
            });
        </script>
    </body>
</html>