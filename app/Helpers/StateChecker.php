<?php


namespace App\Helpers;


class StateChecker
{
    public static function index(string $state): string
    {
        $res = '';

        $currentStates = [
            ['full' => 'NewSouthWales', 'short' => 'NSW'],
            ['full' => 'Victoria', 'short' => 'VIC'],
            ['full' => 'Queensland', 'short' => 'QLD'],
            ['full' => 'Tasmania', 'short' => 'TAS'],
            ['full' => 'SouthAustralia', 'short' => 'SA'],
            ['full' => 'WesternAustralia', 'short' => 'WA'],
            ['full' => 'NorthernTerritory', 'short' => 'NT'],
            ['full' => 'AustralianCapitalTerritory', 'short' => 'ACT'],
        ];

        foreach ($currentStates as $currentState) {
            if($currentState['full'] === str_replace(' ', '', $state)){
                $res = $currentState['short'];
            }
        }
        return $res;
    }
}
