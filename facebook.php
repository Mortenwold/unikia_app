<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Unikia Facebook</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="images/unikiaicon.ico">
        <link href="CSS/bootstrap.min.css" rel="stylesheet">
        <link href="navbar-top-fixed.css" rel="stylesheet">
        <link href="CSS/facebook.css" rel="stylesheet">
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
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="analyticsdashboard.php">Google Analytics</a>
                    </li>
                    <li class="nav-item dropdown active">
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
        <div id ="facebook">
            <?php
            require_once __DIR__ . '/src/Facebook/autoload.php';

            $fb = new Facebook\Facebook([
                'app_id' => '232799497183653',
                'app_secret' => '462516f7993b1c50e81e4cb438a6c8b9',
                'default_graph_version' => 'v2.5'
            ]);

            $helper = $fb->getRedirectLoginHelper();

            // app directory could be anything but website URL must match the URL given in the developers.facebook.com/apps
            define('APP_URL', 'http://localhost/unikia_app/index.php');
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
                echo '<h1>Facebook Archive</h1>';
                $showtoday = new DateTime();
                $showtodayFormat = $showtoday->format('d-m-Y');
                $todayDatePrint = $showtodayFormat;
                //     echo 'Today is '.$showtodayFormat;

                $showDateDayStart = new DateTime();
                $showDateDayStart = $showDateDayStart->modify('-7 days');
                $showDateFormatDayStart = $showDateDayStart->format('d');
                $showDateMonthStart = new DateTime();
                $showDateMonthStart = $showDateMonthStart->modify('-7 days');
                $showDateFormatMonthStart = $showDateMonthStart->format('m');
                $showDateYearStart = new DateTime();
                $showDateYearStart = $showDateYearStart->modify('-7 days');
                $showDateFormatYearStart = $showDateYearStart->format('Y');

                $showDateDayEnd = new DateTime();
                $showDateDayEnd = $showDateDayEnd->modify('+1 days');
                $showDateFormatDayEnd = $showDateDayEnd->format('d');
                $showDateMonthEnd = new DateTime();
                $showDateMonthEnd = $showDateMonthEnd->modify('+1 days');
                $showDateFormatMonthEnd = $showDateMonthEnd->format('m');
                $showDateYearEnd = new DateTime();
                $showDateYearEnd = $showDateYearEnd->modify('+1 days');
                $showDateFormatYearEnd = $showDateYearEnd->format('Y');

                // For info text
                $showDateStartDefault = $showDateDayStart->format('d-m-Y');
                $showDateEndDefault = $showtoday->modify('+1 day');
                $showDateEndDefault = $showDateEndDefault->format('d-m-Y');
                ?>
                <div class="scaleZoom">
                    <form action="" method ="post">
                        <table class="searchSetupTable" border ="2">
                            <th colspan="3">  Start Date (Default: <?php echo $showDateStartDefault ?> - 7 Days ago) </th> 
                            <tr><td class="searchSetupTd" > <select id="yearStart" name="yearStart">                      
                                        <option value="<?php echo $showDateFormatYearStart ?>">--Select Year--</option>
                                        <option value="2016">2016</option>
                                        <option value="2017">2017</option>
                                        <option value="2018">2018</option>
                                        <option value="2019">2019</option>
                                        <option value="2020">2020</option>
                                        <option value="2021">2021</option>
                                        <option value="2022">2022</option>
                                    </select> </td>
                                <td class="searchSetupTd" >  <select id="monthStart" name="monthStart">                      
                                        <option value="<?php echo $showDateFormatMonthStart ?>">--Select Month--</option>
                                        <option value="01">January</option>
                                        <option value="02">February</option>
                                        <option value="03">March</option>
                                        <option value="04">April</option>
                                        <option value="05">May</option>
                                        <option value="06">June</option>
                                        <option value="07">Juli</option>
                                        <option value="08">August</option>
                                        <option value="09">September</option>
                                        <option value="10">October</option>
                                        <option value="11">November</option>
                                        <option value="12">December</option>
                                    </select> </td>
                                <td class="searchSetupTd" >  <select id="dayStart" name="dayStart">                      
                                        <option value="<?php echo $showDateFormatDayStart ?>">--Select Day--</option>
                                        <option value="01">1</option>
                                        <option value="02">2</option>
                                        <option value="03">3</option>
                                        <option value="04">4</option>
                                        <option value="05">5</option>
                                        <option value="06">6</option>
                                        <option value="07">7</option>
                                        <option value="08">8</option>
                                        <option value="09">9</option>
                                        <option value="10">10</option>
                                        <option value="11">11</option>
                                        <option value="12">12</option>
                                        <option value="13">13</option>
                                        <option value="14">14</option>
                                        <option value="15">15</option>
                                        <option value="16">16</option>
                                        <option value="17">17</option>
                                        <option value="18">18</option>
                                        <option value="19">19</option>
                                        <option value="20">20</option>
                                        <option value="21">21</option>
                                        <option value="22">22</option>
                                        <option value="23">23</option>
                                        <option value="24">24</option>
                                        <option value="25">25</option>
                                        <option value="26">26</option>
                                        <option value="27">27</option>
                                        <option value="28">28</option>
                                        <option value="29">29</option>
                                        <option value="30">30</option>
                                        <option value="31">31</option>
                                    </select>
                                </td></tr>
                            <tr><th colspan="3"> End Date (Default: <?php echo $showDateEndDefault ?> - Tomorrow)
                                </th></tr>
                            <tr> <td class="searchSetupTd" >
                                    <select id="yearEnd" name="yearEnd">                      
                                        <option value="<?php echo $showDateFormatYearEnd ?>">--Select Year--</option>
                                        <option value="2016">2016</option>
                                        <option value="2017">2017</option>
                                        <option value="2018">2018</option>
                                        <option value="2019">2019</option>
                                        <option value="2020">2020</option>
                                        <option value="2021">2021</option>
                                        <option value="2022">2022</option>
                                    </select> </td>
                                <td class="searchSetupTd" >  <select id="monthEnd" name="monthEnd">                      
                                        <option value="<?php echo $showDateFormatMonthEnd ?>">--Select Month--</option>
                                        <option value="01">January</option>
                                        <option value="02">February</option>
                                        <option value="03">March</option>
                                        <option value="04">April</option>
                                        <option value="05">May</option>
                                        <option value="06">June</option>
                                        <option value="07">Juli</option>
                                        <option value="08">August</option>
                                        <option value="09">September</option>
                                        <option value="10">October</option>
                                        <option value="11">November</option>
                                        <option value="12">December</option>
                                    </select> </td>

                                <td class="searchSetupTd" >  <select id="dayEnd" name="dayEnd">                      
                                        <option value="<?php echo $showDateFormatDayEnd ?>">--Select Day--</option> 
                                        <option value="01">1</option>
                                        <option value="02">2</option>
                                        <option value="03">3</option>
                                        <option value="04">4</option>
                                        <option value="05">5</option>
                                        <option value="06">6</option>
                                        <option value="07">7</option>
                                        <option value="08">8</option>
                                        <option value="09">9</option>
                                        <option value="10">10</option>
                                        <option value="11">11</option>
                                        <option value="12">12</option>
                                        <option value="13">13</option>
                                        <option value="14">14</option>
                                        <option value="15">15</option>
                                        <option value="16">16</option>
                                        <option value="17">17</option>
                                        <option value="18">18</option>
                                        <option value="19">19</option>
                                        <option value="20">20</option>
                                        <option value="21">21</option>
                                        <option value="22">22</option>
                                        <option value="23">23</option>
                                        <option value="24">24</option>
                                        <option value="25">25</option>
                                        <option value="26">26</option>
                                        <option value="27">27</option>
                                        <option value="28">28</option>
                                        <option value="29">29</option>
                                        <option value="30">30</option>
                                        <option value="31">31</option>
                                    </select> </td></tr>
                            <tr><th>  
                                    Post limit (Default: 5)
                                </th> <th colspan="2"> Select Facebook (Default: UnikiaNorge) </th></tr>
                            <tr><td class="searchSetupTd" > <select id="searchLimit" name="searchLimit">                      
                                        <option value="5">--Select Limit--</option>  
                                        <option value="1">1</option>
                                        <option value="5">5</option>
                                        <option value="10">10</option>
                                        <option value="15">15</option>
                                        <option value="20">20</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                    </select> </td>
                                <td class="searchSetupTd" colspan="2"> <select id="pageSelect" name="pageSelect">                      
                                        <option value="Unikianorge">--Select Facebook--</option>  
                                        <option value="Unikianorge">UnikiaNorge</option>
                                        <option value="Unikiainnovation">UnikiaInnovation</option>
                                        <option value="Barnasdesignlab">Barnas Designlab</option>
                                    </select> </td></tr>
                            <tr><td  class="searchSetupTd" >
                                    <input id ="buttonScale" class ="btn btn-secondary" type="submit" name="Search" value="Search" /> </td>
                                <td  colspan="2" class="searchSetupTd" >
                                    <image id ="buttoninfo"  
                                           src="images/infob.png" title='Start date includes itself, but the end date does not.
The system will require over 1 minute if you pick 25-50 posts as the limit.' />

                                </td></tr>
                        </table>  
                    </form>
                </div>
                <?php
                if (isset($_REQUEST["Search"])) {
                    $yearStart = $_POST["yearStart"];
                    $monthStart = $_POST["monthStart"];
                    $dayStart = $_POST["dayStart"];

                    $yearEnd = $_POST["yearEnd"];
                    $monthEnd = $_POST["monthEnd"];
                    $dayEnd = $_POST["dayEnd"];

                    $searchLimit = $_POST["searchLimit"];
                    $pageSelect = $_POST["pageSelect"];

                    $startDate = $dayStart . '-' . $monthStart . '-' . $yearStart;
                    $endDate = $dayEnd . '-' . $monthEnd . '-' . $yearEnd;

                    $getDateRange = $fb->get($pageSelect . '/posts?since=' . $startDate . '&until=' . $endDate . '&fields=created_time,message,likes.limit(0)&limit=' . $searchLimit);
                    $getDateRange = $getDateRange->getGraphEdge()->asArray();
                    $counter = 1;
                    $dateRangeLikes = 0;
                    $dateRangeComments = 0;
                    $dateRangeShares = 0;
                    $message = "";

                    echo '<div class="scaleZoom">';
                    echo '<table class="searchTable" border="2"> 
                           <th colspan="5" id="searchHeader">' . $pageSelect . '  (' . $todayDatePrint . ' to ' . $startDate . ') </th><th class="thMessage" rowspan="2">Message </th>
                           <tr> <th>Limit: ' . $searchLimit . '</th><th>Date</th><th>Likes</th><th>Comments</th><th>Shares</th> </tr>';
                    set_time_limit(0);
                    foreach ($getDateRange as $key) {
                        if (isset($key['id'])) {
                            $date = $key['created_time'];
                            $dateformat = $date->format('d-m-Y');
                            $likesResponse = $fb->get('/' . $key['id'] . '/likes?limit=0&summary=true');
                            $getLikeCount = $likesResponse->getGraphEdge();
                            $currentLikeCount = $getLikeCount->getTotalCount();
                            $dateRangeLikes += $currentLikeCount;
                            $linkAddress = 'http://www.facebook.com/' . $key['id'];

                            if (isset($key['message']) && $key['message']) {
                                $message = $key['message'];
                            } else {
                                $message = 'No message';
                            }
                            $commentsResponse = $fb->get('/' . $key['id'] . '/comments?limit=0&summary=true');
                            $getCommentsCount = $commentsResponse->getGraphEdge();
                            $currentCommentCount = $getCommentsCount->getTotalCount();
                            $dateRangeComments += $currentCommentCount;

                            $sharesResponse = $fb->get('/' . $key['id'] . '?fields=shares');
                            $sharesResponse = $sharesResponse->getGraphNode()->asArray();
                            if (isset($sharesResponse["shares"]["count"])) {
                                $sharesCount = $sharesResponse["shares"]["count"];
                                $dateRangeShares += $sharesCount;
                            } else {
                                $sharesCount = 0;
                            }
                            $linkPost = 'Post ' . $counter;
                            echo '<tr><td id ="linkTd">';
                            echo "<a href='" . $linkAddress . "'>" . $linkPost . "</a>";
                            echo '</td><td class="dateTd"y>' . $dateformat . '</td><td class="likesSettings">' . $currentLikeCount . '</td><td class="commentsSettings">' . $currentCommentCount .
                            '</td><td class="sharesSettings">' . $sharesCount . '</td><td class="messageSettings">' . $message . '</td></tr>';
                            $counter++;
                        }
                    }
                    if ($counter < 2) {
                        echo '<tr><td colspan="6">No posts were made during this period.</td></tr>';
                        echo '</table>';
                    } else {
                        echo '<tr><td id ="linkTd" colspan="2">Total</td><td class="likesSettings">' . $dateRangeLikes . '</td>'
                        . '<td class="commentsSettings">' . $dateRangeComments . '</td><td class="sharesSettings">' . $dateRangeShares . '</td>'
                        . '<td></td></tr>';
                        echo '<tr><td colspan="6" id="linkTd">' . $pageSelect . '</td></tr>';
                        echo '</table>';
                    }
                    echo '</div>';
                }
            } else {
                // replace your website URL same as added in the developers.facebook.com/apps e.g. if you used http instead of https and you used non-www version or www version of your website then you must add the same here
                $loginUrl = $helper->getLoginUrl(APP_URL, $permissions);
                echo '<a id="linkBlackColor" href="' . $loginUrl . '">Log in with Facebook!</a>';
            }
            ?>

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
    </script>
</body>
</html>
