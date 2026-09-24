<?php

interface Reservable
{
    public function reserve(int $quantity): bool;

    public function cancelReservation(): bool;
}