<?php

function formatPrice($str)
{
    return str_replace( ',', '.', number_format($str));
}
