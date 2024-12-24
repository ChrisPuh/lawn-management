<?php

declare(strict_types=1);

namespace App\Enums;

enum GrassSeed
{
    case LoliumPerenne;
    case PoaTrivialis;
    case PoaAnnua;
    case FestucaRubraSubspCommutata;
    case PoaPratensis;
    case FestucaOvina;
    case FestucaTrachyphylla;
    case FestucaRubra;

    public function label(): string
    {
        return match ($this) {
            self::LoliumPerenne => 'Lolium perenne',
            self::PoaTrivialis => 'Poa trivialis',
            self::PoaAnnua => 'Poa annua',
            self::FestucaRubraSubspCommutata => 'Festuca rubra subsp. commutata',
            self::PoaPratensis => 'Poa pratensis',
            self::FestucaOvina => 'Festuca ovina',
            self::FestucaTrachyphylla => 'Festuca trachyphylla',
            self::FestucaRubra => 'Festuca rubra',
        };
    }

    public function value(): string
    {
        return match ($this) {
            self::LoliumPerenne => 'LoliumPerenne',
            self::PoaTrivialis => 'PoaTrivialis',
            self::PoaAnnua => 'PoaAnnua',
            self::FestucaRubraSubspCommutata => 'FestucaRubraSubspCommutata',
            self::PoaPratensis => 'PoaPratensis',
            self::FestucaOvina => 'FestucaOvina',
            self::FestucaTrachyphylla => 'FestucaTrachyphylla',
            self::FestucaRubra => 'FestucaRubra',
        };
    }
}
