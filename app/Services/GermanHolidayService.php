<?php

namespace App\Services;

use App\Models\Holiday;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GermanHolidayService
{
    public const FEDERAL_STATES = [
        'BW' => 'Baden-Württemberg',
        'BY' => 'Bayern',
        'BE' => 'Berlin',
        'BB' => 'Brandenburg',
        'HB' => 'Bremen',
        'HH' => 'Hamburg',
        'HE' => 'Hessen',
        'MV' => 'Mecklenburg-Vorpommern',
        'NI' => 'Niedersachsen',
        'NW' => 'Nordrhein-Westfalen',
        'RP' => 'Rheinland-Pfalz',
        'SL' => 'Saarland',
        'SN' => 'Sachsen',
        'ST' => 'Sachsen-Anhalt',
        'SH' => 'Schleswig-Holstein',
        'TH' => 'Thüringen',
    ];

    public const STATE_URL_NAMES = [
        'BW' => 'Baden-Wuerttemberg',
        'BY' => 'Bayern',
        'BE' => 'Berlin',
        'BB' => 'Brandenburg',
        'HB' => 'Bremen',
        'HH' => 'Hamburg',
        'HE' => 'Hessen',
        'MV' => 'Mecklenburg-Vorpommern',
        'NI' => 'Niedersachsen',
        'NW' => 'Nordrhein-Westfalen',
        'RP' => 'Rheinland-Pfalz',
        'SL' => 'Saarland',
        'SN' => 'Sachsen',
        'ST' => 'Sachsen-Anhalt',
        'SH' => 'Schleswig-Holstein',
        'TH' => 'Thueringen',
    ];

    public function importPublicHolidays(int $organizationId, string $state, int $year): int
    {
        $holidays = $this->getPublicHolidays($state, $year);
        $count = 0;

        foreach ($holidays as $holiday) {
            Holiday::updateOrCreate(
                [
                    'organization_id' => $organizationId,
                    'date' => $holiday['date'],
                    'type' => 'public_holiday',
                    'federal_state' => $state,
                ],
                [
                    'name' => $holiday['name'],
                    'name_de' => $holiday['name_de'],
                    'year' => $year,
                ]
            );
            $count++;
        }

        return $count;
    }

    public function importSchoolBreaks(int $organizationId, string $state, int $year): int
    {
        $breaks = $this->fetchSchoolBreaks($state, $year);
        $count = 0;

        foreach ($breaks as $break) {
            $period = CarbonPeriod::create($break['start'], $break['end']);
            foreach ($period as $date) {
                if ($date->isWeekend()) {
                    continue;
                }
                Holiday::updateOrCreate(
                    [
                        'organization_id' => $organizationId,
                        'date' => $date->format('Y-m-d'),
                        'type' => 'school_break',
                        'federal_state' => $state,
                    ],
                    [
                        'name' => $break['name'],
                        'name_de' => $break['name_de'],
                        'year' => $year,
                    ]
                );
                $count++;
            }
        }

        return $count;
    }

    public function importWeekends(int $organizationId, int $year): int
    {
        $start = Carbon::create($year, 1, 1);
        $end = Carbon::create($year, 12, 31);
        $count = 0;

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            if ($date->isWeekend()) {
                Holiday::updateOrCreate(
                    [
                        'organization_id' => $organizationId,
                        'date' => $date->format('Y-m-d'),
                        'type' => 'weekend',
                    ],
                    [
                        'name' => $date->isSaturday() ? 'Saturday' : 'Sunday',
                        'name_de' => $date->isSaturday() ? 'Samstag' : 'Sonntag',
                        'year' => $year,
                        'federal_state' => null,
                    ]
                );
                $count++;
            }
        }

        return $count;
    }

    public function getPublicHolidays(string $state, int $year): array
    {
        $easterDate = Carbon::createFromTimestamp(easter_date($year));

        $holidays = [
            ['date' => Carbon::create($year, 1, 1), 'name' => "New Year's Day", 'name_de' => 'Neujahr'],
            ['date' => $easterDate->copy()->subDays(2), 'name' => 'Good Friday', 'name_de' => 'Karfreitag'],
            ['date' => $easterDate->copy(), 'name' => 'Easter Sunday', 'name_de' => 'Ostersonntag'],
            ['date' => $easterDate->copy()->addDay(), 'name' => 'Easter Monday', 'name_de' => 'Ostermontag'],
            ['date' => Carbon::create($year, 5, 1), 'name' => 'Labour Day', 'name_de' => 'Tag der Arbeit'],
            ['date' => $easterDate->copy()->addDays(39), 'name' => 'Ascension Day', 'name_de' => 'Christi Himmelfahrt'],
            ['date' => $easterDate->copy()->addDays(49), 'name' => 'Whit Sunday', 'name_de' => 'Pfingstsonntag'],
            ['date' => $easterDate->copy()->addDays(50), 'name' => 'Whit Monday', 'name_de' => 'Pfingstmontag'],
            ['date' => Carbon::create($year, 10, 3), 'name' => 'German Unity Day', 'name_de' => 'Tag der Deutschen Einheit'],
            ['date' => Carbon::create($year, 12, 25), 'name' => 'Christmas Day', 'name_de' => '1. Weihnachtstag'],
            ['date' => Carbon::create($year, 12, 26), 'name' => 'Second Christmas Day', 'name_de' => '2. Weihnachtstag'],
        ];

        $stateHolidays = $this->getStateSpecificHolidays($state, $year, $easterDate);
        $holidays = array_merge($holidays, $stateHolidays);

        return $holidays;
    }

    private function getStateSpecificHolidays(string $state, int $year, Carbon $easterDate): array
    {
        $holidays = [];

        // Heilige Drei Könige (Epiphany) - BW, BY, ST
        if (in_array($state, ['BW', 'BY', 'ST'])) {
            $holidays[] = ['date' => Carbon::create($year, 1, 6), 'name' => 'Epiphany', 'name_de' => 'Heilige Drei Könige'];
        }

        // Internationaler Frauentag - BE, MV
        if (in_array($state, ['BE', 'MV'])) {
            $holidays[] = ['date' => Carbon::create($year, 3, 8), 'name' => "International Women's Day", 'name_de' => 'Internationaler Frauentag'];
        }

        // Fronleichnam (Corpus Christi) - BW, BY, HE, NW, RP, SL
        if (in_array($state, ['BW', 'BY', 'HE', 'NW', 'RP', 'SL'])) {
            $holidays[] = ['date' => $easterDate->copy()->addDays(60), 'name' => 'Corpus Christi', 'name_de' => 'Fronleichnam'];
        }

        // Mariä Himmelfahrt (Assumption) - BY, SL
        if (in_array($state, ['BY', 'SL'])) {
            $holidays[] = ['date' => Carbon::create($year, 8, 15), 'name' => 'Assumption of Mary', 'name_de' => 'Mariä Himmelfahrt'];
        }

        // Weltkindertag - TH
        if ($state === 'TH') {
            $holidays[] = ['date' => Carbon::create($year, 9, 20), 'name' => "World Children's Day", 'name_de' => 'Weltkindertag'];
        }

        // Reformationstag - BB, HB, HH, MV, NI, SN, ST, SH, TH
        if (in_array($state, ['BB', 'HB', 'HH', 'MV', 'NI', 'SN', 'ST', 'SH', 'TH'])) {
            $holidays[] = ['date' => Carbon::create($year, 10, 31), 'name' => 'Reformation Day', 'name_de' => 'Reformationstag'];
        }

        // Allerheiligen (All Saints) - BW, BY, NW, RP, SL
        if (in_array($state, ['BW', 'BY', 'NW', 'RP', 'SL'])) {
            $holidays[] = ['date' => Carbon::create($year, 11, 1), 'name' => "All Saints' Day", 'name_de' => 'Allerheiligen'];
        }

        // Buß- und Bettag - SN
        if ($state === 'SN') {
            $nov23 = Carbon::create($year, 11, 23);
            $bussUndBettag = $nov23->copy()->previous(Carbon::WEDNESDAY);
            $holidays[] = ['date' => $bussUndBettag, 'name' => 'Repentance and Prayer Day', 'name_de' => 'Buß- und Bettag'];
        }

        return $holidays;
    }

    public function fetchSchoolBreaks(string $state, int $year): array
    {
        $stateName = self::STATE_URL_NAMES[$state] ?? $state;
        $url = "https://www.schulferien.org/Kalender_mit_Ferien/kalender_{$year}_ferien_{$stateName}.html";

        try {
            $response = Http::timeout(15)->get($url);

            if (!$response->successful()) {
                Log::warning("Failed to fetch school breaks from {$url}: {$response->status()}");
                return $this->getFallbackSchoolBreaks($state, $year);
            }

            $html = $response->body();
            return $this->parseSchoolBreaks($html, $year);
        } catch (\Exception $e) {
            Log::warning("Error fetching school breaks: {$e->getMessage()}");
            return $this->getFallbackSchoolBreaks($state, $year);
        }
    }

    private function parseSchoolBreaks(string $html, int $year): array
    {
        $breaks = [];

        $ferienTypes = [
            'Winterferien' => 'Winter Break',
            'Osterferien' => 'Easter Break',
            'Pfingstferien' => 'Whitsun Break',
            'Sommerferien' => 'Summer Break',
            'Herbstferien' => 'Autumn Break',
            'Weihnachtsferien' => 'Christmas Break',
        ];

        foreach ($ferienTypes as $nameDe => $nameEn) {
            $pattern = '/' . preg_quote($nameDe, '/') . '.*?(\d{1,2}\.\d{1,2}\.(?:\d{2,4})?)\s*[-–]\s*(\d{1,2}\.\d{1,2}\.(?:\d{2,4})?)/s';
            if (preg_match($pattern, $html, $matches)) {
                $startStr = $matches[1];
                $endStr = $matches[2];

                $start = $this->parseDateString($startStr, $year);
                $end = $this->parseDateString($endStr, $year);

                if ($start && $end && $start->lte($end)) {
                    $breaks[] = [
                        'name' => $nameEn,
                        'name_de' => $nameDe,
                        'start' => $start->format('Y-m-d'),
                        'end' => $end->format('Y-m-d'),
                    ];
                }
            }
        }

        if (empty($breaks)) {
            return $this->getFallbackSchoolBreaks('NW', $year);
        }

        return $breaks;
    }

    private function parseDateString(string $dateStr, int $defaultYear): ?Carbon
    {
        $dateStr = trim($dateStr, '. ');
        $parts = explode('.', $dateStr);

        if (count($parts) === 2) {
            return Carbon::create($defaultYear, (int) $parts[1], (int) $parts[0]);
        } elseif (count($parts) === 3) {
            $yearPart = (int) $parts[2];
            if ($yearPart < 100) {
                $yearPart += 2000;
            }
            return Carbon::create($yearPart, (int) $parts[1], (int) $parts[0]);
        }

        return null;
    }

    private function getFallbackSchoolBreaks(string $state, int $year): array
    {
        return [
            ['name' => 'Easter Break', 'name_de' => 'Osterferien', 'start' => "{$year}-04-07", 'end' => "{$year}-04-21"],
            ['name' => 'Summer Break', 'name_de' => 'Sommerferien', 'start' => "{$year}-07-07", 'end' => "{$year}-08-19"],
            ['name' => 'Autumn Break', 'name_de' => 'Herbstferien', 'start' => "{$year}-10-13", 'end' => "{$year}-10-25"],
            ['name' => 'Christmas Break', 'name_de' => 'Weihnachtsferien', 'start' => "{$year}-12-22", 'end' => "{$year}-12-31"],
        ];
    }

    public function getSchulferienUrl(string $state, int $year): string
    {
        $stateName = self::STATE_URL_NAMES[$state] ?? $state;
        return "https://www.schulferien.org/Kalender_mit_Ferien/kalender_{$year}_ferien_{$stateName}.html";
    }
}
