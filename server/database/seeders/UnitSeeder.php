<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit; // FONTOS: a modell importálása

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        // Fix számítógéphez kapcsolódó mértékegységek listája
        $units = [
            'GHz',           // gigahertz (processzor sebesség)
            'MHz',           // megahertz (processzor sebesség)
            'GB',            // gigabájt (RAM, tárolás)
            'MB',            // megabájt (RAM, tárolás)
            'TB',            // terabájt (tárolás)
            'W',             // watt (teljesítmény)
            'V',             // volt (feszültség)
            'A',             // amper (áram)
            'RPM',           // fordulatszám (ventilátor, HDD)
            'Hz',            // hertz (monitor frissítési gyakoriság)
            'dB',            // decibel (hang, zajszint)
            'C',             // Celsius (hőmérséklet)
            'K',             // Kelvin (hőmérséklet)
            'lux',           // lux (fényerő)
            'nm',            // nanométer (fényhullámhossz)
            'bit',           // bit (adatsebesség)
            'GB/s',          // gigabájt per másodperc (adatsebesség)
            'ms',            // milliszekundum (válaszidő, késleltetés)
            'kPa',           // kilopascal (nyomás, pl. hűtés)
            'm²',            // négyzetméter (monitor képernyőméret)
            'm³',            // köbméter (ventilátor légáramlás)
            'Lux',           // világítás erősség
            'cd',            // kandela (fényerő)
        ];

        // Mértékegységek beszúrása, ha még nem léteznek
        foreach ($units as $unit) {
            Unit::firstOrCreate(['unit_name' => $unit]);
        }
    }
}
