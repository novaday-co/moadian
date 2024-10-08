# Laravel Moadian API Driver (API Only)

This Laravel package provides a convenient way to interact with the API of the "Moadian system" (سامانه مودیان) offered by intamedia.ir. With this package, you can easily make requests to the Moadian API and handle the responses in your Laravel application.

> [!IMPORTANT]
> This package provides access to the Moadian system API and is not intended for direct user interaction. It's designed for developers integrating Moadian functionality into their applications.

## Requirements

This package requires Laravel 8 and PHP 7.4 or higher.

## Installation

To install this package, simply run the following command:
```bash
composer require novaday-co/moadian
```
## Usage

To use this package, you will need to obtain a username, private key and certificate from [intamedia.ir](https://intamedia.ir). Once you have your credentials, you can configure the package in your Laravel application's `.env` file:

```
MOADIAN_USERNAME=your-username-here
MOADIAN_PRIVATE_KEY_PATH=/path/to/private.pem
MOADIAN_CERTIFICATE_PATH=/path/to/certificate.crt
```
> [!NOTE]
> The default location to store the private key is: storage_path('app/keys/private.pem');\
> The default location to store the certificate is: storage_path('app/keys/certificate.crt');

You can then use the `Moadian` facade to interact with the Moadian API. Here are some examples:

```php
use Novaday\Moadian\Facades\Moadian;

// Get server info
$info = Moadian::getServerInfo();

// Get fiscal info
$fiscalInfo = Moadian::getFiscalInfo();

// Get economic code information
$info = Moadian::getEconomicCodeInformation('10840096498');

// Inquiry by reference numbers
$info = Moadian::inquiryByReferenceNumbers('a45aa663-6888-4025-a89d-86fc789672a0');
```

### Send Invoice

To send an invoice to Moadian, you can use the sendInvoice() method provided by the plugin. Here's an example of how to use it:

```php
use Novaday\Moadian\Invoice as MoadianInvoice;
use Novaday\Moadian\InvoiceHeader;
use Novaday\Moadian\InvoiceItem;
use Novaday\Moadian\Payment;

public function sendInvoice($invoiceId = '') {
    $invoiceId = intval($invoiceId);
    $invoice = Invoice::find($invoiceId);

    if (!$invoice) {
        throw new Exception('Invoice not found');
    }

    $timestamp = Carbon::parse($invoice->date)->timestamp * 1000;

    $header = new InvoiceHeader(env('MOADIAN_USERNAME'));
    $header->setTaxID(Carbon::parse($invoice->date), $invoice->number);
    $header->indati2m = $timestamp;
    $header->indatim = $timestamp;
    $header->inty = 1; //invoice type
    $header->inno = $invoiceId;
    $header->irtaxid = null; // invoice reference tax ID
    $header->inp = $invoice->inp; //invoice pattern
    $header->ins = 1;
    $header->tins = env('TAXID');
    $header->tob = 2;
    $header->bid = $invoice->nationalnum;
    $header->tinb = $invoice->nationalnum;
    $header->bpc = $invoice->postal;

    $amount   = $invoice->items->sum('amount');
    $discount = $invoice->items->sum('discount');
    $vat      = $invoice->items->sum('vat');
    $header->tprdis = $amount;
    $header->tdis = $discount;
    $header->tadis = $amount - $discount;
    $header->tvam = $vat;
    $header->todam = 0;
    $header->tbill = $amount - $discount + $vat;
    $header->setm = $invoice->setm;
    $header->cap = $amount - $discount + $vat;

    $moadianInvoice = new MoadianInvoice($header);

    foreach ($invoice->items as $item) {
        $body = new InvoiceItem();
        $body->sstid = $item->seals->sstid;
        $body->sstt = $item->desc;
        $body->am = '1';
        $body->mu = 1627;
        $body->fee = $item->amount;
        $body->prdis = $item->amount;
        $body->dis = $item->discount;
        $body->adis = $item->amount - $item->discount;
        $body->vra = 9;
        $body->vam = $item->vat; // or directly calculate here like floor($body->adis * $body->vra / 100)
        $body->tsstam = $item->amount - $item->discount + $item->vat;
        $moadianInvoice->addItem($body);
    }

    foreach ($invoice->cashes as $cashe) {
        if ($cashe->active == 1) {
            $payment = new Payment();
            $payment->trn = $cashe->code;
            $payment->pdt = Carbon::parse($cashe->date)->timestamp * 1000;
            $moadianInvoice->addPayment($payment);
        }
    }

    $info = Moadian::sendInvoice($moadianInvoice);
    $info = $info->getBody();
    $info = $info['result'][0];

    $invoice->taxID           = $header->taxid;
    $invoice->uid             = $info['uid'] ?? '';
    $invoice->referenceNumber = $info['referenceNumber'] ?? '';
    $invoice->taxResult       = 'send';

    $invoice->save();
}
```

Note that you need to have a valid Moadian account and credentials to use this plugin.

There are other types of invoices (Cancellation, corrective, Sales return) that you can send with this package. For more information about different types of invoices and how to send them, please refer to the official document.

## Useful Links

  * [The regulation of store terminals](https://www.intamedia.ir/The-regulation-of-store-terminals)
  * [Guide to extract public key from digital signature](https://www.intamedia.ir/The-regulation-of-store-terminals/ID/15879/%D8%B1%D8%A7%D9%87%D9%86%D9%85%D8%A7%DB%8C-%D8%A7%D8%B3%D8%AA%D8%AE%D8%B1%D8%A7%D8%AC-%DA%A9%D9%84%DB%8C%D8%AF-%D8%B9%D9%85%D9%88%D9%85%DB%8C-%D8%A7%D8%B2-%D8%A7%D9%85%D8%B6%D8%A7%DB%8C-%D8%AF%DB%8C%D8%AC%DB%8C%D8%AA%D8%A7%D9%84%D9%85%D9%87%D8%B1%D8%B3%D8%A7%D8%B2%D9%85%D8%A7%D9%86%DB%8C-%D9%88-%D8%A8%D8%A7%D8%B1%DA%AF%D8%B0%D8%A7%D8%B1%DB%8C-%D8%A2%D9%86-%D8%AF%D8%B1-%DA%A9%D8%A7%D8%B1%D9%BE%D9%88%D8%B4%D9%87-%D8%B3%D8%A7%D9%85%D8%A7%D9%86%D9%87-%D9%85%D9%88%D8%AF%DB%8C%D8%A7%D9%86)

## Contributing

If you find a bug or would like to contribute to this package, please feel free to [submit an issue](https://github.com/novaday-co/moadian/issues) or [create a pull request](https://github.com/novaday-co/moadian/pulls).

## License

This package is open source software licensed under the [GPL-3.0 license](./LICENSE).
