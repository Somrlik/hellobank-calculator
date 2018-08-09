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

}
