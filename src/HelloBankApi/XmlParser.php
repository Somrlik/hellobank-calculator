<?php

namespace Somrlik\HelloBankApi;

use Nette\Utils\Strings;
use Somrlik\HelloBankApi\DataObjects\Barem;
use Somrlik\HelloBankApi\DataObjects\IncomeType;
use Somrlik\HelloBankApi\DataObjects\Insurance;
use Somrlik\HelloBankApi\DataObjects\Limit;

abstract class XmlParser {

    private function __construct() { }

    /**
     * Sometimes the API sends an XML that is not an XML.
     *
     * What?
     *
     * THIS IS WHY JESUS LEGALLY CHANGED HIS MIDDLE NAME TO FUCKING
     *
     * @param $xmlString
     * @return string
     */
    private static function fixXml($xmlString) {
        $xml = Strings::replace($xmlString, '/encoding=""/', 'encoding="utf-8"');
        return $xml;
    }

    /**
     * @param $content
     * @return array
     * @throws InvalidRequestException
     */
    public static function parseBaremsResponse($content) {
        $baremy = [];
        $content = self::fixXml($content);
        $xml = simplexml_load_string($content);

        $error = $xml->xpath('/bareminfo/chyba');
        if (! empty($error)) {
            throw new InvalidRequestException('There was an error in the incoming XML ' . $error[0][0]);
        }

        foreach ($xml as $barem) {
            $id = (string) $barem->attributes()->id;

            // limity
            $minuver = (int) $barem->uver->attributes()->min;
            $maxuver = (int) $barem->uver->attributes()->max;
            switch ((string) $barem->splatky->attributes()->type) {
                case 'fixed':
                    $minpocspl = $maxpocspl = (int) $barem->splatky->attributes()->value;
                    break;
                default:
                case 'range':
                    $minpocspl = (int) $barem->splatky->attributes()->min;
                    $maxpocspl = (int) $barem->splatky->attributes()->max;
                    break;
            }

            // odklad
            $reqodklad = (int) $barem->odklad->attributes()->required;
            switch ((string) $barem->odklad->attributes()->type) {
                case 'fixed':
                    $minodklad = $maxodklad = (int) $barem->odklad->attributes()->value;
                    break;
                case 'range':
                    $minodklad = (int) $barem->odklad->attributes()->min;
                    $maxodklad = (int) $barem->odklad->attributes()->max;
                    break;
                default:
                case 'none':
                    $minodklad = $maxodklad = null;
            }

            // prima platba
            $platba = (string) $barem->platba->attributes()->type;
            $platbaValue = (int) $barem->platba->attributes()->value;

            $baremy[$id] = new Barem(
                $id,
                trim((string) $barem->titul),
                new Limit($minuver, $maxuver, $minpocspl, $maxpocspl, $minodklad, $maxodklad, $reqodklad),
                $platba,
                $platbaValue
            );

        }
        return $baremy;
    }

    /**
     * @param $content
     * @return array
     * @throws InvalidRequestException
     */
    public static function parseInsuranceResponse($content) {
        $insuranceArray = [];
        $content = self::fixXml($content);
        $xml = simplexml_load_string($content);

        $error = $xml->xpath('/webciselnik/chyba');
        if (! empty($error)) {
            throw new InvalidRequestException('There was an error in the incoming XML ' . $error[0][0]);
        }

        foreach ($xml as $oneInsurance) {
            $id = (string) $oneInsurance->attributes()->id;
            $insuranceArray[$id] = new Insurance(
                $id,
                trim((string) $oneInsurance->titul),
                trim((string) $oneInsurance->popis),
                trim((string) $oneInsurance->napoveda)
            );
        }

        return $insuranceArray;
    }

    /**
     * @param $content
     * @return array
     * @throws InvalidRequestException
     */
    public static function parseIncomeTypeResponse($content) {
        $incomeTypes = [];
        $content = self::fixXml($content);
        $xml = simplexml_load_string($content);

        $error = $xml->xpath('/webciselnik/chyba');
        if (! empty($error)) {
            throw new InvalidRequestException('There was an error in the incoming XML ' . $error[0][0]);
        }

        foreach ($xml as $prijem_typ) {
            $id = (string) $prijem_typ->attributes()->id;
            $incomeTypes[$id] = new IncomeType(
                $id,
                trim((string) $prijem_typ->titul),
                trim((string) $prijem_typ->nasobek),
                trim((string) $prijem_typ->minimum)
            );
        }

        return $incomeTypes;
    }

    /**
     * @param                            $content
     * @param CurrencyFormatterInterface $currencyFormatter
     * @return mixed
     * @throws InvalidRequestException
     */
    public static function parseLoanCalculatorResponse($content, CurrencyFormatterInterface $currencyFormatter) {
        $content = self::fixXml($content);
        $xml = simplexml_load_string($content);

        $error = $xml->xpath('/webkalkulator/chyba');
        if (! empty($error)) {
            throw new InvalidRequestException('There was an error in the incoming XML ' . $error[0][0]);
        }

        $messages = [];
        foreach ((array) $xml->info->zprava as $message) {
            $messages[] = self::fixCzechForCalculatorMessage($message, $currencyFormatter);
        }
        $message = implode("\n", $messages);
        $out['info'] = $message;
        $out['status'] = (string) $xml->status;

        foreach ((array)$xml->vysledek as $key => $value) {
            if ($key == 'opce') continue;
            if (preg_match('/^[0-9]+,[0-9]+$/', $value))
                $value = str_replace(',', '.', $value);

            if (is_numeric($value))
                $out[$key] = (float) $value;
            else
                $out[$key] = (string) $value;
        }

        if ($out['status'] !== 'error') {
            if ((int) $xml->vysledek->opce->attributes()->enabled == 1) {
                $opce = [];
                foreach ((array)$xml->vysledek->opce as $key => $value) {
                    if (property_exists($opce, $key))
                        $opce[$key] = (string) $value;
                }
                $out['opce'] = $opce;
            }
        }

        return $out;
    }


    private static function fixCzechForCalculatorMessage($message, CurrencyFormatterInterface $currencyFormatter) {
        $mapping = [
            'pocet' => 'počet',
            'Prima' => 'Přímá',
        ];

        foreach ($mapping as $pattern => $replacement) {
            $message = str_replace($pattern, $replacement, $message);
        }

        if (Strings::contains($message, 'Přímá platba')) {
            $message = Strings::replace($message, '/[0-9]+/', function ($match) use ($currencyFormatter) {
                return $currencyFormatter->formatCurrency($match[0] * 100);
            });
        }

        return $message;
    }
}
