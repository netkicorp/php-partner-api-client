# Netki PHP Partner Library

This is the Netki Partner library written in PHP. It allows you to use the Netki Partner API to CRUD all of your partner data:

* Wallet Names
* Domains
* Partners

### Example

```php

use Netki\NetkiClient;

$partnerId = "XXXXXXXXXXXXXXXXXXXX";
$apiKey = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";

$client = new NetkiClient($partnerId, $apiKey, "https://api.netki.com");

// Get All Domains
$domains = $client->get_domains();

// Create a new domain not belonging to a partner
$newTestDomain = $client->create_domain("testdomain.com", null);

// Get All Partners
$partners = $client->get_partners();

// Create a new partner
$newPartner = $client->create_partner("Partner");

// Create a new domain belonging to a partner
$partnerTestDomain = $client->create_domain("partnerdomain.com", $newPartner->id);

// Delete Domain
$partnerTestDomain->delete();

// Delete Partner
$newPartner->delete();

// Get All Wallet Names
$walletNames = $client->get_wallet_names();

// Update a Wallet Names BTC Wallet Address
$walletNameToUpdate = $walletNames[0];
$walletNameToUpdate->set_currency_address("btc", "3J98t1WpEZ73CNmQviecrnyiWrnqRhWNLy");
$walletNameToUpdate->save();

// Create a New Wallet Name
$walletName = $client->create_wallet_name("testdomain.com", "testwallet", "externalId");
$walletName->set_currency_address("btc", "1CpLXM15vjULK3ZPGUTDMUcGATGR9xGitv");
$walletName->save();

// Add Litecoin Wallet Address
$walletName->set_currency_address("ltc", "LQVeWKif6kR1Z5KemVcijyNTL2dE3SfYQM");
$walletName->save();

// Get all Wallet Names for a Domain
$filteredWalletNames = $client->get_wallet_names("testdomain.com", null);

// Get all Wallet Names matching an External ID
$filteredWalletNames = $client->get_wallet_names(null, "externalId");

// Get all Wallet Names for a Domain matching an External ID
$filteredWalletNames = $client->get_wallet_names("testdomain.com", "externalId");

// Get a Single Wallet Name by External ID and Change Name
$findOneWalletName = $client->get_wallet_names(null, "Wallet1ExternalId")
if(count($findOneWalletName) == 1) {
    $findOneWalletName[0]->$name = 'newname';
    $findOneWalletName[0]->save();
}

// Delete Wallet Name
$walletName->delete();
```