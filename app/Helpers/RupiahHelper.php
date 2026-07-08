<?php
if (! function_exists('rupiah')) {
    function rupiah(int $angka): string
    {
        $nilai = number_format((int) $angka, 0, ',', '.');
        return 'Rp ' . $nilai;
    }
}
