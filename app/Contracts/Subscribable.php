<?php

namespace App\Contracts;

/**
 * Marker interface for Purchasable models whose access is time-limited —
 * a purchase grants 1 oy (one month) of access and must be renewed
 * afterwards. Implemented by Section, Book and User (teacher subscription).
 * Purchasable models that don't implement this (DTM/sertifikat/til imtihoni
 * testlari) keep the older one-time, never-expiring purchase behavior.
 */
interface Subscribable {}
