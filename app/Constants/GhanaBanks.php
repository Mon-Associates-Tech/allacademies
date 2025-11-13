<?php

namespace App\Constants;

class GhanaBanks
{
    /**
     * Get all Ghana bank codes and names
     *
     * @return array
     */
    public static function all(): array
    {
        return [
            '030100' => 'Absa Bank Ghana Limited',
            '280100' => 'Access Bank (Ghana) Plc',
            '080100' => 'Agricultural Development Bank Plc',
            '300341' => 'Affinity Ghana Savings and Loans',
            'ATL'    => 'AirtelTigo Money',
            '070101' => 'ARB Apex Bank',
            '210100' => 'Bank of Africa Ghana Limited',
            '010100' => 'Bank of Ghana',
            '300335' => 'Best Point Savings and Loans',
            '140100' => 'CalBank PLC',
            '340100' => 'Consolidated Bank Ghana Limited',
            '130100' => 'Ecobank Ghana Plc',
            '200100' => 'FBNBank Ghana Limited',
            '240100' => 'Fidelity Bank Ghana Limited',
            '170100' => 'First Atlantic Bank Limited',
            '330100' => 'First National Bank Ghana Limited',
            '040100' => 'GCB Bank Limited',
            '230100' => 'Guaranty Trust Bank (Ghana) Limited',
            'MTN'    => 'MTN Mobile Money',
            '050100' => 'National Investment Bank Limited',
            '360100' => 'OmniBSIC Bank Ghana Limited',
            '300457' => 'Paystack Limited',
            '180100' => 'Prudential Bank Limited',
            '110100' => 'Republic Bank (Ghana) PLC',
            '300361' => 'Services Integrity Savings and Loans',
            '090100' => 'Société Générale Ghana Plc',
            '190100' => 'Stanbic Bank Ghana Limited',
            '020100' => 'Standard Chartered Bank Ghana Plc',
            '060100' => 'United Bank for Africa Ghana Limited',
            '100100' => 'Universal Merchant Bank Ghana Limited',
            'VOD'    => 'Vodafone Cash',
            '120100' => 'Zenith Bank Ghana',
        ];
    }

    /**
     * Get bank name from code
     *
     * @param string $code
     * @return string
     */
    public static function getNameFromCode(string $code): string
    {
        $banks = self::all();
        return $banks[$code] ?? $code;
    }

    /**
     * Get bank codes only
     *
     * @return array
     */
    public static function codes(): array
    {
        return array_keys(self::all());
    }

    /**
     * Get bank names only
     *
     * @return array
     */
    public static function names(): array
    {
        return array_values(self::all());
    }

    /**
     * Check if a bank code exists
     *
     * @param string $code
     * @return bool
     */
    public static function codeExists(string $code): bool
    {
        return array_key_exists($code, self::all());
    }
}
