<?php

namespace App\Enums;

enum RmaStatus: string
{
    case RusakDiKantor = 'rusak_di_kantor';
    case PengirimanKePusat = 'pengiriman_ke_pusat';
    case DiterimaDiPusat = 'diterima_di_pusat';
    case PengirimanKeVendor = 'pengiriman_ke_vendor';
    case DiprosesVendor = 'diproses_vendor';
    case DiterimaDariVendor = 'diterima_dari_vendor';
    case PengirimanKeKantor = 'pengiriman_ke_kantor';
    case SelesaiDipasang = 'selesai_dipasang';
}
