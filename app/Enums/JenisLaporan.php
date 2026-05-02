<?php

namespace App\Enums;

enum JenisLaporan: string
{
    case KEUANGAN = 'keuangan';
    case OPERASIONAL = 'operasional';
    case LAINNYA = 'lainnya';

    public function label(): string
    {
        return match ($this) {
            self::KEUANGAN => 'Keuangan',
            self::OPERASIONAL => 'Operasional',
            self::LAINNYA => 'Lainnya',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::KEUANGAN => '💰',
            self::OPERASIONAL => '⚙️',
            self::LAINNYA => '📄',
        };
    }
}
