<?php
// backend/utils.php

/** Generate a UUID v4 string. */
function uuidv4(): string {
  $data = random_bytes(16);
  // version 4
  $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
  // variant 10xx
  $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
  return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

/**
 * Generate a simple reference code.
 * Example:
 *   simple_ref('C') -> C1234567
 *   simple_ref('U') -> U7654321
 *
 * @param string $prefix One-letter (or more) prefix to identify entity type
 * @param int $digits   Number of random digits (default 7)
 * @return string       Reference code
 */
function simple_ref(string $prefix, int $digits = 7): string {
  $max = (10 ** $digits) - 1;
  $num = str_pad((string)random_int(0, $max), $digits, '0', STR_PAD_LEFT);
  return strtoupper($prefix) . $num;
}
