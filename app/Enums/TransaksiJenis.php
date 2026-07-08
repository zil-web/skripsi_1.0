<?php
namespace App\Enums;

enum TransaksiJenis: string
{
    case PEMASUKAN = 'pemasukan';
    case PENGELUARAN = 'pengeluaran';
}
