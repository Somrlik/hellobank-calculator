<?php

namespace Somrlik\HelloBankApi;

use Http\Client\Exception;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Nette\Utils\Arrays;
use Nette\Utils\Strings;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Somrlik\HelloBankApi\DataObjects\Barem;

class HelloBankApi {

    const TEST_MERCHANT_ID = 2044576;

    const ENVIRONMENT_DUMMY = 'dummy';
    const ENVIRONMENT_TEST = 'uat';
    const ENVIRONMENT_PRODUCTION = 'production';

    const ENVIRONMENT_MAP = [
        'local' => self::ENVIRONMENT_DUMMY,
        'dummy' => self::ENVIRONMENT_DUMMY,
        'test' => self::ENVIRONMENT_TEST,
        'uat' => self::ENVIRONMENT_TEST,
        'debug' => self::ENVIRONMENT_TEST,
        'production' => self::ENVIRONMENT_PRODUCTION,
    ];

    const REQUEST_TYPE_INFO = 'info';
    const REQUEST_TYPE_INSURANCE = 'pojisteni';
    const REQUEST_TYPE_CALCULATOR = 'kalkulator';
    const REQUEST_TYPE_INCOME_TYPE = 'prijem_typ';

    /**
     * @var int
     */
    private $merchantId = self::TEST_MERCHANT_ID;

    /**
     * @var string
     */
    private $environment = self::ENVIRONMENT_TEST;

    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * @var HttpClient|null
     */
    private $httpClient;

    /**
     * @var CurrencyFormatterInterface
     */
    private $currencyFormatter;

    /**
     * @var Barem[]
     */
    private $barems;

    /**
     * @var array
     */
    private $insurance;

    /**
     * @var array
     */
    private $incomeType;

    /**
     * HelloBankApi constructor.
     *
     * @param int                        $merchantId
     * @param string                     $environment
     * @param RequestFactoryInterface    $requestFactory
     * @param HttpClient|null            $httpClient
     * @param CurrencyFormatterInterface $currencyFormatter
     */
    public function __construct(
        int $merchantId = self::TEST_MERCHANT_ID,
        string $environment = self::ENVIRONMENT_TEST,
        RequestFactoryInterface $requestFactory = null,
        HttpClient $httpClient = null,
        CurrencyFormatterInterface $currencyFormatter = null
    ) {
        $this->merchantId = $merchantId;
        $this->environment = Arrays::get(self::ENVIRONMENT_MAP, $environment, null);

        $this->requestFactory = $requestFactory ?? MessageFactoryDiscovery::find();
        $this->httpClient = $httpClient ?? HttpClientDiscovery::find();
        $this->currencyFormatter = $currencyFormatter ?? new CZKCurrencyFormatter();
    }

    /**
     * @param $url
     * @return bool|string
     * @throws InvalidRequestException
     */
    private function getResourceFromUrl($url) {
        if (Strings::startsWith($url, 'file://')) {
            $contents = file_get_contents($url);
        } else {
            if ($this->requestFactory === null) {
                throw new InvalidRequestException('You did not specify a request factory for an HTTP client.');
            }
            $request = $this->requestFactory->createRequest('GET', $url);
            try {
                $response = $this->httpClient->sendRequest($request);
            } catch (\Exception $e) {
                throw new InvalidRequestException('Failed to establish HTTP connection.');
            } catch (Exception $e) {
                throw new InvalidRequestException('Failed to establish HTTP connection.');
            }

            if ($response->getStatusCode() !== 200) {
                throw new InvalidRequestException('The HelloBank API responded with code other than 200.');
            }

            $contents = (string) $response->getBody();
            // @todo Remove this debugging line
            // file_put_contents('in.xml', $contents);
        }
        return $contents;
    }

    /**
     * @return Barem[]
     * @throws InvalidRequestException
     */
    public function getBarems() {
        if (! empty($this->barems)) return $this->barems;

        $url = self::getUrlForVendorAndEnvironment(
            $this->merchantId,
            $this->environment,
            self::REQUEST_TYPE_INFO
        );

        $contents = $this->getResourceFromUrl($url);
        $barems = XmlParser::parseBaremsResponse($contents);
        $this->barems = $barems;
        return $barems;
    }

    /**
     * @return array
     * @throws InvalidRequestException
     */
    public function getInsurance() {
        if (! empty($this->insurance)) return $this->insurance;

        $url = self::getUrlForVendorAndEnvironment(
            $this->merchantId,
            $this->environment,
            self::REQUEST_TYPE_INSURANCE
        );

        $contents = $this->getResourceFromUrl($url);
        $insurance = XmlParser::parseInsuranceResponse($contents);
        $this->insurance = $insurance;
        return $insurance;
    }

    /**
     * @return array
     * @throws InvalidRequestException
     */
    public function getIncomeType() {
        if (! empty($this->incomeType)) return $this->incomeType;

        $url = self::getUrlForVendorAndEnvironment(
            $this->merchantId,
            $this->environment,
            self::REQUEST_TYPE_INCOME_TYPE
        );

        $contents = $this->getResourceFromUrl($url);
        $incomeType = XmlParser::parseIncomeTypeResponse($contents);
        $this->incomeType = $incomeType;
        return $incomeType;
    }

    /**
     * @return LoanCalculator
     */
    public function getLoanCalculator() {
        return new LoanCalculator($this);
    }

    /**
     * @return int
     */
    public function getMerchantId(): int {
        return $this->merchantId;
    }

    /**
     * @return string
     */
    public function getEnvironment(): string {
        return $this->environment;
    }

    /**
     * @return CurrencyFormatterInterface
     */
    public function getCurrencyFormatter(): CurrencyFormatterInterface {
        return $this->currencyFormatter;
    }

    public function getAjaxHandler() {
        if (empty($this->ajaxHandler)) {
            $this->ajaxHandler = new AjaxHandler($this);
        }

        return $this->ajaxHandler;
    }

    /**
     * @param int     $vendorId The vendor ID
     * @param string  $environment The environment, one of ENVIRONMENT constants from this class
     * @param string  $type The type, one of REQUEST_TYPE constants from this class
     * @return string The url of the required resource
     * @throws InvalidRequestException When the requested combination does not exist
     */
    private static function getUrlForVendorAndEnvironment(int $vendorId, string $environment, string $type) {
        $urls = [
            self::ENVIRONMENT_DUMMY => [
                self::REQUEST_TYPE_INFO => 'file://' . __DIR__ . '/../../data/info.xml',
                self::REQUEST_TYPE_INSURANCE => 'file://' . __DIR__ . '/../../data/pojisteni.xml',
                self::REQUEST_TYPE_CALCULATOR => 'file://' . __DIR__ . '/../../data/kalkulace.xml',
                self::REQUEST_TYPE_INCOME_TYPE => 'file://' . __DIR__ . '/../../data/prijem_typ.xml',
            ],
            self::ENVIRONMENT_TEST => [
                self::REQUEST_TYPE_INFO =>
                    'https://www.cetelem.cz:8654/webciselnik2.php?kodProdejce='.$vendorId.'&typ=info',
                self::REQUEST_TYPE_INSURANCE =>
                    'https://www.cetelem.cz:8654/webciselnik2.php?kodProdejce='.$vendorId.'&typ=pojisteni',
                self::REQUEST_TYPE_CALCULATOR =>
                    'https://www.cetelem.cz:8654/webkalkulator.php?kodProdejce='.$vendorId,
                self::REQUEST_TYPE_INCOME_TYPE => 'file://' . __DIR__ . '/../../data/prijem_typ.xml',
            ],
            self::ENVIRONMENT_PRODUCTION => [
                self::REQUEST_TYPE_INFO =>
                    'https://www.cetelem.cz/webciselnik2.php?kodProdejce='.$vendorId.'&typ=info',
                self::REQUEST_TYPE_INSURANCE =>
                    'https://www.cetelem.cz/webciselnik2.php?kodProdejce='.$vendorId.'&typ=pojisteni',
                self::REQUEST_TYPE_CALCULATOR => 
                    'https://www.cetelem.cz/webkalkulator.php?kodProdejce='.$vendorId,
                self::REQUEST_TYPE_INCOME_TYPE => 'file://' . __DIR__ . '/../../data/prijem_typ.xml',
            ],
        ];

        if (! empty($urls[$environment])) {
            if (! empty($urls[$environment][$type])) {
                return $urls[$environment][$type];
            }
        }

        throw new InvalidRequestException('Requested invalid environment ' . $environment . ' and type ' . $type);
    }

}
