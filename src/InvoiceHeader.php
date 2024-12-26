<?php

namespace Novaday\Moadian;

use DateTime;
use Novaday\Moadian\Contracts\Arrayable;
use Novaday\Moadian\Services\VerhoeffService;

class InvoiceHeader implements Arrayable
{
    use Concerns\Arrayable;

    protected const CHARACTER_TO_NUMBER_CODING = [
        'A' => 65, 'B' => 66, 'C' => 67, 'D' => 68, 'E' => 69, 'F' => 70, 'G' => 71, 'H' => 72, 'I' => 73,
        'J' => 74, 'K' => 75, 'L' => 76, 'M' => 77, 'N' => 78, 'O' => 79, 'P' => 80, 'Q' => 81, 'R' => 82,
        'S' => 83, 'T' => 84, 'U' => 85, 'V' => 86, 'W' => 87, 'X' => 88, 'Y' => 89, 'Z' => 90,
    ];

    /**
     * MOADIAN_USERNAME
     */
    protected string $clientId;

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): void
    {
        $this->clientId = $clientId;
    }

    /**
     * unique tax ID (should be set by setTaxID )
     */
    public string $taxid;

    /**
     * invoice timestamp (milliseconds from epoch)
     */
    public int $indatim;

    /**
     * invoice creation timestamp (milliseconds from epoch)
     */
    public ?int $indati2m;

    /**
     * invoice type
     */
    public int $inty;

    /**
     * internal invoice number
     */
    public ?string $inno;

    /**
     * invoice reference tax ID
     */
    public ?string $irtaxid;

    /**
     * invoice pattern
     */
    public int $inp;

    /**
     * invoice subject
     */
    public int $ins;

    /**
     * seller tax identification number
     */
    public string $tins;

    /**
     * type of buyer
     */
    public ?int $tob;

    /**
     * buyer ID
     */
    public ?string $bid;

    /**
     * buyer tax identification number
     */
    public ?string $tinb;

    /**
     * seller branch code
     */
    public ?string $sbc;

    /**
     * buyer postal code
     */
    public ?string $bpc;

    /**
     * buyer branch code
     */
    public ?string $bbc;

    /**
     * flight type
     */
    public ?int $ft;

    /**
     * buyer passport number
     */
    public ?string $bpn;

    /**
     * seller customs licence number
     */
    public ?string $scln;

    /**
     * seller customs code
     */
    public ?string $scc;

    /**
     * contract registration number
     */
    public ?string $crn;

    /**
     * billing ID
     */
    public ?string $billid;

    /**
     * total pre discount
     */
    public ?float $tprdis;

    /**
     * total discount
     */
    public ?float $tdis;

    /**
     * total after discount
     */
    public ?float $tadis;

    /**
     * total VAT amount
     */
    public float $tvam;

    /**
     * total other duty amount
     */
    public ?float $todam;

    /**
     * total bill
     */
    public float $tbill;

    /**
     * settlement type
     */
    public ?int $setm;

    /**
     * cash payment
     */
    public ?float $cap;

    /**
     * installment payment
     */
    public ?float $insp;

    /**
     * total VAT of payment
     */
    public ?float $tvop;

    /**
     * tax17
     */
    public ?float $tax17;

    /**
     * customs declaration cottage number
     */
    public ?string $cdcn;

    /**
     * customs declaration cottage date
     */
    public ?int $cdcd;

    /**
     * total net weight
     */
    public ?float $tonw;

    /**
     * total Rial value
     */
    public ?float $torv;

    /**
     * total currency value
     */
    public ?float $tocv;

    public ?string $tinc;

    public ?string $lno;

    public ?string $lrno;

    public ?string $ocu;

    public ?string $oci;

    public ?string $dco;

    public ?string $dci;

    public ?string $tid;

    public ?string $rid;

    public ?int $lt;

    public ?string $cno;

    public ?string $did;

    public ?array $sg;

    public ?string $asn;
    public ?int $asd;

    public function setTaxID(DateTime $date, int $internalInvoiceId)
    {
        $daysPastEpoch = $this->getDaysPastEpoch($date);
        $daysPastEpochPadded = str_pad((string)$daysPastEpoch, 6, '0', STR_PAD_LEFT);
        $hexDaysPastEpochPadded = str_pad(dechex($daysPastEpoch), 5, '0', STR_PAD_LEFT);

        $numericClientId = $this->clientIdToNumber($this->clientId);

        $internalInvoiceIdPadded = str_pad((string)$internalInvoiceId, 12, '0', STR_PAD_LEFT);
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
        $result = '';
        foreach (str_split($clientId) as $char) {
            if (is_numeric($char)) {
                $result .= $char;
            } else {
                $result .= self::CHARACTER_TO_NUMBER_CODING[$char];
            }
        }

        return $result;
    }
}
