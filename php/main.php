<?php

namespace php;

session_start();
//var_dump($_SESSION);
?>
<H1>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Eveniet, quaerat laborum, quis maiores, magnam quas eius accusamus consequuntur qui libero aut rem! Aperiam, aspernatur vel.</H1>
<?php
if ($_SESSION['role'] == "VKUser") {
?><img src="/assets/img/slide1.jpg" alt=""><?php
                                          }
