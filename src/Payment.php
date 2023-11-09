<?php

namespace Novaday\Moadian;

class Payment
{
    /**
     * Iin number
     */
    public $iinn;

    /**
     * acceptor number
     */
    public $acn;

    /**
     * terminal number
     */
    public $trmn;

    /**
     * payment method
     */
    public $pmt;

    /**
     * tracking number
     */
    public $trn;

    /**
     * payer card number
     */
    public $pcn;

    /**
     * payer id
     */
    public $pid;

    /**
     * payment DateTime
     */
    public $pdt;

    /**
     * payment value
     */
    public $pv;

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}