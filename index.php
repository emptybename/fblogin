<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>Home</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">

    <!-- Toastr style -->
    <link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">

    <!-- Gritter -->
    <link href="js/plugins/gritter/jquery.gritter.css" rel="stylesheet">

    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

</head>

<body>
<script>
var t;
var page_token;
var page_id;
  // This is called with the results from from FB.getLoginStatus().
  function statusChangeCallback(response) {
    //console.log('statusChangeCallback');
    //console.log(response.authResponse.accessToken);
    // The response object is returned with a status field that lets the
    // app know the current login status of the person.
    // Full docs on the response object can be found in the documentation
    // for FB.getLoginStatus().
    if (response.status === 'connected') {
        t = response.authResponse.accessToken
        //console.log("**********************************************");
        //console.log(t);
        //$("#table_content").hide();
        $("#after_logout").hide();
        $("#table_content").show();
        $("#after_login").show();
      // Logged into your app and Facebook.
      testAPI();
      get_pages();
    } else if (response.status === 'not_authorized') {
        $("#table_content").hide();
        $("#after_login").hide();
        $("#after_logout").show();
      // The person is logged into Facebook, but not your app.
      document.getElementById('status').innerHTML = 'Please log ' +
        'into this app.';

    } else {
        $("#table_content").hide();
        $("#after_login").hide();
        $("#after_logout").show();
      // The person is not logged into Facebook, so we're not sure if
      // they are logged into this app or not.
      document.getElementById('status').innerHTML = 'Please log ' +
        'into Facebook.';
    }
  }
  function logout(){
    //console.log("******************************");
    FB.getLoginStatus(function(response) {
            if (response.status === 'connected') {
                FB.logout(function(response) {
                    // this part just clears the $_SESSION var
                    // replace with your own code
                    $("#table_content").hide();
                    $("#after_login").hide();
                    $("#after_logout").show();
                });
            }
        });
  }

   function login(){
    FB.login(function(response) {
        statusChangeCallback(response);
    }, {scope: 'pages_show_list, email, user_likes, manage_pages, publish_pages, pages_manage_instant_articles'});
   }

  // This function is called when someone finishes with the Login
  // Button.  See the onlogin handler attached to it in the sample
  // code below.
  function checkLoginState() {
    FB.getLoginStatus(function(response) {
      statusChangeCallback(response);
    });
  }

  window.fbAsyncInit = function() {
  FB.init({
    appId      : '360292527661885',

    cookie     : true,  // enable cookies to allow the server to access 
                        // the session
    xfbml      : true,  // parse social plugins on this page
    version    : 'v2.8' // use graph api version 2.8
  });

  // Now that we've initialized the JavaScript SDK, we call 
  // FB.getLoginStatus().  This function gets the state of the
  // person visiting this page and can return one of three states to
  // the callback you provide.  They can be:
  //
  // 1. Logged into your app ('connected')
  // 2. Logged into Facebook, but not your app ('not_authorized')
  // 3. Not logged into Facebook and can't tell if they are logged into
  //    your app or not.
  //
  // These three cases are handled in the callback function.

  FB.getLoginStatus(function(response) {
    statusChangeCallback(response);
  });


  };

  // Load the SDK asynchronously
  (function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));

  // Here we run a very simple test of the Graph API after login is
  // successful.  See statusChangeCallback() for when this call is made.
  function get_page_data(token, id){
    data = {};
    //console.log(token);
    data['access_token'] =t;


    // if(token.toString() == data['access_token']){
    //     console.log("*********************************************");
    // }
    //console.log(data, id);
    $.ajax({
        type: 'GET',
        url: 'https://graph.facebook.com/v2.8/'+id+'?fields=single_line_address,phone,overall_star_rating,name,id,access_token',
        data: data,
        success: function(response){
            console.log(response);
            $("#page_id").val(response['id']);
            $("#page_token").val(response['access_token']);
            $("#fb_page").html(response['name']);
            $("#fb_phone").html(response['phone']);
            $("#fb_ratings").html(response['overall_star_rating']);
            $("#fb_address").html(response['single_line_address']);
            //$("#form_address").val(response['single_line_address']);
            //$("#form_phone").val(response['phone']);


        }
    })
  }
  function get_pages(){
    FB.api('/me/accounts', function(response) {
        if(false)
            get_page_data(response['data'][1]['access_token'], response['data'][1]['id']);
        else
            get_page_data(response['data'][0]['access_token'], response['data'][0]['id']);
      //   $("#username").html(response.name);
      // console.log('Successful login for: ' + response.name);

      // document.getElementById('status').innerHTML =
      //   'Thanks for logging in, ' + response.name + '!';
    });
  }

  function update_data(){
     var street1 = $("#form_street1").val();
    var street2 = $("#form_street2").val();
    var city = $("#form_city").val();
    var country = $("#form_country").val();
    var phone = $("#form_phone").val();
    var token = $("#page_token").val();
    var id = $("#page_id").val();
    data = {};
    if(phone != ""){
        data['phone'] = phone;
    }
    data['location']={ 'city':city,'country':country,'state':street2,'street':street1};
    data['access_token'] = token;
   // console.log(data);
    $.ajax({
        type: 'POST',
        url: 'https://graph.facebook.com/v2.8/'+id,
        data: data,
        success: function(response){
            alert("save");
            window.location.reload();
            //$("#form_address").val(response['single_line_address']);
            //$("#form_phone").val(response['phone']);


        }
     })

     
  }
  function testAPI() {
    //console.log('Welcome!  Fetching your information.... ');
    FB.api('/me', function(response) {
        //console.log(response);
        $("#username").html(response.name);
     // console.log('Successful login for: ' + response.name);

      // document.getElementById('status').innerHTML =
      //   'Thanks for logging in, ' + response.name + '!';
        //get_pages();
    });

    
  }
</script>


    <div id="wrapper">
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav metismenu" id="side-menu">
                    <li class="nav-header" style=" color:red;
    padding-top: 15px;
    padding-bottom: 10px;
    padding-left: 30px;
">
                        <h3>GROWTH PLUG</h3>
                    </li>

                    <li >
                        <a href="index.html"><i class="fa fa-home"></i> <span class="nav-label">Dashboards</span>
                    </li>
                    <li>
                        <a href="layouts.html"><i class="fa fa-desktop"></i> <span class="nav-label">Website</span></a>
                    </li>
                    

                    <li>
                        <a href="metrics.html"><i class="fa fa-user"></i> <span class="nav-label">Visitors</span>  </a>
                    </li>
                    <li>
                        <a target="_blank" href="landing.html"><i class="fa fa-star"></i> <span class="nav-label">Reviews</span> <!-- <span class="label label-warning pull-right">NEW</span> --></a>
                    </li>
                    <li class="active">
                        <a href="widgets.html"><i class="fa fa-list"></i> <span class="nav-label">Listing</span></a>
                    </li>
                    

                    <li>
                        <a href="grid_options.html"><i class="fa fa-calendar"></i> <span class="nav-label">Appointments</span></a>
                    </li>
                    
                    
                    <li >
                        <a href="package.html"><i class="fa fa-cog"></i> <span class="nav-label">Settings</span></a>
                    </li>
                </ul>

            </div>
        </nav>

        <div id="page-wrapper" class="gray-bg dashbard-1">
        <div class="row border-bottom">
        <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
              
                <h3 style="margin-top:20px;margin-left:15px;">Tobiit Professional Medical Corporation</h3>
            </div>
            <ul class="nav navbar-top-links navbar-right" id="after_login">
                <li>
                  
                    <span>
                            <img alt="image" class="img-circle" src="img/profile_small.jpg" />
                             </span>

                </li>
                <li class="dropdown profile-element" > 
                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            
                             </span> <span class="text-muted text-xs block"><i id="username"> </i><b class="caret"></b></span> </span> </a>
                            <ul class="dropdown-menu animated fadeInRight m-t-xs">
                                <li><a >Profile</a></li>
                                <li><a onclick="logout()">Logout</a></li>
                                
                                <li class="divider"></li>
                                
                            </ul>
                        </li>
                      
                <li class="dropdown"><!-- 
                    
                 --></li>


                <li>
                    <a>
                        <i class="fa fa-sign-out" onclick="logout()"></i> Log out
                    </a>
                </li>
                
            </ul>
            <ul class="nav navbar-top-links navbar-right" id="after_logout">
                <li>
                    <a  onclick="login()" class="btn btn-success btn-facebook">
                            <i class="fa fa-facebook"> </i> Sign in with Facebook
                        </a>
                </li>
            </ul>
        </nav>
        </div>
        <div class="wrapper wrapper-content animated fadeInRight" id="table_content">
            <div class="row">
                <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title" style="text-align: center;background-color:grey;">
                        <h5 style="color:white">Listings</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                          
                        </div>
                    </div>
                    <div class="ibox-content">

                        <table class="table">
                            <thead>
                            <tr>
                                <th></th>
                                <th>Source</th>
                                <th>Name</th>
                                <th>Address</th>
                                <th>Phone</th>
                                <th>Rating</th>
                                <th>Listed</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><i class="fa fa-google-plus fa-2x"></i></td>
                                <td>Google</td>
                                <td>ABC Dental</td>
                                <td>@mdo</td>
                                <td>8989415322</td>
                                <td>3/5</td>
                                <td>Yes</td>
                                <td><i class="fa fa-check fa-2x"></td>
                                <td><button type="button" class="btn btn-small btn-primary">Update</button></td></td>
                                
                            </tr>
                            <tr>
                                <td><i class="fa fa-yelp fa-2x"></i></td>
                                <td>Yelp</td>
                                <td>ABC Dental</td>
                                <td>@mdo</td>
                                <td>8989415322</td>
                                <td>3/5</td>
                                <td>Yes</td>
                                <td><i class="fa fa-times fa-2x"></td>
                                <td><button type="button" class="btn btn-small btn-primary">Update</button></td></td>
                                
                            </tr>
                            <tr>
                                <td><i class="fa fa-yahoo fa-2x"></i></td>
                                <td>Yahoo!</td>
                                <td>ABC Dental</td>
                                <td>@mdo</td>
                                <td>8989415322</td>
                                <td>3/5</td>
                                <td>Yes</td>
                                <td><i class="fa fa-times fa-2x"></td>
                                <td><button type="button" class="btn btn-small btn-primary">Update</button></td></td>
                                
                            </tr>
                            <tr>
                                <td><i class="fa fa-foursquare fa-2x"></i></td>
                                <td>Fourquare</td>
                                <td>ABC Dental</td>
                                <td>@mdo</td>
                                <td>8989415322</td>
                                <td>3/5</td>
                                <td>Yes</td>
                                <td><i class="fa fa-check fa-2x"></td>
                                <td><button type="button" class="btn btn-small btn-primary">Update</button></td></td>
                                
                            </tr>
                            <tr>
                                <td><i class="fa fa-facebook fa-2x"></i></td>
                                <td>Facebook</td>
                                <td id="fb_page">ABC Dental</td>
                                <td id="fb_address">@mdo</td>
                                <td id="fb_phone">8989415322</td>
                                <td id="fb_ratings">3/5</td>
                                <td id="fb_status">Yes</td>
                                <td><i class="fa fa-check fa-2x"></td>
                                <td><button type="button" class="btn btn-small btn-primary" data-toggle="modal" data-target="#myModal5">Update</button></td>
                                
                            </tr>
                            </tbody>
                        </table>
                         <div class="modal inmodal fade" id="myModal5" tabindex="-1" role="dialog"  aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                            <h4 class="modal-title">Update Facebook Tycoon Page Data </h4>
                                            <small class="font-bold">Update Address And phone </small>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="", id="page_id" value="">
                                            <input type="hidden" name="", id="page_token" value="">
                                           <table>
                                                <tr>
                                                    <td>Street:<span style="color:red">*</span></td>
                                                    <td><input id="form_street1" type="textarea" name="street1" value="" required></td>
                                                </tr>
                                                <tr>
                                                    <td>City:<span style="color:red">*</span></td>
                                                    <td><input id="form_city" type="text" name="city" value="" required ></td>
                                                </tr>
                                                <tr>
                                                    <td>State:</td> 
                                                    <td><input id="form_street2" type="text" name="street2" value=""></td>
                                                </tr>
                                                <tr>
                                                    <td>Country:<span style="color:red">*</span></td>
                                                    <td><input id="form_country" type="text" name="country" value="" required></td>
                                                </tr>
                                                <tr>
                                                    <td>Phone:</td>
                                                   <td><input id="form_phone" type="number" name="phone" value="" ></td>
                                                </tr>
                                            </table>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                                            <button  onclick ="update_data()" type="button" class="btn btn-primary">Update</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
            
            </div>
            <div class="row"><!-- 
                
             --></div>
            <div class="row"><!-- 
                

             --></div>
        </div>
               
        <div class="row">
        </div>

        </div>
        <div class="small-chat-box fadeInRight animated"></div>



    <!-- Mainly scripts -->
    <script src="js/jquery-2.1.1.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

    <!-- Flot -->
    <script src="js/plugins/flot/jquery.flot.js"></script>
    <script src="js/plugins/flot/jquery.flot.tooltip.min.js"></script>
    <script src="js/plugins/flot/jquery.flot.spline.js"></script>
    <script src="js/plugins/flot/jquery.flot.resize.js"></script>
    <script src="js/plugins/flot/jquery.flot.pie.js"></script>

    <!-- Peity -->
    <script src="js/plugins/peity/jquery.peity.min.js"></script>
    <script src="js/demo/peity-demo.js"></script>

    <!-- Custom and plugin javascript -->
    <script src="js/inspinia.js"></script>
    <script src="js/plugins/pace/pace.min.js"></script>

    <!-- jQuery UI -->
    <script src="js/plugins/jquery-ui/jquery-ui.min.js"></script>

    <!-- GITTER -->
    <script src="js/plugins/gritter/jquery.gritter.min.js"></script>

    <!-- Sparkline -->
    <script src="js/plugins/sparkline/jquery.sparkline.min.js"></script>

    <!-- Sparkline demo data  -->
    <script src="js/demo/sparkline-demo.js"></script>

    <!-- ChartJS-->
    <script src="js/plugins/chartJs/Chart.min.js"></script>

    <!-- Toastr -->
    <script src="js/plugins/toastr/toastr.min.js"></script>


    <script>
         $(document).ready(function() {

              checkLoginState();

         });

    </script>
</body>
</html>
