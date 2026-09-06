<?php

namespace App\Enums;

enum NetworkAssetStatus: string
{
    case BelumDipasang = 'belum_dipasang';
    case Aktif = 'aktif';
    case TidakAktif = 'tidak_aktif';
}
