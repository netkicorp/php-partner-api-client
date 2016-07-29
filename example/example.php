<?php

require '../vendor/autoload.php';
require '../init.php';

// Instantiate NetkiClient
$partnerId = "XXXXXXXXXXXXXXXXXXXX";
$apiKey = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
$client = new \Netki\NetkiClient($partnerId, $apiKey, "http://localhost:5000");


/* Lookup a Wallet Name. This call is useful on a SEND screen where your customer
might be looking up ANY Wallet Name. This includes Wallet Names that you do not
manage. e.g. wallet.yourcompany.name user sending to wallet.anothercompany.name user.
This will lookup Wallet Names that you manage as well, which is why it is perfect
for the SEND screen.
This call will return JSON that contains either a wallet address or bip21/72 URI. */
$lookupName = $client->lookup_wallet_name(
    'wallet.BruceWayne.rocks', // Fully qualified Wallet Name
    'btc' // Currency short code - Detailed codes here: http://docs.netki.apiary.io/#reference/partner-api/wallet-name-management
);
$lookupName->wallet_address; // Wallet Address To Send Funds To

// Lookup Available Currencies for a Wallet Name
$lookupCurrencies = $client->get_wallet_name_currencies('wallet.BruceWayne.rocks');
$lookupCurrencies->available_currencies; // All Currencies That a Wallet Name Contains Wallet Addresses for


// *** Wallet Name Management ***
// The following examples demonstrate common operations you will perform managing your customer's Wallet Names

/* Retrieve all Netki Wallet Names for your account. This call is useful if you need to make a mass update to your
Wallet Names. It is preferred that you provide specific parameters as covered in the next example below if you need to
retrieve a single Wallet Name. As your Wallet Name count grows, this data set will become large. It is also preferred
that you do not use this call to check if a Wallet Name already exists. Either attempt to fetch a Wallet Name
specifically by passing the appropriate externalId or simply try to create the Wallet Name. The server will provide the
appropriate messaging if the Wallet Name already exists. */
$walletNames = $client->get_wallet_names();


/* Retrieve a specific Netki Wallet Name based on the unique externalId that you
provided during creation. This is the preferred method of retrieving a
Wallet Name for the purposes of updating it or if necessary to check if the
Wallet Name has already been created. */
$walletNames = $client->get_wallet_names(null, "externalId");


/* Retrieve all Netki Wallet Names associated with a specific domainName. If
you have multiple domain names with which you use to create Wallet Names
this call is useful if you need to preform a mass update of all Wallet Names
specifically associated with a domain name. */
$filteredWalletNames = $client->get_wallet_names("testdomain.com", null);


// Create a new Netki Wallet Name by first creating the Wallet Name object
$walletName = $client->create_wallet_name(
    "yourwalletnamedomain.com", // Domain you use for customer Wallet Names.
    "username", // Your Users Wallet Name.
    "externalId" // Unique identifier you use to identify this user in your system.
);
/* The above example will yield a walletName object with a Wallet Name of username.yourwalletnamedomain.com
A real example is batman.tip.me
Substitute:
yourwalletnamedomain.com == tip.me
username == batman */

// Next set the desired currency and wallet address on the yielded walletName object.
$walletName->set_currency_address(
    "btc", // Currency for associated wallet address.
    "1CpLXM15vjULK3ZPGUTDMUcGATGR9xGitv" // Your users wallet address or endpoint to retrieve an address (HD Wallets)
);
$walletName->set_currency_address("ltc", "LQVeWKif6kR1Z5KemVcijyNTL2dE3SfYQM"); // Add additional addresses if desired

// Finally call save() to commit the Wallet Name to the Netki API.
$walletName->save();


// Get a Single Wallet Name by External ID. Add or Update a Wallet Name's BTC Wallet Address
$walletNames = $client->get_wallet_names(null, "externalId");
$walletName = $walletNames[0]; // Select Desired Wallet Name to Update
$walletName->set_currency_address("btc", "3J98t1WpEZ73CNmQviecrnyiWrnqRhWNLy");
$walletName->save();


// Get a Single Wallet Name by External ID. Update the Wallet Name "name" field.
$walletNames = $client->get_wallet_names(null, "Wallet1ExternalId");
$walletName = $walletNames[0]; // Select Desired Wallet Name
$walletName->$name = 'newname';
$walletName->save();


// Delete Wallet Name
$walletNames = $client->get_wallet_names(null, "Wallet1ExternalId");
$walletName = $walletNames[0]; // Select Desired Wallet Name
$walletName->delete();


// *** Partner Setup and Management ***
// Get All Domains Setup For Wallet Names
$domains = $client->get_domains();

// Create A New Domain For Your Wallet Names
$newTestDomain = $client->create_domain("yourwalletnamedomain.com", null);

// Delete A Wallet Name Domain
$partnerTestDomain->delete();

// * These Partner Calls Are Used For Managing Sub-Partners (Generally Used By Platform Providers) *
// Get All Partners
$partners = $client->get_partners();

// Create New Partner
$newPartner = $client->create_partner("Partner");

// Create New Domain To Be Used Specifically By A Partner
$partners = $client->get_partners();
$partner = $partners[0]; // Select Desired Partner
$partnerTestDomain = $client->create_domain("partnerdomain.com", $partner->id);

// Delete Partner
$partners = $client->get_partners();
$newPartner = $partner[0]; // Select Partner You Wish To Delete
$newPartner->delete();
