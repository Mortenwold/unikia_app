<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="favicon.ico">

        <title>Unikia Facebook</title>

        <!-- Bootstrap core CSS -->
        <link href="CSS/bootstrap.min.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="navbar-top-fixed.css" rel="stylesheet">

        <link href="CSS/facebook.css" rel="stylesheet">
    </head>

    <body>
        <?php
        session_start();
        if (!$_SESSION["login"]) {
            Header("location: login.php");
        }
        ?>
        <nav class="navbar navbar-toggleable-md navbar-inverse fixed-top bg-inverse">
            <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <img id="unikiaicon" src="images/unikia-link.png">
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="index.html">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="analyticsdashboard.html">Google Analytics</a>
                    </li>
                    <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="http://example.com" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Dropdown</a>
            <div class="dropdown-menu" aria-labelledby="dropdown01">
              <a class="dropdown-item" href="#">Action</a>
              <a class="dropdown-item" href="#">Another action</a>
              <a class="dropdown-item" href="#">Something else here</a>
            </div>
          </li>
                </ul>
            </div>
        </nav>
        <div id ="facebook">
            <!DOCTYPE html>
            <!--
                To change this license header, choose License Headers in Project Properties.
            To change this template file, choose Tools | Templates
            and open the template in the editor.
            -->
            <html>
                <head>
                    <meta charset="UTF-8">
                    <title></title>
                </head>
                <body>

                    <?php
                    session_start();
                    require_once __DIR__ . '/src/Facebook/autoload.php';

                    $fb = new Facebook\Facebook([
                        'app_id' => '232799497183653',
                        'app_secret' => '462516f7993b1c50e81e4cb438a6c8b9',
                        'default_graph_version' => 'v2.5'
                    ]);

                    $helper = $fb->getRedirectLoginHelper();

                    // app directory could be anything but website URL must match the URL given in the developers.facebook.com/apps
                    define('APP_URL', 'http://localhost/unikia_app/facebookone.php');
                    $permissions = ['user_posts', 'user_photos']; // optional

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
                        // redirect the user back to the same page if it has "code" GET variable
                        /* if (isset($_GET['code'])) {
                          header('Location: ./');
                          } */
                        // validating user access token
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

                        echo 'Total likes UnikiaNorge: ';
                        $getTotalLikesNorge = $fb->get('Unikianorge?fields=fan_count');
                        $getTotalLikesNorge = $getTotalLikesNorge->getGraphNode()->asArray();

                        echo $getTotalLikesNorge['fan_count'] . '<br>';
                        echo 'DayLikes<br>';
                        echo '7DayLikes<br>';
                        echo '30DayLikes<br>';

                        $howManyPosts = 1; // Can change this number to show more posts
                        $getLatestPost = $fb->get('Unikianorge/posts?likes.limit(0)&limit=' . $howManyPosts);
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
                        echo '<table class="latestPostTable" border="2">
                            <th colspan="2">';
                        echo "<a href='" . $linkAddress . "'>Latest UnikiaNorge Post</a>";
                        echo '</th><th>Message</th>
                            <tr>
                                <td>Date</td>';
                        echo '<td>' . $dateformat . '</td><td rowspan="4">' . $messageLatestPost . '</td>
                            </tr>
                            <tr>
                                <td>Likes</td>';
                        echo'<td class="likesSettings">' . $currentLikeCount . '</td>
                            </tr>
                            <tr>
                                <td >Shares</td>';
                        echo'<td class="sharesSettings">' . $sharesCount . '</td>
                            </tr>
                            <tr>
                                <td>Comments</td>';
                        echo'<td class="commentsSettings">' . $currentCommentCount . '</td>
                            </tr>
                        </table>';

                        $today = new DateTime();
                        $todayPrint = $today->format('d-m-Y');
                        $today = $today->modify('+1 days');
                        $todayFormat = $today->format('Y-m-d');

                        $sevenDays = $today->modify('-8 days');
                        $sevenDaysFormat = $sevenDays->format('Y-m-d');
                        $sevenDaysPrint = $sevenDays->format('d-m-Y');

                        $getCreatedTime = $fb->get('Unikianorge/posts?since=' . $sevenDaysFormat . '&until=' . $todayFormat . '&fields=likes.limit(0),created_time,comments.summary(true)');
                        $getCreatedTime = $getCreatedTime->getGraphEdge()->asArray();

                        $sevenDaysLikes = 0;
                        $sevenDaysComments = 0;
                        $sevenDaysShares = 0;
                        $messageCounter = 0;

                        echo '<br><br>';
                        echo '<table class="summaryTable" border="2"> 
                            <th>Last 7 Days</th><th>Date</th><th>Likes</th><th>Comments</th><th>Shares</th>';

                        foreach ($getCreatedTime as $key) {
                            if (isset($key['created_time'])) {
                                $post = $key['id'];
                                $date = $key['created_time'];
                                $dateformat = $date->format('d-m-Y');

                                $likesResponse = $fb->get('/' . $key['id'] . '/likes?limit=0&summary=true');
                                $getLikeCount = $likesResponse->getGraphEdge();
                                $currentLikeCount = $getLikeCount->getTotalCount();
                                $sevenDaysLikes += $currentLikeCount;

                                $commentsResponse = $fb->get('/' . $key['id'] . '/comments?limit=0&summary=true');
                                $getCommentsCount = $commentsResponse->getGraphEdge();
                                $currentCommentCount = $getCommentsCount->getTotalCount();
                                $sevenDaysComments += $currentCommentCount;

                                $sharesResponse = $fb->get('/' . $key['id'] . '?fields=shares');
                                $sharesResponse = $sharesResponse->getGraphNode()->asArray();
                                if (isset($sharesResponse["shares"]["count"])) {
                                    $sharesCount = $sharesResponse["shares"]["count"];
                                    $sevenDaysShares += $sharesCount;
                                } else {
                                    $sharesCount = 0;
                                }
                                $messageCounter++;
                                $linkAddress = 'http://www.facebook.com/' . $post;
                                $linkPost = 'Post ' . $messageCounter;
                                echo '<tr><td id ="linkTd">';
                                echo "<a href='" . $linkAddress . "'>" . $linkPost . "</a>";
                                echo '</td><td>' . $dateformat . '</td><td class="likesSettings">' . $currentLikeCount . '</td><td class="commentsSettings">' . $currentCommentCount .
                                '</td><td class="sharesSettings">' . $sharesCount . '</td></tr>';
                                // echo 'Post made '.$dateformat.' with '.$currentLikeCount.' likes, '.$currentCommentCount.' comments & '.$sharesCount.' shares<br>';
                            }
                        }
                        if ($messageCounter <= 0) {
                            echo '<tr><td colspan="5">No posts were made during this period.</td></tr>';
                        } else {
                            echo '<tr><td id ="linkTd" colspan="2">Total</td><td class="likesSettings">' . $sevenDaysLikes . '</td>'
                            . '<td class="commentsSettings">' . $sevenDaysComments . '</td><td class="sharesSettings">' . $sevenDaysShares . '</td>'
                            . '</tr>';
                        }
                        echo '</table>';
                        ?>
                        <form action="facebookone.php" method ="post">
                            <br>
                            <input class ="btn btn-secondary" type="submit" name="mostLikes" value="Top Likes & Comments" />
                            <br>
                        </form>
                        <?php
                        if (isset($_REQUEST["mostLikes"])) {
                            $getPostsLikes = $fb->get('/unikianorge/posts?fields=likes.limit(1000),message,created_time&limit=100');
                            $getPostsLikes = $getPostsLikes->getGraphEdge()->asArray();

                            $mostLikes = 0;
                            $bestPost = 0;
                            $counterFirstCheck = 0;
                            $counterSecondCheck = 0;
                            $bestPostMessage = '';
                            $bestPostLink = '';
                            set_time_limit(0);
                            foreach ($getPostsLikes as $key) {
                                if (isset($key['likes'])) {
                                    $counterFirstCheck = count($key['likes']);
                                    if ($counterFirstCheck > $mostLikes) {
                                        $mostLikes = $counterFirstCheck;
                                        $currentPostID = $key['id'];
                                        $likesResponse = $fb->get('/' . $currentPostID . '/likes?limit=0&summary=true');
                                        $getLikeCount = $likesResponse->getGraphEdge();
                                        $currentLikeCount = $getLikeCount->getTotalCount();
                                        if ($currentLikeCount > $counterSecondCheck) {
                                            $bestPost = $key['id'];
                                            $counterSecondCheck = $currentLikeCount;
                                            $bestPostLink = 'http://www.facebook.com/' . $bestPost;
                                            $bestPostDate = $key['created_time'];
                                            $bestPostDatePrint = $bestPostDate->format('d-m-Y');

                                            if (isset($key['message']) && $key['message']) {
                                                $bestPostMessage = $key['message'];
                                            } else {
                                                $bestPostMessage = 'No message';
                                            }
                                        }
                                    }
                                }
                            }
                            if ($bestPost != 0) {
                                $sharesMostLikes = $fb->get('/' . $bestPost . '?fields=shares');
                                $sharesMostLikes = $sharesMostLikes->getGraphNode()->asArray();
                                if (isset($sharesMostLikes["shares"]["count"])) {
                                    $sharesCount = $sharesMostLikes["shares"]["count"];
                                } else {
                                    $sharesCount = 0;
                                }
                                $commentsMostLikes = $fb->get('/' . $bestPost . '/comments?limit=0&summary=true');
                                $getCommentsCount = $commentsMostLikes->getGraphEdge();
                                $currentCommentCount = $getCommentsCount->getTotalCount();
                            }
                            echo '<table class="likesTable" border="2">
                            <th colspan="2">';
                            echo "<a href='" . $bestPostLink . "'>Most liked post</a>";
                            echo '</th><th>Message</th>
                            <tr>
                                <td>Date</td>';
                            echo '<td>' . $bestPostDatePrint . '</td><td rowspan="4">' . $bestPostMessage . '</td>
                            </tr>
                            <tr>
                                <td>Likes</td>';
                            echo'<td class="likesSettings">' . $counterSecondCheck . '</td>
                            </tr>
                            <tr>
                                <td >Shares</td>';
                            echo'<td class="sharesSettings">' . $sharesCount . '</td>
                            </tr>
                            <tr>
                                <td>Comments</td>';
                            echo'<td class="commentsSettings">' . $currentCommentCount . '</td>
                            </tr>
                        </table>';

                            $getComments = $fb->get('Unikianorge/posts?fields=comments.summary(true),created_time,message,likes.limit(0)&limit=100');
                            $getComments = $getComments->getGraphEdge()->asArray();

                            $largestCommentCount = 0;
                            $postWithMostComments = false;
                            $dateMostComments = 0;
                            $mostCommentsID = 0;
                            $mostCommentsMessage = '';
                            set_time_limit(0);
                            foreach ($getComments as $post) {
                                if (count($post['comments']) >= $largestCommentCount || count($post['comments']) == 25) {
                                    $commentsResponse = $fb->get('/' . $post['id'] . '/comments?limit=0&summary=true');
                                    $getCommentsCount = $commentsResponse->getGraphEdge();
                                    $currentCommentCount = $getCommentsCount->getTotalCount();
                                }

                                if (isset($post['comments']) && $currentCommentCount > $largestCommentCount) {
                                    $postWithMostComments = $post;
                                    $date = $post['created_time'];
                                    $dateMostComments = $date->format('d-m-Y');
                                    $mostCommentsID = $post['id'];
                                    $largestCommentCount = $currentCommentCount;
                                    $mostCommentsMessage = $post['message'];
                                }
                            }
                            if ($postWithMostComments !== false) {
                                $linkAddress = 'http://www.facebook.com/' . $postWithMostComments['id'];

                                $likesResponse = $fb->get('/' . $mostCommentsID . '/likes?limit=0&summary=true');
                                $getLikeCount = $likesResponse->getGraphEdge();
                                $currentLikeCount = $getLikeCount->getTotalCount();

                                $sharesMostComments = $fb->get('/' . $mostCommentsID . '?fields=shares');
                                $sharesMostComments = $sharesMostComments->getGraphNode()->asArray();
                                if (isset($sharesMostComments["shares"]["count"])) {
                                    $sharesCount = $sharesMostComments["shares"]["count"];
                                } else {
                                    $sharesCount = 0;
                                }
                            } else {
                                $mostCommentsMessage = 'There is no post with more than 0 comments';
                                $dateMostComments = 'None';
                                $currentLikeCount = '0';
                                $sharesCount = '0';
                                $largestCommentCount = '0';
                            }

                            echo '<table class="likesTable" border="2">
                            <th colspan="2">';
                            echo "<a href='" . $linkAddress . "'>Most commented post</a>";
                            echo '</th><th>Message</th>
                            <tr>
                                <td>Date</td>';
                            echo '<td>' . $dateMostComments . '</td><td rowspan="4">' . $mostCommentsMessage . '</td>
                            </tr>
                            <tr>
                                <td>Likes</td>';
                            echo'<td class="likesSettings">' . $currentLikeCount . '</td>
                            </tr>
                            <tr>
                                <td >Shares</td>';
                            echo'<td class="sharesSettings">' . $sharesCount . '</td>
                            </tr>
                            <tr>
                                <td>Comments</td>';
                            echo'<td class="commentsSettings">' . $largestCommentCount . '</td>
                            </tr>
                        </table>';
                        }
                        // Now you can redirect to another page and use the access token from $_SESSION['facebook_access_token']
                    } else {
                        // replace your website URL same as added in the developers.facebook.com/apps e.g. if you used http instead of https and you used non-www version or www version of your website then you must add the same here
                        $loginUrl = $helper->getLoginUrl(APP_URL, $permissions);
                        echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
                    }
                    ?>


                    <form action="facebooktwo.php" method ="post">
                        <br><br>
                        <input type="submit" name="pagetwo" value="Unikia Innovation" />
                        <br>
                    </form>
                    <form action="facebookthree.php" method ="post">
                        <br><br>
                        <input type="submit" name="pagethree" value="Barnas Designlab" />
                        <br>
                    </form>
                    <form action="facebook.php" method ="post">
                        <br><br>
                        <input type="submit" name="FB_search" value="FB Search" />
                        <br>
                    </form>
                    <?php /*
                      $logoutUrl = $helper->getLogoutUrl(APP_URL, $permissions);
                      echo '<br><br><br><br>';
                      echo '<a href="' . $logoutUrl . '">Logout of Facebook!</a>';
                      echo '<br>Use this link if you are logged in to the wrong Facebook account (without access to this website). Change to the correct account and try again!'
                     */
                    ?> 

                </body>
            </html>

        </div>

        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
        <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
        <script src="../../dist/js/bootstrap.min.js"></script>
        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>
    </body>
</html>
