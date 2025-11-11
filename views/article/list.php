<?php
// list.php
// Front Office pour afficher les articles / deals
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyber Monday Sale - Unreal Deals Await!</title>
    <link rel="stylesheet" href="../../assets/css/frontstyle.css">
</head>
<body>
    <header>
        <div class="container">
            <h1 class="logo">CyberDeals</h1>
            <nav>
                <ul>
                    <li><a href="#home" class="super-button">Home</a></li>
                    <li><a href="#deals" class="super-button">Deals</a></li>
                    <li><a href="#deals" class="super-button">Shop Now</a></li>
                    <li><a href="#contact" class="super-button">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section id="home" class="hero">
        <div class="container">
            <h2>Unreal Savings on Cyber Monday!</h2>
            <p>Get up to 70% off on electronics, gadgets, and more. Limited time offer!</p>
            <div id="countdown" class="countdown">
                <div class="time">
                    <span id="days">00</span>
                    <small>Days</small>
                </div>
                <div class="time">
                    <span id="hours">00</span>
                    <small>Hours</small>
                </div>
                <div class="time">
                    <span id="minutes">00</span>
                    <small>Minutes</small>
                </div>
                <div class="time">
                    <span id="seconds">00</span>
                    <small>Seconds</small>
                </div>
            </div>
            <button class="shop-now-btn">Shop Now</button>
        </div>
    </section>

    <section id="deals" class="deals">
        <div class="container">
            <h3>Hot Deals</h3>
            <div class="deal-cards">
                <div class="card">
                    <img src="" alt="Laptop Deal">
                    <h4>Gaming Laptop</h4>
                    <p>$999 <span class="old-price">$1499</span></p>
                    <a href="#deals" class="super-button">Buy Now</a>
                </div>
                <div class="card">
                    <img src="" alt="Headphones Deal">
                    <h4>Wireless Headphones</h4>
                    <p>$199 <span class="old-price">$299</span></p>
                    <a href="#deals" class="super-button">Buy Now</a>
                </div>
                <div class="card">
                    <img src="" alt="Smartphone Deal">
                    <h4>Smartphone</h4>
                    <p>$499 <span class="old-price">$799</span></p>
                    <a href="#deals" class="super-button">Buy Now</a>
                </div>
            </div>
        </div>
    </section>

    <footer id="contact">
        <div class="container">
            <p>&copy; 2023 CyberDeals. All rights reserved.</p>
            <p>Contact: info@cyberdeals.com</p>
        </div>
    </footer>

    <script src="../../assets/js/frontscript.js"></script>
</body>
</html>

