<?php

/**
 * Return a canonical Nepal mobile number, or null when the input is invalid.
 *
 * Accepted formats include 98XXXXXXXX, +97798XXXXXXXX, and 97798XXXXXXXX.
 */
function normalizeNepalPhoneNumber(string $phone): ?string
{
    $phone = preg_replace('/[\s().-]+/', '', trim($phone));

    if (!preg_match('/^(?:\+977|977)?(9\d{9})$/', $phone, $matches)) {
        return null;
    }

    return '+977' . $matches[1];
}
