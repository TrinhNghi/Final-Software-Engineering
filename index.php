<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Hotel Management System</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">



    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css"
        integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
    <style>
        .button1:hover {
            background-color: #A0522D;
            color: #FFF;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .button1 {
            border: 2px solid black;
            color: white;
            font-size: 30px;
            word-spacing: 5px;
            border-radius: 15px;
            background-color: blue;
        }
    </style>


</head>

<body>


    <nav class="navbar navbar-expand-lg navbar-light bg-light"
        style="padding: 20px;width:100%;position: fixed;opacity:0.9;z-index: 1;height:50px;">
        <center><button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo03"
                aria-controls="navbarTogglerDemo03" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="HMS.html"
                style="text-align: center; margin-bottom: 5px; padding-left: 100px; padding-right: 150px;"><i
                    class="material-icons"
                    style="font-size: 30px; color:  #c68c53; text-align: center;padding: 5px;vertical-align: text-bottom;">hotel</i>
                HOTEL MANAGEMENT SYSTEM</a>
        </center>
        <button id="Historybtn">
            Scrollll
        </button>
    </nav>

    <!-- Static Section -->
    <div class="jumbotron text-overlay"
        style="background-color: transparent; color: #8B4513; text-align: center; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 2; padding: 20px;">
        <h1 class="display-3" style="font-family: 'Playfair Display', serif; font-weight: bold;">
            <b>Peace Home</b>
        </h1>
        <p style="font-size: 1.5rem; font-family: 'Segoe UI', sans-serif; font-weight: bold;">HOMESTAY & RESTAURANT
        </p>
        <p style="font-size: 1.2rem; font-family: 'Segoe UI', sans-serif; font-weight: bold;">Supan village • near
            Sapa</p>
        <a href="Signup.html">
            <button class="button1"
                style="background-color: #8B4513; color: white; border: none; padding: 10px 20px; font-size: 1.2rem; border-radius: 10px; transition: 0.3s;">
                Join Us Today!
            </button>
        </a>
    </div>

    <!-- Carousel Section -->
    <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel" style="width: 100%; position: relative;">
        <ol class="carousel-indicators">
            <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
        </ol>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img class="d-block w-100" src="images/img1.jpg" alt="First slide" style="height: 700px; filter: blur(5px);">
            </div>
            <div class="carousel-item">
                <img class="d-block w-100" src="images/img2.jpg" alt="Second slide" style="height: 700px; filter: blur(5px);">
            </div>
            <div class="carousel-item">
                <img class="d-block w-100" src="images/img3.jpg" alt="Third slide" style="height: 700px; filter: blur(5px);">
            </div>
        </div>
        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
            <span class="sr-only">Next</span>
        </a>
    </div>

    <div
        style="background-color: #F0F8FF; padding: 100px; letter-spacing: 1.5px; line-height: 30px; text-align: center; padding-left: 300px; padding-right: 300px;">
        <h1 style="font-family: initial; line-height: 100px">Exprience an exclusicve hospitality</h1>
        <p style="font-family: Arial;"> Embark on a journey you will never forget at the first luxury boutique resort
            with pivete beach access overlooking the beautiful island. We assure you an unforgettable exprience,
            bringing back with you many incredible memories.<br><b>&#9883; &#x269B; &#9883;</p>
    </div>

    <div class="row" style="background-color: #c68c53; padding: 60px; width: 100%; margin-left: 2px">
        <div class="column" style="float: left; padding-left: 250px"><img src="image\11.jpg"
                style="width: 450px; height: 275px"></div>
        <div style="float: left; width:40%; padding-left: 70px">
            <h2 style="font-family: Times New Roman; color: white;">A refreshing and unique feel to the face of
                hospitality</h2>
            <p style="color: white; font-weight: 1">While preserving the mature tree canopy atop the seaside cliff
                facing out to island, the 23 suites provide a refreshing and unique feel to the face of hospitality.</p>
        </div>

        <div class="column"
            style="float: left; padding-left: 225px; width: 46%; padding-top: 40px; text-align: right; padding-right: 25px">
            <h2 style="font-family: Times New Roman; color: white;">Designed with contemporary approach</h2>
            <p style="color: white; font-weight: 1">All suites are designed with contemporary approach and an open space
                concept, complemented with accents of pure luxury and style. Each of the 16 garden suites, 3 sea breeze
                suites and 4 canopy suites attributes unique touches and amenities.</p>
            <button id="button1" data-toggle="modal" data-target="#exampleModalLong"
                style="background-color:#c68c53; color: white; text-align: center; transition-duration: 0.4s;"><a
                    href="#" style="color: white; text-decoration: none;">EXPLORE ALL OUR ROOMS</a></button>

            <!--Modal-->
            <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Rooms</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid" style="text-align: left;">
                                <div class="row">
                                    <h5>Standard Room- One king bed</h5>
                                    <div class="col"><img src="image\3.jpg" style="width: 300px; height: 200px"></div>
                                </div><br>
                                <div class="row">
                                    <h5>Superior Room- Two double beds</h5>
                                    <div class="col"><img src="image\bed\5.jpg" style="width: 300px; height: 200px">
                                    </div>
                                </div><br>
                                <div class="row">
                                    <h5>Junior Suit- One single bed</h5>
                                    <div class="col"><img src="image\bed\4.jpg" style="width: 300px; height: 200px">
                                    </div>
                                </div><br>
                                <div class="row">
                                    <h5>Grand Superior Room- Two king beds</h5>
                                    <div class="col"><img src="image\bed\3.jpg" style="width: 300px; height: 200px">
                                    </div>
                                </div><br>
                                <div class="row">
                                    <h5>Superior Room- One king bed</h5>
                                    <div class="col"><img src="image\bed\2.jpg" style="width: 300px; height: 200px">
                                    </div>
                                </div><br>
                                <div class="row">
                                    <h5>Delux Room- One king bed</h5>
                                    <div class="col"><img src="image\21.jpg" style="width: 300px; height: 200px"></div>
                                </div><br>
                                <div class="row">
                                    <h5>Family Special- Three double beds</h5>
                                    <div class="col"><img src="image\bed\5.jpg" style="width: 300px; height: 200px">
                                    </div>
                                </div><br>
                                <div class="row">
                                    <h5>Premium Room- Two single beds</h5>
                                    <div class="col"><img src="image\bed\3.jpg" style="width: 300px; height: 200px">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="float: left; padding-left: 40px; padding-top: 50px"><img src="image\16.jpg"
                style="width: 450px; height: 275px"></div>
    </div>

    <div class="row" style="width: 100%; margin-left: 2px; background-color: #F0F8FF; ">
        <div class="col-sm-5"
            style=" padding-left: 100px; padding-right: 30px; padding-top: 30px; padding-bottom: 20px;">
            <h3 style="font-family: initial;">Gallery</h3>

            <div class="row" style="padding-left: 5px">
                <div class="col-sm-4" style="padding: 5px">
                    <a href="image\2.jpg" target="_blank"><img src="image\2.jpg"
                            style="width: 160px; height: 100px;"></a>
                </div>
                <div class="col-sm-4" style="padding: 5px">
                    <a href="image\24.jpg" target="_blank"><img src="image\24.jpg"
                            style="width: 160px; height: 100px;"></a>
                </div>
                <div class="col-sm-4" style="padding: 5px">
                    <a href="image\13.jpg" target="_blank"><img src="image\13.jpg"
                            style="width: 160px; height: 100px;"></a>
                </div>
            </div>

            <div class="row" style="padding-left: 5px">
                <div class="col-sm-4" style="padding: 5px">
                    <a href="image\23.jpg" target="_blank"><img src="image\23.jpg"
                            style="width: 160px; height: 100px;"></a>
                </div>
                <div class="col-sm-4" style="padding: 5px">
                    <a href="image\9.jpg" target="_blank"><img src="image\9.jpg"
                            style="width: 160px; height: 100px;"></a>
                </div>
                <div class="col-sm-4" style="padding: 5px">
                    <a href="image\10.jpg" target="_blank"><img src="image\10.jpg"
                            style="width: 160px; height: 100px;"></a>
                </div>
            </div>

            <div class="row" style="padding-left: 5px">
                <div class="col-sm-4" style="padding: 5px">
                    <a href="image\22.jpg" target="_blank"><img src="image\22.jpg"
                            style="width: 160px; height: 100px;"></a>
                </div>
                <div class="col-sm-4" style="padding: 5px">
                    <a href="image\8.jpg" target="_blank"><img src="image\8.jpg"
                            style="width: 160px; height: 100px;"></a>
                </div>
                <div class="col-sm-4" style="padding: 5px">
                    <a href="image\17.jpg" target="_blank"><img src="image\17.jpg"
                            style="width: 160px; height: 100px;"></a>
                </div>
            </div>

        </div>
        <div class="col-sm-3" style=" padding-left: 20px; padding-top: 30px; padding-bottom: 20px;">
            <h3 style="font-family: initial;">Video Tour</h3>
            <video controls preload muted style="height:225px; width:350px;">
                <source src="image\Malak Regency a Luxury Hotel (promo video English).mp4" type="video/mp4">
            </video>
            <h4 style="font-weight: 1">Conference on Human Health</h4>
            <p style="font-weight: 1">All suites are designed with contemporary approach and an open space concept.</p>
        </div>
        <div class="col-sm-4" style=" padding-left: 40px; padding-top: 30px; padding-bottom: 20px">
            <h3 style="font-family: initial;">Happenings</h3>
            <div style="width:400px; height: 300px; overflow: auto; text-align: left;">
                <div style="border-bottom: 1px solid" id="History">
                    <h4>Conference on Human Health</h4><br>
                    <p style="font-weight: 1">All suites are designed with contemporary approach and an open space
                        concept, complemented with accents of pure luxury and style.</p>
                </div>
                <div style="border-bottom: 1px solid">
                    <h4>National Conference on ‘Contemporary Perspectives in Design and Creative Arts’</h4><br>
                    <p style="font-weight: 1">All suites are designed with contemporary approach and an open space
                        concept, complemented with accents of pure luxury and style.</p>
                </div>
                <div style="border-bottom: 1px solid">
                    <h4> North-India’s biggest Women Entrepreneurship event ‘The Enterprising SHE’</h4><br>
                    <p style="font-weight: 1">We recently had an event/submit in which all future Entrepreneur met up
                        and enhance their visions to plan their career.</p>
                </div>
            </div>

        </div>
    </div>

    <div
        style="background-color: #c68c53; font-family: initial; text-align: center; padding: 100px; width: 100%; color: white;">
        <h1>Our Location</h1>
        <p>Visit us at our location for an unforgettable experience!</p>
        <div style="width: 100%; height: 400px;">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3717.047482477723!2d103.9198331!3d22.2948518!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x36cd38ac38ebcdb3%3A0x35cd8e71fd74319c!2sAn%20B%C3%ACnh%20Home!5e0!3m2!1sen!2s!4v1680000000000!5m2!1sen!2s"
                width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy">
            </iframe>
        </div>
    </div>




    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"
        integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ"
        crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"
        integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm"
        crossorigin="anonymous"></script>
    <script>
        // Add a click event listener to the button
        document.getElementById("Historybtn").addEventListener("click", function () {
            // Scroll to the element with ID "History"
            document.getElementById("History").scrollIntoView({
                behavior: "smooth", // Smooth scrolling animation
                block: "start" // Align to the top of the element
            });
        });
    </script>
</body>

</html>