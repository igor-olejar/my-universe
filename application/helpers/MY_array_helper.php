<?php
function vdump($stuff)
{
    echo "<pre>";
    print_r($stuff);
    echo "</pre>";
}

function vdie($stuff)
{
    echo "<pre>";
    print_r($stuff);
    echo "</pre>";
    die();
}