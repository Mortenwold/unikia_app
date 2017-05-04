<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="favicon.ico">

        <title>Fixed top navbar example for Bootstrap</title>

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
                    <li class="nav-item">
                        <a class="nav-link" href="facebookone.php">Facebook</a>
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


                        echo 'Facebook for Barnas Designlab<br><br>';

                        echo 'Total likes Barnas Designlab: ';
                        $getTotalLikesBarna = $fb->get('barnasdesignlab?fields=fan_count');
                        $getTotalLikesBarna = $getTotalLikesBarna->getGraphNode()->asArray();

                        echo $getTotalLikesBarna['fan_count'] . '<br><br>';

                        echo '<br>Latest Barnas Designlab Post<br>';
                        $howManyPosts = 1; // ADD A BUTTON (DROPDOWN???) FOR USER
                        $getLatestPost = $fb->get('barnasdesignlab/posts?likes.limit(0)&limit=' . $howManyPosts);
                        $getLatestPost = $getLatestPost->getGraphEdge()->asArray();

                        foreach ($getLatestPost as $key) {
                            if (isset($key['id'])) {
                                $post = $key['id'];
                                $date = $key['created_time'];
                                $dateformat = $date->format('d-m-Y');
                                echo 'Date: ' . $dateformat . ' - ';
                                $linkAddress = 'http://www.facebook.com/' . $post;
                                echo "<a href='" . $linkAddress . "'>Link</a>";

                                $likesResponse = $fb->get('/' . $key['id'] . '/likes?limit=0&summary=true');
                                $getLikeCount = $likesResponse->getGraphEdge();
                                $currentLikeCount = $getLikeCount->getTotalCount();
                                echo ' - Likes: ' . $currentLikeCount;

                                if (isset($key['message']) && $key['message']) {
                                    echo '<br>Message: ' . $key['message'] . '<br><br>';
                                } else {
                                    echo '<br>No message<br><br>';
                                }
                            }
                        }


                        // getting likes data of recent 100 posts by user
                        $getPostsLikes = $fb->get('/barnasdesignlab/posts?fields=likes.limit(1000),message,created_time&limit=100');
                        $getPostsLikes = $getPostsLikes->getGraphEdge()->asArray();
                        // printing likes data as per requirements
                        $mostLikes = 0;
                        $bestPost = 0;
                        $counterFirstCheck = 0;
                        $counterSecondCheck = 0;
                        $bestPostMessage = '';
                        $bestPostLink = '';
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
                                            $bestPostMessage = 'Message: ' . $key['message'] . '<br><br>';
                                        } else {
                                            $bestPostMessage = 'No message<br><br>';
                                        }
                                    }
                                }
                            }
                        }
                        echo 'Post with the most likes (last 100 posts):<br>';
                        echo 'ID: ' . $bestPost . '<br>';
                        echo 'Date: ' . $bestPostDatePrint . '<br>';
                        echo "<a href='" . $bestPostLink . "'>Link</a>" . ' - Likes: ' . $counterSecondCheck;
                        echo '<br>' . $bestPostMessage;


                        /* $teller = 0;
                          foreach ($getPostsLikes as $key) {
                          $teller++;
                          if (isset($key['likes'])) {
                          $counter = count($key['likes']);
                          echo count($key['likes']) . '<br>';

                          //      foreach ($key['likes'] as $key) {
                          //             echo $key['name'] . '<br>';
                          //   }
                          }
                          }
                         */

                        echo '<br>Post with the most comments (last 100 posts):<br>';
                        $getComments = $fb->get('barnasdesignlab/posts?fields=comments.summary(true),created_time,message,likes.limit(0)&limit=100');
                        $getComments = $getComments->getGraphEdge()->asArray();

                        $largestCommentCount = 0;
                        $postWithMostComments = false;
                        $dateMostComments = 0;
                        $mostCommentsID = 0;
                        $mostCommentsMessage = '';

                        foreach ($getComments as $post) {
                            if (isset($post['comments']) && count($post['comments']) > $largestCommentCount) {
                                $postWithMostComments = $post;
                                $date = $post['created_time'];
                                $dateMostComments = $date->format('d-m-Y');
                                $mostCommentsID = $post['id'];
                                $largestCommentCount = count($post['comments']);
                                $mostCommentsMessage = $post['message'];
                            }
                        }
                        if ($postWithMostComments !== false) {
                            echo 'ID: ' . $postWithMostComments['id'] . '<br>';
                            echo 'Date: ' . $dateMostComments;
                            $linkAddress = 'http://www.facebook.com/' . $postWithMostComments['id'];
                            echo "<br><a href='" . $linkAddress . "'>Link</a>" . ' - Likes: ';

                            $likesResponse = $fb->get('/' . $mostCommentsID . '/likes?limit=0&summary=true');
                            $getLikeCount = $likesResponse->getGraphEdge();
                            $currentLikeCount = $getLikeCount->getTotalCount();

                            echo $currentLikeCount;
                            echo '<br>Comments: ' . $largestCommentCount . '<br>';
                            echo 'Message: ' . $mostCommentsMessage;
                        } else {
                            echo '<br>There is no post with more than 0 comments<br>';
                        }
                        echo '<br><br>';

                        /*
                          $bestPost;
                          $counter = 0;
                          $teller = 0;
                          $response = $fb->get('/unikianorge/posts?fields=likes.limit(0).summary(true)&limit=50');
                          $getPostID = $response->getGraphEdge()->asArray();
                          foreach($getPostID as $IDKey){
                          if(isset($IDKey['id'])){
                          $teller++;
                          $currentPostID = $IDKey['id'];
                          $likesResponse = $fb->get('/'.$currentPostID.'/likes?limit=0&summary=true');
                          //   echo $currentPostID . '<br>'; //optional
                          $getLikeCount = $likesResponse->getGraphEdge();
                          $currentLikeCount = $getLikeCount->getTotalCount();
                          if($currentLikeCount > $counter)
                          {
                          $bestPost = $IDKey['id'];
                          $counter = $currentLikeCount;
                          echo "New best: ".$currentLikeCount . '<br>';
                          }
                          //    echo $currentLikeCount . '<br>';
                          }
                          }
                          $response2 = $fb->get('/unikianorge/posts?fields=likes.limit(0).summary(true)&limit=50&offset=50');
                          $getPostID2 = $response2->getGraphEdge()->asArray();
                          foreach($getPostID2 as $IDKey){
                          if(isset($IDKey['id'])){
                          $teller++;
                          $currentPostID = $IDKey['id'];
                          $likesResponse = $fb->get('/'.$currentPostID.'/likes?limit=0&summary=true');
                          //   echo $currentPostID . '<br>'; //optional
                          $getLikeCount = $likesResponse->getGraphEdge();
                          $currentLikeCount = $getLikeCount->getTotalCount();
                          if($currentLikeCount > $counter)
                          {
                          $bestPost = $IDKey['id'];
                          $counter = $currentLikeCount;
                          echo "New best: ".$currentLikeCount . '<br>';
                          }
                          // echo $currentLikeCount . '<br>';
                          }
                          }




                          echo '<br><br>Best post ID: '.$bestPost;
                          echo '<br>'.$teller;
                          echo '<br><br>';
                         */
                        // GET POSTS WITHIN THE LAST 7 DAYS




                        echo '<br>Total likes last 7 days';

                        $today = new DateTime();
                        $todayPrint = $today->format('d-m-Y');
                        $today = $today->modify('+1 days');
                        $todayFormat = $today->format('Y-m-d');

                        $sevenDays = $today->modify('-8 days');
                        $sevenDaysFormat = $sevenDays->format('Y-m-d');
                        $sevenDaysPrint = $sevenDays->format('d-m-Y');

                        $getCreatedTime = $fb->get('designlab/posts?since=' . $sevenDaysFormat . '&until=' . $todayFormat . '&fields=likes.limit(0),created_time');
                        $getCreatedTime = $getCreatedTime->getGraphEdge()->asArray();


                        echo '<br>Today: ' . $todayPrint . ' - 7 days ago: ' . $sevenDaysPrint . '<br><br>';
                        $sevenDaysLikes = 0;
                        $messageCounter = 0;

                        foreach ($getCreatedTime as $key) {
                            if (isset($key['created_time'])) {
                                $date = $key['created_time'];
                                $dateformat = $date->format('d-m-Y');
                                $likesResponse = $fb->get('/' . $key['id'] . '/likes?limit=0&summary=true');
                                $getLikeCount = $likesResponse->getGraphEdge();
                                $currentLikeCount = $getLikeCount->getTotalCount();
                                $sevenDaysLikes += $currentLikeCount;
                                echo 'Post made ' . $dateformat . ' with ' . $currentLikeCount . ' likes<br>';
                                $messageCounter++;
                            }
                        }
                        if ($messageCounter > 0) {
                            echo '<br>Total likes for this period: ' . $sevenDaysLikes . '<br><br><br>';
                        } else {
                            echo 'No posts were made during this period.<br><br><br>';
                        }

                        /*
                          echo '<br><br>';
                          echo 'bilder <br><br>'; */

                        // getting likes data of recent 100 photos by user
                        /*
                          $getPhotosLikes = $fb->get('/unikianorge/photos?fields=likes.limit(10){name,id}&limit=10&type=uploaded');
                          $getPhotosLikes = $getPhotosLikes->getGraphEdge()->asArray();
                          // printing likes data as per requirements
                          foreach ($getPhotosLikes as $key) {
                          if (isset($key['likes'])) {
                          echo count($key['likes']) . '<br>';
                          foreach ($key['likes'] as $key) {
                          echo $key['name'] . '<br>';
                          }
                          }
                          } */
                        // Now you can redirect to another page and use the access token from $_SESSION['facebook_access_token']
                    } else {
                        // replace your website URL same as added in the developers.facebook.com/apps e.g. if you used http instead of https and you used non-www version or www version of your website then you must add the same here
                        $loginUrl = $helper->getLoginUrl(APP_URL, $permissions);
                        echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
                    }
                    ?>

                    <form action="facebookone.php" method ="post">
                        <br><br>
                        <input type="submit" name="pageone" value="Unikia Norge" />
                        <br>
                    </form>
                    <form action="facebooktwo.php" method ="post">
                        <br><br>
                        <input type="submit" name="pagetwo" value="Unikia Innovation" />
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
