<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

/**
 * Get the Deposit methods for Tier
 *
 * This method to replace the const below!
 *
 * private const DEPOSIT_TIERS = [
 *     'Tier 1' => [
 *         'deposit_payler' => 'on',
 *         'deposit_facilero' => 'on',
 *         'deposit_cashtocode' => 'on',
 *         'deposit_getkollo' => 'on',
 *         'deposit_changelly' => 'on',
 *         'deposit_tunzer_cc' => 'on',
 *         'deposit_kangol' => 'off',
 *         'deposit_ezeewallet' => 'on',
 *         'deposit_jeton' => 'on',
 *     ],
 *     'Tier 2' => [
 *         'deposit_payler' => 'off',
 *         'deposit_facilero' => 'on',
 *         'deposit_cashtocode' => 'on',
 *         'deposit_getkollo' => 'on',
 *         'deposit_changelly' => 'on',
 *         'deposit_tunzer_cc' => 'on',
 *         'deposit_kangol' => 'off',
 *         'deposit_ezeewallet' => 'on',
 *         'deposit_jeton' => 'on',
 *     ],
 *     'Tier 3' => [
 *         'deposit_payler' => 'off',
 *         'deposit_facilero' => 'off',
 *         'deposit_cashtocode' => 'on',
 *         'deposit_getkollo' => 'off',
 *         'deposit_changelly' => 'on',
 *         'deposit_tunzer_cc' => 'off',
 *         'deposit_kangol' => 'off',
 *         'deposit_ezeewallet' => 'on',
 *         'deposit_jeton' => 'on',
 *     ],
 * ];
 * self::DEPOSIT_TIERS[$request->tagData['tag']]
 *
 */
class GetDataTiers
{
    public static function handle(string $type)
    {
        return DB::table('tier_method_settings as tms')
            ->selectRaw('t.name as tier, p.name as provider, tms.direction, tms.enabled')
            ->join('tiers as t', 't.id', '=', 'tms.tier_id')
            ->leftJoin('providers as p', 'p.id', '=', 'tms.provider_id')
            ->where('tms.direction', $type)
            ->where('p.isActive', 1)
            ->groupBy('t.name', 'p.name', 'tms.direction', 'tms.enabled')
            ->get()
            ->reduce(function ($carry, $item) {
                $key = $item->direction . '_' . $item->provider;
                $carry[$item->tier][$key] = $item->enabled ? 'on' : 'off';
                return $carry;
            }, []);
    }
}
