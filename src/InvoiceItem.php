<?php

namespace Novaday\Moadian;

class InvoiceItem
{
/**
     * service stuff ID
     */
    public $sstid;

    /**
     * service stuff title
     */
    public $sstt;

    /**
     * amount
     */
    public $am;

    /**
     * measurement unit
     */
    public $mu;

    /**
     * net weight
     */
    public $nw;

    /**
     * fee (pure price per item)
     */
    public $fee;

    /**
     * fee in foreign currency
     */
    public $cfee;

    /**
     * currency type
     */
    public $cut;

    /**
     * exchange rate
     */
    public $exr;

    /**
     * service stuff Rial Value
     */
    public $ssrv;

    /**
     * service stuff currency value
     */
    public $sscv;

    /**
     * pre discount
     */
    public $prdis;

    /**
     * discount
     */
    public $dis;

    /**
     * after discount
     */
    public $adis;

    /**
     * VAT rate
     */
    public $vra;

    /**
     * VAT amount
     */
    public $vam;

    /**
     * other duty title
     */
    public $odt;

    /**
     * other duty rate
     */
    public $odr;

    /**
     * other duty amount
     */
    public $odam;

    /**
     * other legal title
     */
    public $olt;

    /**
     * other legal rate
     */
    public $olr;

    /**
     * other legal amount
     */
    public $olam;

    /**
     * construction fee
     */
    public $consfee;

    /**
     * seller profit
     */
    public $spro;

    /**
     * broker salary
     */
    public $bros;

    /**
     * total construction profit broker salary
     */
    public $tcpbs;

    /**
     * cash share of payment
     */
    public $cop;

    /**
     * vat of payment
     */
    public $vop;

    /**
     * buyer register number
     */
    public $bsrn;

    /**
     * total service stuff amount
     */
    public $tsstam;

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}