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

    <nav class="navbar navbar-toggleable-md navbar-inverse fixed-top bg-inverse">
      <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <a class="navbar-brand" href="#">Fixed navbar</a>
      <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item active">
            <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Link</a>
          </li>
          <li class="nav-item">
            <a class="nav-link disabled" href="#">Disabled</a>
          </li>
        </ul>
        <form class="form-inline mt-2 mt-md-0">
          <input class="form-control mr-sm-2" type="text" placeholder="Search">
          <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
      </div>
    </nav>
      <div id ="facebook">
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
        define('APP_URL', 'http://localhost/unikia_app/facebooktwo.php');
        $permissions = ['user_posts', 'user_photos']; // optional

        try {
                if (isset($_SESSION['facebook_access_token'])) {
                        $accessToken = $_SESSION['facebook_access_token'];
                } else {
                        $accessToken = $helper->getAccessToken();
                }
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
                // When Graph returns an error
                echo 'Graph returned an error: ' . $e->getMessage();
                exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
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
            //    if (isset($_GET['code'])) {
              //          header('Location: ./');
              //  }
                // validating user access token
                try {
                        $user = $fb->get('/me');
                        $user = $user->getGraphNode()->asArray();
                } catch(Facebook\Exceptions\FacebookResponseException $e) {
                        // When Graph returns an error
                        echo 'Graph returned an error: ' . $e->getMessage();
                        session_destroy();
                        // if access token is invalid or expired you can simply redirect to login page using header() function
                        exit;
                } catch(Facebook\Exceptions\FacebookSDKException $e) {
                        // When validation fails or other local issues
                        echo 'Facebook SDK returned an error: ' . $e->getMessage();
                        exit;
                }

                echo 'Latest UnikiaNorge Post<br>';
                $howManyPosts = 1; // ADD A BUTTON (DROPDOWN???) FOR USER
                $getLatestPost = $fb->get('Unikianorge/posts?likes.limit(0)&limit='.$howManyPosts);
                $getLatestPost = $getLatestPost->getGraphEdge()->asArray();
                
                foreach ($getLatestPost as $key) {
                    if (isset($key['id'])) {
                        $post = $key['id'];
                        $date = $key['created_time'];
                        $dateformat = $date->format('d-m-Y');
                        echo 'Latest post ('.$dateformat.'): ';
                        $linkAddress = 'http://www.facebook.com/'.$post;
                        echo "<a href='".$linkAddress."'>Link</a>";
                        
                        $likesResponse = $fb->get('/'.$key['id'].'/likes?limit=0&summary=true');
                        $getLikeCount = $likesResponse->getGraphEdge();
                        $currentLikeCount = $getLikeCount->getTotalCount();
                        echo ' - Likes: '.$currentLikeCount;
                        
                        if (isset($key['message']) && $key['message']) {
                            echo '<br>Message: '.$key['message'].'<br><br>'; 
                        }
                        else   {
                            echo 'No message<br><br>';
                        }
                                            }
                }
                 echo '<br>Latest UnikiaInnovation Post<br>';
                $howManyPosts = 1; // ADD A BUTTON (DROPDOWN???) FOR USER
                $getLatestPost = $fb->get('Unikiainnovation/posts?likes.limit(0)&limit='.$howManyPosts);
                $getLatestPost = $getLatestPost->getGraphEdge()->asArray();
                
                foreach ($getLatestPost as $key) {
                    if (isset($key['id'])) {
                        $post = $key['id'];
                        $date = $key['created_time'];
                        $dateformat = $date->format('d-m-Y');
                        echo 'Latest post ('.$dateformat.'): ';
                        $linkAddress = 'http://www.facebook.com/'.$post;
                        echo "<a href='".$linkAddress."'>Link</a>";
                        
                        $likesResponse = $fb->get('/'.$key['id'].'/likes?limit=0&summary=true');
                        $getLikeCount = $likesResponse->getGraphEdge();
                        $currentLikeCount = $getLikeCount->getTotalCount();
                        echo ' - Likes: '.$currentLikeCount;
                        
                        if (isset($key['message']) && $key['message'])    {
                            echo '<br>Message: '.$key['message'].'<br><br>'; 
                        }
                        else  {
                            echo 'No message<br><br>';
                        } 
                                            }
                } 
                echo '<br>Latest Barnas Designlab Post<br>';
                $howManyPosts = 1; // ADD A BUTTON (DROPDOWN???) FOR USER
                $getLatestPost = $fb->get('barnasdesignlab/posts?likes.limit(0)&limit='.$howManyPosts);
                $getLatestPost = $getLatestPost->getGraphEdge()->asArray();
                
                foreach ($getLatestPost as $key) {
                    if (isset($key['id'])) {
                        $post = $key['id'];
                        $date = $key['created_time'];
                        $dateformat = $date->format('d-m-Y');
                        echo 'Latest post ('.$dateformat.'): ';
                        $linkAddress = 'http://www.facebook.com/'.$post;
                        echo "<a href='".$linkAddress."'>Link</a>";
                        
                        $likesResponse = $fb->get('/'.$key['id'].'/likes?limit=0&summary=true');
                        $getLikeCount = $likesResponse->getGraphEdge();
                        $currentLikeCount = $getLikeCount->getTotalCount();
                        echo ' - Likes: '.$currentLikeCount;
                        
                       if (isset($key['message']) && $key['message'])   {
                            echo '<br>Message: '.$key['message'].'<br><br>'; 
                        }
                        else  {
                            echo '<br>No message<br><br>';
                        }
                                            }
                }
                echo '<br><br>';
                ?>
    <input type='button'  value='Info - hover me' title='Start date includes itself, but the end date does not. 
Example: 2017-01-01 -- 2017-01-10 will print from (including) January 1st to (not including) January 10th' />
                <?php
                echo '<br><br>';
                $showtoday = new DateTime();
                $showtodayFormat = $showtoday->format('d-m-Y');
                echo 'Today is '.$showtodayFormat;
                
                $showDateDayStart = new DateTime(); $showDateDayStart = $showDateDayStart->modify('-7 days'); 
                $showDateFormatDayStart = $showDateDayStart->format('d');
                $showDateMonthStart = new DateTime(); $showDateMonthStart = $showDateMonthStart->modify('-7 days'); 
                $showDateFormatMonthStart = $showDateMonthStart->format('m');
                $showDateYearStart = new DateTime(); $showDateYearStart = $showDateYearStart->modify('-7 days'); 
                $showDateFormatYearStart = $showDateYearStart->format('Y');
                    
                $showDateDayEnd = new DateTime();  $showDateDayEnd = $showDateDayEnd->modify('+1 days');
                $showDateFormatDayEnd = $showDateDayEnd->format('d');
                $showDateMonthEnd= new DateTime(); $showDateMonthEnd = $showDateMonthEnd->modify('+1 days');
                $showDateFormatMonthEnd = $showDateMonthEnd->format('m');
                $showDateYearEnd= new DateTime(); $showDateYearEnd = $showDateYearEnd->modify('+1 days');
                $showDateFormatYearEnd = $showDateYearEnd->format('Y');
                
                // For info text
                $showDateStartDefault = $showDateDayStart->format('d-m-Y');
                $showDateEndDefault = $showtoday->modify('+1 day'); $showDateEndDefault = $showDateEndDefault->format('d-m-Y');
                
                ?>
                <form action="" method ="post">
                     Start Date (Default: <?php echo $showDateStartDefault ?> - 7 Days ago) <br>
                    <select id="yearStart" name="yearStart">                      
                    <option value="<?php echo $showDateFormatYearStart ?>">--Select Year--</option>
                    <option value="2016">2016</option>
                    <option value="2017">2017</option>
                    <option value="2018">2018</option>
                    <option value="2019">2019</option>
                    <option value="2020">2020</option>
                    <option value="2021">2021</option>
                    <option value="2022">2022</option>
                    </select>
                      <select id="monthStart" name="monthStart">                      
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
                    </select>
                      <select id="dayStart" name="dayStart">                      
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
                   
                     <br><br>
                      End Date (Default: <?php echo $showDateEndDefault ?> - Tomorrow)
                      <br>
                   <select id="yearEnd" name="yearEnd">                      
                    <option value="<?php echo $showDateFormatYearEnd ?>">--Select Year--</option>
                    <option value="2016">2016</option>
                    <option value="2017">2017</option>
                    <option value="2018">2018</option>
                    <option value="2019">2019</option>
                    <option value="2020">2020</option>
                    <option value="2021">2021</option>
                    <option value="2022">2022</option>
                    </select>
                      <select id="monthEnd" name="monthEnd">                      
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
                    </select>
                    
                      <select id="dayEnd" name="dayEnd">                      
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
                    </select>
                      <br><br>
                    Post limit (Default: 10)
                    <br>
                    <select id="searchLimit" name="searchLimit">                      
                    <option value="10">--Select Limit--</option>  
                    <option value="1">1</option>
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="20">20</option>
                    <option value="25">25</option>
                    </select>
                    <br><br>
                    Select Facebook (Default UnikiaNorge)
                    <br>
                    <select id="pageSelect" name="pageSelect">                      
                    <option value="Unikianorge">--Select Facebook--</option>  
                    <option value="Unikianorge">UnikiaNorge</option>
                    <option value="Unikiainnovation">UnikiaInnovation</option>
                    <option value="Barnasdesignlab">Barnas Designlab</option>
                    </select>
                    <br><br>
                    <input type="submit" name="Search" value="Search" />
                    <br>
                 </form>
                
                <?php
                 if(isset($_REQUEST["Search"]))
                 {
                   $yearStart = $_POST["yearStart"];
                   $monthStart = $_POST["monthStart"];
                   $dayStart = $_POST["dayStart"];
                   
                   $yearEnd = $_POST["yearEnd"];
                   $monthEnd = $_POST["monthEnd"];
                   $dayEnd = $_POST["dayEnd"];
                   
                   $searchLimit = $_POST["searchLimit"];
                   $pageSelect = $_POST["pageSelect"];
           
                                      
                   echo '<br> Results <br><br>';
                   // GET POSTS IN X RANGE
                   $startDate = $dayStart.'-'.$monthStart.'-'.$yearStart;
                   $endDate = $dayEnd.'-'.$monthEnd.'-'.$yearEnd; 

                   echo 'Posts from '.$startDate.' to '.$endDate.'<br>';
                   echo 'Postlimit: '.$searchLimit.'<br>';
                   echo 'Facebook: '.$pageSelect.'<br><br>';
                   
                   $getDateRange = $fb->get($pageSelect.'/posts?since='.$startDate.'&until='.$endDate.'&fields=created_time,message,likes.limit(0)&limit='.$searchLimit);
                   $getDateRange = $getDateRange->getGraphEdge()->asArray();
                   $counter = 1;
                   $dateRangeLikes = 0;
                
                   foreach ($getDateRange as $key) {
                       if (isset($key['id'])) { 
                           $date = $key['created_time'];
                           $dateformat = $date->format('d-m-Y'); 
                           $likesResponse = $fb->get('/'.$key['id'].'/likes?limit=0&summary=true');
                           $getLikeCount = $likesResponse->getGraphEdge();
                           $currentLikeCount = $getLikeCount->getTotalCount();
                           $dateRangeLikes += $currentLikeCount;
                           $linkAddress = 'http://www.facebook.com/'.$key['id'];
                           echo 'Post '.$counter.' ('.$dateformat.'): '."<a href='".$linkAddress."'>Link</a>".' - likes: '.$currentLikeCount.'<br>';
                           echo 'ID: '.$key['id'].'<br>';
                           if (isset($key['message']) && $key['message'])   {
                                echo 'Message: '.$key['message'].'<br><br>';  
                            }
                           else  {
                                echo 'No message<br><br>';
                           }              
                           $counter++;
                       }                       
                   }
                   if($counter < 2) {
                     echo  'No posts were found in this date range';
                   }
                   else {
                       echo 'Total likes during this period: '.$dateRangeLikes;
                   }

                   echo '<br><br><br>';
                
                 }
                } else {
                // replace your website URL same as added in the developers.facebook.com/apps e.g. if you used http instead of https and you used non-www version or www version of your website then you must add the same here
                $loginUrl = $helper->getLoginUrl(APP_URL, $permissions);
                echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
        }

?>
<form action="facebooktwo.php" method ="post">
            <br><br>
                    <input type="submit" name="back" value="back" />
                    <br>
</form>
       
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
