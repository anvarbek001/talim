<?php

namespace App\Contracts;

/**
 * Implemented by Purchasable content that belongs to a single teacher
 * (Section, Book) — lets PurchaseService grant access to a student who
 * instead holds an active subscription to that teacher, without buying the
 * item itself.
 */
interface TeacherOwned
{
    public function teacherId(): int;
}
