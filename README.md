# Vzorová implementace splátkové kalkulačky [HELLOBANK](https://www.hellobank.cz)

je určena primárně pro programátory internetových e-shopů a jiných online nákupních a e-business aplikací.

## Požadavky

### Server
Webový server, PHP s SimpleXML a JSON, otevřená komunikace na Cetelem webové služby pomocí `file_get_content` (alternativně lze použít CURL).

### Podpora prohlížečů
Internet Eplorer 9+, Chrome, Firefox, Safari

## Použité komponenty
* [**LESS**](http://lesscss.org) – pro snadnější správu CSS souborů byl použit LESS. CSS soubory byly následně vygenerovány bez komprese, takže jsou nadále čitelné.
* [**SelectBoxIt**](http://gregfranko.com/jquery.selectBoxIt.js/) – na stylování SELECT tagů. 
* [**jQuery**](http://jquery.com) – veškerá dynamická funkcionalita na straně klienta byla implementovaná s pomocí jQuery.
* [**jQueryUI**](https://jqueryui.com) – pro větší uživatelské pohodlí byly nekteré standartní INPUT pole nahrazena dynamickými, např. Slider, Spinner.



## Changelog

`2.0.0` - 29. Června 2018

* Nová verze pro Hellobank

## Licence
Copyright (c) 2018 BNP Paribas Personal Finance SA, odštěpný závod (https://www.hellobank.cz) pod licencí MIT



Otázky pro Hello Bank/Cetelem
-----------------------------

Testovací účet
--------------

Provádím dotaz na
`https://www.cetelem.cz:8654/webciselnik2.php?kodProdejce=2044576&typ=info`
a dostávám s `Content-Type: text/xml`

```xml
<?xml version="1.0" encoding=""?>
<!DOCTYPE bareminfo SYSTEM "https://www.cetelem.cz:8654/bareminfo.dtd">
<bareminfo>
	<chyba>Prodejce nemá povolené splátky na webu</chyba>
</bareminfo>
```

Což není v [RFC 7303](https://tools.ietf.org/html/rfc7303#section-3) zmíněno,
podle [W3C](https://www.w3.org/TR/REC-xml/#sec-well-formed) to není XML a
podle [RFC 2046](https://tools.ietf.org/html/rfc2046#section-4.1.2) bych měl
považovat kódování za ASCII-US, protože má mime `text/*`.
Ani definované DTD není validní.
Takže tento endpoint považuji za nefunkční.

Provádím dotaz na
`https://www.cetelem.cz:8654/webkalkulator.php?kodProdejce=2044576`
a dostanu s `Content-Type: text/xml`

```xml
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE webkalkulator SYSTEM "https://www.cetelem.cz:8654/webkalkulator.dtd">
<webkalkulator>
	<status>error</status>
	<info>
		<zprava>Prodejce nema povolene splatky na webu</zprava>
	</info>
</webkalkulator>
```

Zde vypadá všechno v pořádku, akorát DTD je navalidní, je v něm nepopsaný element.

Provádím dotaz na
`https://www.cetelem.cz:8654/webciselnik2.php?kodProdejce=2044576&typ=pojisteni`
a dostanu s `Content-Type: text/xml`

```xml
<?xml version="1.0" encoding="windows-1250"?>
<webciselnik>
	<chyba>Prodejce nemá povolené splátky na webu</chyba>
</webciselnik>
```

Produkční requesty
------------------

`https://www.cetelem.cz/webciselnik2.php?kodProdejce=[PRODUCTION_CODE]&typ=info`

```xml
<?xml version="1.0" encoding="windows-1250"?>
<!DOCTYPE bareminfo SYSTEM "https://www.cetelem.cz/bareminfo.dtd">
<bareminfo>
	<barem id="104">
		<titul>10% + 10 x 10%</titul>
		<platba type="percent" value="10">přímá platba vždy 10% z ceny zboží</platba>
		<odklad type="none" required="0">bez odkladu splátek</odklad>
		<uver type="range" min="2000" max="300000">úvěr 2000,- až 300000,- Kč</uver>
		<splatky type="fixed" value="10">počet splátek 10</splatky>
	</barem>
	<barem id="133">
		<titul>Klasický úvěr</titul>
		<platba type="free">volitelná přímá platba od 0%</platba>
		<odklad type="range" min="2" max="3" required="0">možný odklad splátek 2 až 3 měsíce</odklad>
		<uver type="range" min="2000" max="300000">úvěr 2000,- až 300000,- Kč</uver>
		<splatky type="range" min="5" max="60">volitelný počet splátek od 5 do 60</splatky>
	</barem>
</bareminfo>
```

Což není validní podle DTD.

`https://www.cetelem.cz/webkalkulator.php?kodProdejce=[PRODUCTION_CODE]`

Je v pohodě, až na to, že to DTD je nevalidní.

`https://www.cetelem.cz/webciselnik2.php?kodProdejce=[PRODUCTION_CODE]&typ=pojisteni`

```xml
<?xml version="1.0" encoding="windows-1250"?>
<webciselnik>
	<pojisteni id="T1">
		<titul>SOUBOR MASTER</titul>
		<popis>4,99 % z měsíční splátky úvěru</popis>
		<napoveda><![CDATA[SOUBOR MASTER v sobě zahrnuje pojištění pro případ ztráty zaměstnání nebo hospitalizace*, pracovní neschopnosti, invalidity III. stupně a úmrtí. Úhrada za pojištění je 4,99 % z měsíční splátky úvěru. <small>* Pojištěný je pojištěn vždy pouze na jedno z těchto rizik dle jeho aktuálního zaměstnaneckého statutu</small>]]></napoveda>
	</pojisteni>
	<pojisteni id="T2">
		<titul>SOUBOR MASTER PLUS</titul>
		<popis>4,99 % z měsíční splátky úvěru plus 49 Kč</popis>
		<napoveda><![CDATA[SOUBOR MASTER PLUS v sobě zahrnuje pojištění pro případ ztráty zaměstnání nebo hospitalizace*, pracovní neschopnosti, invalidity III. stupně, úmrtí, Pojištění odcizení věci, na kterou byl poskytnut úvěr**, a Pojištění Home Assistance. Úhrada za pojištění je 4,99 % z měsíční splátky úvěru plus 49 Kč. <small>* Pojištěný je pojištěn vždy pouze na jedno z těchto rizik dle jeho aktuálního zaměstnaneckého statutu<br /><strong>** Pojistitelná věc: černá a bílá technika, šedá technika, nábytek, sportovní nářadí a vybavení, videohry, hudební nástroje, knihy</strong></small>]]></napoveda>
	</pojisteni>
	<pojisteni id="S0">
		<titul>BEZ POJIŠTĚNÍ</titul>
		<popis>Bez pojištění</popis>
		<napoveda><![CDATA[Tato varianta v sobě neobsahuje pojištění schopnosti splácet úvěr.]]></napoveda>
	</pojisteni>
</webciselnik>
```

Což pro jistotu nemá DTD.
