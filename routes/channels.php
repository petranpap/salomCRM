<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('appointments.{appointmentId}', function ($user, $appointmentId) {
    return true; // Add your authorization logic here
});

Broadcast::channel('products.{productId}', function ($user, $productId) {
    return true; // Add your authorization logic here
});

Broadcast::channel('customers.{customerId}', function ($user, $customerId) {
    return true; // Add your authorization logic here
});