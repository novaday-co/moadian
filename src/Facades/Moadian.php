<?php

namespace Novaday\Moadian\Facades;

/**
 * @method static string getNonce(int $validity = 30) 
 * @method static Novaday\Moadian\Http\Response getServerInfo()
 * @method static Novaday\Moadian\Http\Response getFiscalInfo()
 * @method static Novaday\Moadian\Http\Response inquiryByUid(string $uid, string $start = '', string $end = '')
 * @method static Novaday\Moadian\Http\Response inquiryByReferenceNumbers(string $referenceId, string $start = '', string $end = '')
 * @method static Novaday\Moadian\Http\Response getEconomicCodeInformation(string $taxID)
 * @method static Novaday\Moadian\Http\Response sendInvoice(Invoice $invoice)
 * 
 * @see \Novaday\Moadian\Moadian
 */

use Illuminate\Support\Facades\Facade;

class Moadian extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Novaday\Moadian\Moadian';
    }
}
