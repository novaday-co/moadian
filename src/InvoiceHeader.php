<?php

namespace Novaday\Moadian;

use DateTime;
use Novaday\Moadian\Services\VerhoeffService;

class InvoiceHeader
{
    /**
     * MOADIAN_USERNAME
     */
    public $clientId;

    /**
     * unique tax ID (should be set by setTaxID )
     */
    public $taxid;

    /**
     * invoice timestamp (milliseconds from epoch)
     */
    public $indatim;

    /**
     * invoice creation timestamp (milliseconds from epoch)
     */
    public $indati2m;

    /**
     * invoice type
     */
    public $inty;

    /**
     * internal invoice number
     */
    public $inno;

    /**
     * invoice reference tax ID
     */
    public $irtaxid;

    /**
     * invoice pattern
     */
    public $inp;

    /**
     * invoice subject
     */
    public $ins;

    /**
     * seller tax identification number
     */
    public $tins;

    /**
     * type of buyer
     */
    public $tob;

    /**
     * buyer ID
     */
    public $bid;

    /**
     * buyer tax identification number
     */
    public $tinb;

    /**
     * seller branch code
     */
    public $sbc;

    /**
     * buyer postal code
     */
    public $bpc;

    /**
     * buyer branch code
     */
    public $bbc;

    /**
     * flight type
     */
    public $ft;

    /**
     * buyer passport number
     */
    public $bpn;

    /**
     * seller customs licence number
     */
    public $scln;

    /**
     * seller customs code
     */
    public $scc;

    /**
     * contract registration number
     */
    public $crn;

    /**
     * customs declaration cottage number
     */
    public $cdcn;

    /**
     * customs declaration cottage date
     */
    public $cdcd;

    /**
     * billing ID
     */
    public $billid;

    /**
     * total pre discount
     */
    public $tprdis;

    /**
     * total discount
     */
    public $tdis;

    /**
     * total after discount
     */
    public $tadis;

    /**
     * total VAT amount
     */
    public $tvam;

    /**
     * total other duty amount
     */
    public $todam;

    /**
     * total bill
     */
    public $tbill;

    /**
     * total net weight
     */
    public $tonw;

    /**
     * total Rial value
     */
    public $torv;

    /**
     * total currency value
     */
    public $tocv;

    /**
     * settlement type
     */
    public $setm;

    /**
     * cash payment
     */
    public $cap;

    /**
     * installment payment
     */
    public $insp;

    /**
     * total VAT of payment
     */
    public $tvop;

    /**
     * tax17
     */
    public $tax17;

    public function __construct(string $username = null) {
        $this->clientId = $username;
    }

    public function toArray(): array
    {
        $arr = get_object_vars($this);
        unset($arr['clientId']);
        return $arr;
    }

    public function setTaxID(DateTime $date, int $internalInvoiceId)
    {
        $daysPastEpoch = $this->getDaysPastEpoch($date);
        $daysPastEpochPadded = str_pad($daysPastEpoch, 6, '0', STR_PAD_LEFT);
        $hexDaysPastEpochPadded = str_pad(dechex($daysPastEpoch), 5, '0', STR_PAD_LEFT);

        $numericClientId = $this->clientIdToNumber($this->clientId);

        $internalInvoiceIdPadded = str_pad($internalInvoiceId, 12, '0', STR_PAD_LEFT);
        $hexInternalInvoiceIdPadded = str_pad(dechex($internalInvoiceId), 10, '0', STR_PAD_LEFT);

        $decimalInvoiceId = $numericClientId . $daysPastEpochPadded . $internalInvoiceIdPadded;

        $checksum = VerhoeffService::checkSum($decimalInvoiceId);

        $this->taxid = strtoupper($this->clientId . $hexDaysPastEpochPadded . $hexInternalInvoiceIdPadded . $checksum);
    }

    private function getDaysPastEpoch(DateTime $date): int
    {
        return (int)($date->getTimestamp() / (3600 * 24));
    }

    private function clientIdToNumber(string $clientId): string
    {
        define('CHARACTER_TO_NUMBER_CODING', [
            'A' => 65, 'B' => 66, 'C' => 67, 'D' => 68, 'E' => 69, 'F' => 70, 'G' => 71, 'H' => 72, 'I' => 73,
            'J' => 74, 'K' => 75, 'L' => 76, 'M' => 77, 'N' => 78, 'O' => 79, 'P' => 80, 'Q' => 81, 'R' => 82,
            'S' => 83, 'T' => 84, 'U' => 85, 'V' => 86, 'W' => 87, 'X' => 88, 'Y' => 89, 'Z' => 90,
        ]);
    
        $result = '';
        foreach (str_split($clientId) as $char) {
            if (is_numeric($char)) {
                $result .= $char;
            } else {
                $result .= CHARACTER_TO_NUMBER_CODING[$char];
            }
        }
    
        return $result;
    }
}
