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

        .gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150, 1fr));
            gap: 15px;
            margin: 20px 0;
        }

        .gallery img {
            width: 100%;
            height: auto;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .gallery img:hover {
            transform: scale(1.1);
        }

        html {
            scroll-behavior: smooth;
        }
    </style>


</head>

<body>

    <button onclick="scrollToTop()" id="returnToTopBtn" title="Go to top"
        style="display: none; position: fixed; bottom: 20px; right: 30px; z-index: 99; font-size: 18px; border: none; outline: none; background-color: #6a1000; color: white; cursor: pointer; padding: 15px; border-radius: 50px;">
        ↑
    </button>
    <nav class="navbar navbar-expand-lg navbar-light bg-light"
        style="padding: 20px; width: 100%; position: fixed; opacity: 0.9; z-index: 1000; height: 50px;">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo03"
                aria-controls="navbarTogglerDemo03" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="index.php"
                style="text-align: center; margin-bottom: 5px; padding-left: 100px; padding-right: 150px;">
                <i class="material-icons"
                    style="font-size: 30px; color:  #c68c53; text-align: center;padding: 5px;vertical-align: text-bottom;">hotel</i>
                HOTEL MANAGEMENT SYSTEM
            </a>
            <div class="collapse navbar-collapse" id="navbarTogglerDemo03">
                <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="#introduction" style="color: #c68c53;">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#footer" style="color: #c68c53;">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="bookroom.php" style="color: #c68c53;">Book Room</a>
                    </li>
                </ul>
                <ul class="navbar-nav ml-auto">
                    <?php if (isset($_SESSION['user'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="userprofile.php" style="color: #c68c53;">
                                <i class="fa fa-user-circle" style="font-size: 20px;"></i> Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php" style="color: #c68c53;">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php" style="color: #c68c53;">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Static Section -->
    <div class="jumbotron text-overlay"
        style="background-color: transparent; color: #c68c53 ; text-align: center; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 2; padding: 20px;">
        <h1 class="display-3" style="font-family: 'Playfair Display', serif; font-weight: bold;">
            <b>Peace Home</b>
        </h1>
        <p style="font-size: 1.5rem; font-family: 'Segoe UI', sans-serif; font-weight: bold;">HOMESTAY & RESTAURANT
        </p>
        <p style="font-size: 1.2rem; font-family: 'Segoe UI', sans-serif; font-weight: bold;">Supan village • near
            Sapa</p>
        <a href="login.php">
            <button class="button1"
                style="background-color: #c68c53 ; color: white; border: none; padding: 10px 20px; font-size: 1.2rem; border-radius: 10px; transition: 0.3s;">
                Join Us Today!
            </button>
        </a>
    </div>

    <!-- Carousel Section -->
    <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel" data-interval="3000"
        style="width: 100%; position: relative;">
        <!-- Removed the indicators -->
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img class="d-block w-100" src="images/img1.jpg" alt="First slide"
                    style="height: 700px; filter: blur(5px);">
            </div>
            <div class="carousel-item">
                <img class="d-block w-100" src="images/img2.jpg" alt="Second slide"
                    style="height: 700px; filter: blur(5px);">
            </div>
            <div class="carousel-item">
                <img class="d-block w-100" src="images/img3.jpg" alt="Third slide"
                    style="height: 700px; filter: blur(5px);">
            </div>
        </div>
        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
            <span class="sr-only">Next</span>
        </a>
    </div>

    <div style="background-color: #F0F8FF; padding: 100px; letter-spacing: 1.5px; line-height: 30px; text-align: center; padding-left: 300px; padding-right: 300px;"
        id="introduction">
        <h1 style="font-family: initial; line-height: 100px">Welcome to Peace Home!</h1>
        <p style="font-family: Arial;"> Peace Home is located in Su Pan, a small village sourrounded by nature and rice
            fields in the area of Sa Pa. We are located 10 km away from Sa Pa town (about 30 minutes drive, or 3 hours
            walk).

            If you are looking for a peaceful place, far from the city buzz, with fresh air and hiking trails, then
            Peace Home is the place for you!<br>
    </div>

    <div class="row" style="background-color: #c68c53; padding: 60px; width: 100%; margin: 0;">

        <!-- Room and Services (Carousel) -->
        <div class="column" style="float: left; padding-left: 250px;">
            <div id="roomCarousel" class="carousel slide" data-ride="carousel" data-interval="2000">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="images/room1.png" class="d-block" style="width: 450px; height: 275px;">
                    </div>
                    <div class="carousel-item">
                        <img src="images/room2.png" class="d-block" style="width: 450px;height: 275px;">
                    </div>
                    <div class="carousel-item">
                        <img src="images/room3.png" class="d-block" style="width: 450px; height: 275px;">
                    </div>
                </div>
            </div>
        </div>
        <div style="float: left; width:40%; padding-left: 70px">
            <h2 style="font-family: Times New Roman; color: white;">Everything You Need at Peace Home</h2>
            <p style="color: white; font-weight: 1">
                Peace Home has all the services you may need: a delicious restaurant open all day, a beautiful garden
                where
                you can make a campfire at night, a swimming pool in the summer, hot medicinal herbal baths, laundry
                service,
                tours and trekking, bus tickets, motorbike rental, and much more.
            </p>
        </div>

        <!-- Restaurant -->
        <div class="column"
            style="float: left; padding-left: 225px; width: 46%; padding-top: 40px; text-align: right; padding-right: 25px">
            <h2 style="font-family: Times New Roman; color: white;">Restaurant</h2>
            <p style="color: white; font-weight: 1">
                Peace Home’s restaurant, Muong Hoa Food & Drink, offers a delightful blend of Vietnamese and Western
                cuisine in
                a warm, homely setting. Savor authentic local flavors like phở, spring rolls, hotpots, fried rice,
                noodles,
                tofu, fragrant chicken curry, and much more – or indulge in comforting Western favorites such as
                homemade
                burgers with crispy fries, french crepes, and our customer’s favorite pumpkin soup. Don’t miss our
                signature
                banana cakes and homemade peanut butter! Whether you’re craving tradition or a taste of home, every dish
                is
                made with fresh ingredients and a touch of love.
            </p>
        </div>
        <div style="float: left; padding-left: 40px; padding-top: 50px">
            <img src="images/restaurant.png" style="width: 450px; height: 275px">
        </div>

        <!-- Herbal Bath -->
        <div class="column" style="float: left; padding-left: 250px">
            <img src="images/herbalbath.png" style="width: 450px; height: 275px">
        </div>
        <div style="float: left; width:40%; padding-left: 70px">
            <h2 style="font-family: Times New Roman; color: white;">Traditional Red Dao Herbal Bath</h2>
            <p style="color: white; font-weight: 1">
                At Peace Home, you have the possibility to immerse yourself in the traditional Red Dao herbal bath, a
                time-honored wellness practice in Sapa. This therapeutic bath combines a variety of medicinal plants,
                carefully selected and harvested from the lush forests surrounding the region. The herbs are
                meticulously
                prepared and boiled to extract their beneficial properties, creating a soothing infusion that addresses
                various health concerns.
            </p>
        </div>

        <!-- Animal Helpers -->
        <div class="column"
            style="float: left; padding-left: 225px; width: 46%; padding-top: 40px; text-align: right; padding-right: 25px">
            <h2 style="font-family: Times New Roman; color: white;">Animal Helpers</h2>
            <p style="color: white; font-weight: 1">
                At Peace Home, you may be greeted or even guided by our beloved animal companions! Our friendly pets not
                only
                bring joy to the atmosphere but also interact warmly with guests, sometimes even helping staff make you
                feel
                at home. Whether it’s a wagging tail or a curious meow, our animal helpers are part of the Peace Home
                family.
            </p>
        </div>
        <div style="float: left; padding-left: 40px; padding-top: 50px">
            <img src="images/pet.png" style="width: 450px; height: 275px">
        </div>

    </div>

    <!-- Gallery Section -->
    <div class="row"
        style="margin: 0; background-color: #F0F8FF; display: flex; justify-content: center; align-items: center;">
        <div class="" style="padding-left: 100px; padding-right: 30px; padding-top: 30px; padding-bottom: 20px;">
            <h3 style="font-family: initial; text-align: center;">Gallery</h3>

            <!-- Gallery Images -->
            <div class="row" style="padding-left: 5px">
                <div class="col-sm-4" style="padding: 5px">
                    <a href="javascript:void(0);" data-toggle="modal" data-target="#imageModal" data-slide="0">
                        <img src="images/image1.png" style="width: 240px; height: 150px;">
                    </a>
                </div>
                <div class="col-sm-4" style="padding: 5px">
                    <a href="javascript:void(0);" data-toggle="modal" data-target="#imageModal" data-slide="1">
                        <img src="images/image2.png" style="width: 240px; height: 150px;">
                    </a>
                </div>
                <div class="col-sm-4" style="padding: 5px">
                    <a href="javascript:void(0);" data-toggle="modal" data-target="#imageModal" data-slide="2">
                        <img src="images/image3.png" style="width: 240px; height: 150px;">
                    </a>
                </div>
            </div>

            <div class="row" style="padding-left: 5px">
                <div class="col-sm-4" style="padding: 5px">
                    <a href="javascript:void(0);" data-toggle="modal" data-target="#imageModal" data-slide="3">
                        <img src="images/image4.png" style="width: 240px; height: 150px;">
                    </a>
                </div>
                <div class="col-sm-4" style="padding: 5px">
                    <a href="javascript:void(0);" data-toggle="modal" data-target="#imageModal" data-slide="4">
                        <img src="images/image5.png" style="width: 240px; height: 150px;">
                    </a>
                </div>
                <div class="col-sm-4" style="padding: 5px">
                    <a href="javascript:void(0);" data-toggle="modal" data-target="#imageModal" data-slide="5">
                        <img src="images/image6.png" style="width: 240px; height: 150px;">
                    </a>
                </div>
            </div>

            <div class="row" style="padding-left: 5px">
                <div class="col-sm-4" style="padding: 5px">
                    <a href="javascript:void(0);" data-toggle="modal" data-target="#imageModal" data-slide="6">
                        <img src="images/image7.png" style="width: 240px; height: 150px;">
                    </a>
                </div>
                <div class="col-sm-4" style="padding: 5px">
                    <a href="javascript:void(0);" data-toggle="modal" data-target="#imageModal" data-slide="7">
                        <img src="images/image8.png" style="width: 240px; height: 150px;">
                    </a>
                </div>
                <div class="col-sm-4" style="padding: 5px">
                    <a href="javascript:void(0);" data-toggle="modal" data-target="#imageModal" data-slide="8">
                        <img src="images/image9.png" style="width: 240px; height: 150px;">
                    </a>
                </div>
            </div>

        </div>

    </div>

    <!-- Modal -->
    <!-- Modal for Fullscreen Image Carousel -->
    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document" style="max-width: 960px; max-height: 600px;">
            <div class="modal-content" style="height: 600px;">
                <div class="modal-header border-0">
                    <button type="button" class="close ml-auto" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0" style="height: calc(100% - 56px); overflow: hidden;">
                    <div id="carouselExample" class="carousel slide h-100" data-ride="carousel">
                        <div class="carousel-inner h-100">
                            <div class="carousel-item active h-100">
                                <img src="images/image1.png" class="d-block w-100 h-100 object-fit-contain"
                                    alt="Image 1">
                            </div>
                            <div class="carousel-item h-100">
                                <img src="images/image2.png" class="d-block w-100 h-100 object-fit-contain"
                                    alt="Image 2">
                            </div>
                            <div class="carousel-item h-100">
                                <img src="images/image3.png" class="d-block w-100 h-100 object-fit-contain"
                                    alt="Image 3">
                            </div>
                            <div class="carousel-item h-100">
                                <img src="images/image4.png" class="d-block w-100 h-100 object-fit-contain"
                                    alt="Image 4">
                            </div>
                            <div class="carousel-item h-100">
                                <img src="images/image5.png" class="d-block w-100 h-100 object-fit-contain"
                                    alt="Image 5">
                            </div>
                            <div class="carousel-item h-100">
                                <img src="images/image6.png" class="d-block w-100 h-100 object-fit-contain"
                                    alt="Image 6">
                            </div>
                            <div class="carousel-item h-100">
                                <img src="images/image7.png" class="d-block w-100 h-100 object-fit-contain"
                                    alt="Image 7">
                            </div>
                            <div class="carousel-item h-100">
                                <img src="images/image8.png" class="d-block w-100 h-100 object-fit-contain"
                                    alt="Image 8">
                            </div>
                            <div class="carousel-item h-100">
                                <img src="images/image9.png" class="d-block w-100 h-100 object-fit-contain"
                                    alt="Image 9">
                            </div>
                        </div>
                        <a class="carousel-control-prev" href="#carouselExample" role="button" data-slide="prev"
                            style="width: 5%;">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#carouselExample" role="button" data-slide="next"
                            style="width: 5%;">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>



    </div>

    <footer
        style="background-color: #c68c53; font-family: initial; text-align: center; padding: 40px 20px; width: 100%; color: white;"
        id="footer">
        <h1 style="font-size: 1.8rem; margin-bottom: 20px;">Our Location</h1>
        <p style="margin-bottom: 30px;">Visit us at our location for an unforgettable experience!</p>

        <div class="container">
            <div class="row">
                <!-- Contact Info -->
                <div class="col-md-4 mb-4 text-start">
                    <!-- Social Media Icons -->
                    <div style="margin-bottom: 20px;">
                        <a href="https://www.facebook.com/PeacehomeSapa/" target="_blank"
                            style="display: inline-block; margin-right: 10px;">
                            <img src="images/Facebook_Logo.png" style="width: 30px; height: 30px;">
                        </a>
                        <a href="https://www.instagram.com/peacehome_sapa/" target="_blank"
                            style="display: inline-block;">
                            <img src="images/Instagram_Icon.png" style="width: 30px; height: 30px;">
                        </a>
                    </div>

                    <!-- Contact Info -->
                    <h5 style="font-size: 1.2rem; margin-bottom: 10px;">Phone / Zalo / Whatsapp:</h5>
                    <p style="margin: 5px 0;">Ms. Dung: 03 95 31 67 60</p>
                    <p style="margin: 5px 0;">Ms. Thơm: 09 82 89 77 36</p>
                    <p style="margin-top: 10px;">If we can be of any assistance, please do not hesitate to contact us!
                    </p>
                </div>

                <!-- Map -->
                <div class="col-md-4 mb-4">
                    <div style="width: 100%; height: 100%;">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3717.047482477723!2d103.9198331!3d22.2948518!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x36cd38ac38ebcdb3%3A0x35cd8e71fd74319c!2sAn%20B%C3%ACnh%20Home!5e0!3m2!1sen!2s!4v1680000000000!5m2!1sen!2s"
                            width="100%" height="200" style="border:0;" allowfullscreen="" loading="lazy">
                        </iframe>
                    </div>
                </div>

                <!-- Address -->
                <div class="col-md-4 mb-4 text-start">
                    <h5 style="font-size: 1.2rem; margin-bottom: 10px;">Our address:</h5>
                    <p style="margin: 5px 0;">
                        Peace Home / An Bình Home<br>
                        Hòa Sử Pán<br>
                        Mường Hoa<br>
                        SA PA<br>
                        Lào Cai Province<br>
                        VIET NAM
                    </p>
                </div>
            </div>
        </div>
    </footer>




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
        // Get the button
        const returnToTopBtn = document.getElementById("returnToTopBtn");
        document.addEventListener("DOMContentLoaded", function () {
            // Hide the button initially
            returnToTopBtn.style.display = "none";
            scrollToTop();
        });
        // Show the button when the user scrolls down 20px from the top
        window.onscroll = function () {
            console.log("Scrolling...");
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                returnToTopBtn.style.display = "block";
            } else {
                returnToTopBtn.style.display = "none";
            }
        };

        // Scroll to the top of the page when the button is clicked
        function scrollToTop() {
            window.scrollTo({
                top: 0
            });
        }
    </script>
</body>

</html>