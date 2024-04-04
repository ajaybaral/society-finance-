<!-- navbar.php -->
<div class="navbar">
    <style>
        /* CSS styles for the navbar */
        .navbar {
            background-color: #333;
            color: #fff;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo img {
            width: 100px; /* Adjust size as needed */
            height: auto;
        }

        .nav-items {
            display: flex;
        }

        .nav-item {
            margin-right: 20px;
        }

        .nav-item a {
            text-decoration: none;
            color: #fff;
            transition: color 0.3s ease;
        }

        .nav-item a:hover {
            color: #ffcc00;
        }
    </style>

    <div class="logo">
        <img src="ani1.png" alt="Logo">
    </div>
    <div class="nav-items">
        <p class="nav-item"><a href="/SFS/">HOME</a></p>
        <p class="nav-item"><a href="/SFS/ABOUT-US">ABOUT US</a></p>
        <p class="nav-item"><a href="/SFS/">SERVICES</a></p>
        <p class="nav-item"><a href="/SFS/LOGOUT">LOGOUT</a></p>
        <p class="nav-item"><a href="/SFS/CONTACT">CONTACT</a></p>
    </div>
</div>
