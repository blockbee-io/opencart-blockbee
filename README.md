[<a href="https://blockbee.io/"><img src="https://blockbee.io/static/assets/images/blockbee_logo_nospaces.png" width="300"/></a>](image.png)

# BlockBee Payment Gateway for OpenCart
Accept cryptocurrency payments on your OpenCart store

### Requirements:

```
OpenCart >= 4.0
```

### Description

Accept payments in Bitcoin, Bitcoin Cash, Litecoin, Ethereum, USDT and Matic directly to your crypto wallet.

#### Allow users to pay with crypto directly on your store

The BlockBee plugin extends OpenCart, allowing you to get paid in crypto directly on your store, with a simple setup.

= Accepted cryptocurrencies & tokens include: =

* (BTC) Bitcoin
* (ETH) Ethereum
* (BCH) Bitcoin Cash
* (LTC) Litecoin
* (MATIC) Matic
* (TRX) Tron
* (BNB) Binance Coin
* (USDT) USDT
* (SHIB) Shiba Inu
* (DOGE) Dogecoin


among many others, for a full list of the supported cryptocurrencies and tokens, check [this page](https://blockbee.io/fees/).

= Auto-value conversion =

BlockBee will attempt to automatically convert the value you set on your store to the cryptocurrency your customer chose.

Exchange rates are fetched every 5 minutes from CoinGecko.

Supported currencies for automatic exchange rates are:

* (XAF) CFA Franc
* (RON) Romanian Leu
* (BGN) Bulgarian Lev
* (HUF) Hungarian Forint
* (CZK) Czech Koruna
* (PHP) Philippine Peso
* (PLN) Poland Zloti
* (UGX) Uganda Shillings
* (MXN) Mexican Peso
* (INR) Indian Rupee
* (HKD) Hong Kong Dollar
* (CNY) Chinese Yuan
* (BRL) Brazilian Real
* (DKK) Danish Krone
* (AED) UAE Dirham
* (JPY) Japanese Yen
* (CAD) Canadian Dollar
* (GBP) GB Pound
* (EUR) Euro
* (USD) US Dollar

If your OpenCart's currency is none of the above, the exchange rates will default to USD.
If you're using OpenCart in a different currency not listed here and need support, please [contact us](https://blockbee.io) via our live chat.

#### Why choose BlockBee?

BlockBee has no setup fees, no monthly fees, no hidden costs, and you don't even need to sign-up!
Simply set your crypto addresses and you're ready to go. As soon as your customers pay we forward your earnings directly to your own wallet.

BlockBee has a low 1% fee on the transactions processed. No hidden costs.
For more info on our fees [click here](https://blockbee.io/fees)

### Installation

1. Open your OpenCart admin
2. Go to Extensions
3. Upload the .zip file

### Configuration

1. Access your OpenCart Admin Panel
2. Go to Extensions -> Extensions
3. Select "Payments"
4. Scroll down to "BlockBee"
5. Activate the payment method (if inactive)
6. Select which cryptocurrencies you wish to accept
7. Input your addresses to the cryptocurrencies you selected. This is where your funds will be sent to, so make sure the addresses are correct.
8. Save Changes
9. All done!

### Cronjob

<!-- Some features require a cronjob to work. You need to create one in your hosting that runs every 1 minute. It should call this URL ``YOUR-DOMAIN/index.php?route=extension/cryptapi/payment/cryptapi|cron``. -->

### Frequently Asked Questions

#### Do I need an API key?

Yes. To use our service you will need to register at our [dashboard](https://dash.blockbee.io/) and create a new API Key.

#### How long do payments take before they're confirmed?

This depends on the cryptocurrency you're using. Bitcoin usually takes up to 11 minutes, Ethereum usually takes less than a minute.

#### Is there a minimum for a payment?

Yes, the minimums change according to the chosen cryptocurrency and can be checked [here](https://blockbee.io/fees/).
If the OpenCart order total is below the chosen cryptocurrency's minimum, an error is raised to the user.

#### Where can I find more documentation on your service?

You can find more documentation about our service on our [website](https://blockbee.io/), our [technical documentation](https://docs.blockbee.io/) page or our [e-commerce](https://blockbee.io/ecommerce/) page.
If there's anything else you need that is not covered on those pages, please get in touch with us, we're here to help you!

#### Where can I get support?

The easiest and fastest way is via our live chat on our [website](https://blockbee.io) or via our [contact form](https://blockbee.io/contacts/).


### Changelog

#### 1.0.0
* Initial release.

#### 1.0.1
* Minor fixes and improvements.

#### 1.0.2
* Minor bugfixes.

#### 1.0.3
* Minor bugfixes.

#### 1.0.4
* Add new choices for order cancellation.
* Minor bugfixes.

### Upgrade Notice
* No breaking changes
