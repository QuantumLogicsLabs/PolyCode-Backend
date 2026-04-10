<?php

$prices = [10, 20, 30, 40, 50];

// Use array_filter to get prices above 25
$expensive = array_filter($prices, function($price) {
    return $price > 25;
});

// Use array_map to add 10% tax to each price
$withTax = array_map(fn($p) => $p * 1.10, $prices);

print_r($expensive);
print_r($withTax);

?>