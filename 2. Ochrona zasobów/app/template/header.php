<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Kalkulator kredytowy">
    <title>Kalkulator Kredytowy</title>
    <link rel="stylesheet" href="<?php echo _APP_URL; ?>/style.css">
</head>
<body>
    <nav>
        <ul>
        <?php
        foreach (@$menu as $url => $label) {
            echo '<li><a href="'._APP_URL.'/'.$url.'">'.$label.'</a></li>';
        }
        ?>
        </ul>
    </nav>
    <header>
        <h1>Kalkulator Kredytowy</h1>
    </header>
    <main>
            <?php
            if(isset($_SESSION['role'])) {

                echo "<p class='result'>Jesteś zalogowany jako: ".$_SESSION['role'];
                if($_SESSION['role'] == 'user') echo "<br> (max. kwota kredytu: 100.000 zł)";
                echo "</p>";
            }
        ?>