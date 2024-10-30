=== Lova Payment Gateway ===
Contributors: lovapay
Tags: lova, woocommerce, gateway, payment
Requires at least: 5.0
Tested up to: 6.1.1
Stable tag: 1.2.1
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
 
Lova je multifunkcionalna platforma sa mobilnom aplikacijom koja pruža usluge digitalnog novčanika i omogućava brzo primanje i slanje elektronskog novca, plaćanje i naplaćivanje roba i usluga jednostavnim skeniranjem QR koda ili unosa ID novčanika u trgovinama ili web shopovima te niz drugih mogućnosti i servisa.

== Šta je Lova Payment Gateway? ==

Lova Payment Gateway je plugin za WooCommerce koji za online trgovce omogućava jedinstven prihvat instant plaćanja putem Lova aplikacije. Ovaj plugin se veoma brzo i jednostavno integriše unutar bilo kojeg Web Shopa baziranog na WooCommerce, te se pri checkoutu pojavljuje kao nova vrsta plaćanja uz već postojeće kao što su plaćanje pouzećem, žiralno ili putem
platnih kartica (VISA, MASTER). Vaši će kupci korištenjem Lova Payment Gateway imati najbolje moguće korisničko iskustvo u procesu naplate. To će povećati vašu stopu konverzije i vjerovatnoću da se kupci vrate u vaš Web Shop.

Trenutno je Lova Payment Gateway za WooCommerce dostupan samo trgovcima u BOSNI I HERCEGOVINI.

== Zašto odabrati Lova Payment Gateway? ==

Prednosti plaćanja putem Lova Payment Gateway za trgovce su višestruke, a posebno bi izdvojili:
- Brzina – Transakcija se izvršava instantno i novac je ODMAH raspoloživ na digitalnom novčaniku trgovca,
- Dostupnost – naplatu za robu i usluge putem webshopa možete vršiti non-stop - 24/7,
- Ekosistem – Lova je najveći provajder digitalnih novčanika u BiH sa najvećim prometom i brojem korisnika,
- Kriptovalute – Korisnici imaju opciju plaćanja u kriptovalutama (trgovac uvijek naplaćuje i dobija KM),
- Transparentnost – pregled svih platnih transakcija i provizija unutar WordPressa - WooCommerca,
- Podrška – besplatna tehnička podrška za implementaciju, računovodstvo ali i za vaše korisnike,
- Cijena – bez fiksnih troškova za podešavanje, mjesečne naknade i skrivenih troškova (naplaćuje se samo provizija kada primate uplate putem Lova Payment Gateway) i sa najnižim provizijama za online plaćanja u BiH,
- Beslatno otkazivanje - kad god želite bez dodatnih troškova.

== Kako funkcioniše Lova Payment Gateway? ==

Prilikom odabira Lova Payment Gateway opcije plaćanja, korisnik se preusmjerava na link na kojem je prikazan QR kod koji sadrži podatke o trgovcu, broju narudžbe i iznosu plaćanja. Da izvrši plaćanje, korisnik skenira QR kod putem Lova mobilne aplikacije te nakon izvršenog plaćanja korisnik dobija potvrdu o plaćanju, a trgovac dobija novac na svoj digitalni novčanik te se u WooCommerce-u narudžba automatski prebacuje u status plaćene. Ukoliko korisnik ne izvrši plaćanje, vratiće se na odabir opcije plaćanja te može odabrati bilo koju drugu opciju. 
U slučaju potrebe, vrlo lako je moguće stornirati plaćenu transakciju i izvršiti povrat novca od strane trgovca.

== Installation ==

= MINIMALNI ZAHTJEVI =

* WooCommerce 3.3 ili noviji.
* WordPress 5.0 ili noviji.
* PHP verzija 7.2 ili novija.
* SSL mora biti instaliran na vašoj web lokaciji i aktivan na vašim stranicama za plaćanje.

= INSTALACIJA =

1. Da bi prihvatao Lova plaćanja, trgovac mora da ima registrovan i verifikovan nalog kao trgovac na Lova ili BCX platformi (www.lova.ba ili www.bcx.ba) te da ima aktiviranu opciju Web Shopa. Ukoliko nemate nalog, posjetite ovaj link i pratite uputstva.
2. Na Vašem Lova/BCX nalogu kreirajte API ključeve (API  key i API callback token) ili kontaktirajte naš tim koji će za Vas kreirati i poslati. 
3. Posjetite WordPress Admin > Plugins > Add New.
3. Potražite "Lova Payment Gateway for WooCommerce".
4. Instalirajte i aktivirajte Lova Payment Gateway for WooCommerce dodatak.

= PODEŠAVANJE I KONFIGURACIJA =

1. Idite na: WooCommerce > Postavke > Plaćanja > Lova Payment Gateway > Manage.
2. Omogućite kada budete spremni za produkciju.
3. Unesite Naslov i Opis koji se prikazuju kupcima pri naplati.
4. Unesite Email, Password, API key i API callback token trgovca koji ste kreirali ili dobili od našeg tima prilikom registracije na Lova ili BCX platformi.
5. Sačuvaj promjene.

Za više opcija instalacije provjerite [zvaničnu WordPress dokumentaciju](https://wordpress.org/support/article/managing-plugins/#manual-plugin-installation) o instaliranju dodataka.

== Screenshots ==

= Kreiranje API ključeva =

1. Potrebno je da se korisnik loguje na svoj LOVA/BCX nalog i da u meniju odabere „Podešavanja“ te odabere opciju „API podešavanja“.
2. Nakon toga je potrebno da u predviđeno polje unese 2FA sigurnosni kod sa mobilnog uređaja i klikne na polje „potvrditi“.
3. Nakon što kliknite na „kreiraj API ključeve“ dobićete vašu kombinaciju javnog i tajnog ključa koji će se koristiti pri podešavanju samog plugina u trećem koraku.

= Instaliranje Lova plugina u Wordpress =

4. Potrebno je da se ulogujete kao administrator u vaš WordPress sajt i da u meniju „Plugins“ odaberete opciju „Add New“.
5. Nakon toga kliknite na „Upload Plugin“.
6. Kliknite na „Choose File“ te odaberite ZIP fajl LOVA plugina.
7. Izaberi lova-payment-gateway.zip
8. Kliknite na „Install Now“.
9. Nakon završetka instalacije kliknite na „Activate Plugin“ da aktivirate plugin.

= Podešavanje plugina =

10. U wordpressu odaberite meni „WooCommerce“ i opciju „settings“ te podmeni „payments“. U izlistanim opcijama pronađite LOVA Payment Gateway i kliknite na „Manage“.
11. Ovde je potrebno da u predviđena polja unesete označene podatke.

= Postupak plaćanja =

12. Izaberite Lova Payment Gateway.
13. Skeniranjem QR koda unutar LOVA aplikacije korisniku se kreira zahtjev za plaćanjem sa svim ovim parametrima i korisnik može da potvrdi ili odbije zahtjev. Ovaj zahtjev je vremenski ograničen na 3 minute i ukoliko kupac ne plati u roku od ove 3 minute, zahtjev će isteći i korisnik će biti vraćen nazad na odabir vrste plaćanja gdje može odabrati drugi način plaćanja ili ponoviti zahtjev za LOVA plaćanje.
14. Ukoliko korisnik izvrši plaćanje, dobiće obavijest o uspješnom plaćanju, te će biti vraćen nazad na stranicu web shop-a sa potvrdom o plaćenoj narudžbi.

== Changelog == 

= 1.2.1 =
* Fix issue redirection link timeout

= 1.2.0 =
* Added transaction list to WP menu
* Revised settings form for test mode

= 1.1.0 =
* Fixed returnURL
* Added error notification
* Added production links

= 1.0.0 =
* Initial stable release.