<?php

function idr($value = null): string
{
   return 'Rp ' . number_format((int) $value, 0, ',', '.');
}

function idr_to_int($value)
{
   if (is_null($value)) return 0;
   return (int) preg_replace('/\D/', '', $value);
}
