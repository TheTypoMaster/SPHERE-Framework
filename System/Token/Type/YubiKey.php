<?php
namespace SPHERE\System\Token\Type;

use SPHERE\System\Proxy\Proxy;
use SPHERE\System\Proxy\Type\Http;
use SPHERE\System\Token\ITypeInterface;
use SPHERE\System\Token\YubiKey\BadOTPException;
use SPHERE\System\Token\YubiKey\ComponentException;
use SPHERE\System\Token\YubiKey\KeyValue;
use SPHERE\System\Token\YubiKey\ReplayedOTPException;

/**
 * Class YubiKey
 *
 * @package KREDA\Sphere\Common\Support
 */
class YubiKey implements ITypeInterface
{

    /** @var string $KeyDelimiter */
    private $KeyDelimiter = '[:]';
    /** @var int $YubiApiTimeout */
    private $YubiApiTimeout = 3;

    /** @var int $YubiApiId */
    private $YubiApiId = 0;
    /** @var null|string $YubiApiKey */
    private $YubiApiKey = null;

    /** @var array $YubiApiEndpoint */
    private $YubiApiEndpoint = array(
        'api.yubico.com/wsapi/2.0/verify',
        'api2.yubico.com/wsapi/2.0/verify',
        'api3.yubico.com/wsapi/2.0/verify',
        'api4.yubico.com/wsapi/2.0/verify',
        'api5.yubico.com/wsapi/2.0/verify'
    );

    /**
     * @param string $Value
     *
     * @throws BadOTPException
     * @return bool|KeyValue
     */
    final public function parseKey($Value)
    {

        if (!preg_match("/^((.*)".$this->KeyDelimiter.")?".
            "(([cbdefghijklnrtuvCBDEFGHIJKLNRTUV]{0,16})".
            "([cbdefghijklnrtuvCBDEFGHIJKLNRTUV]{32}))$/",
            $Value, $Part)
        ) {
            throw new BadOTPException();
        }
        return new KeyValue($Part[3]);
    }

    /**
     * @param KeyValue $Key
     *
     * @return bool
     * @throws BadOTPException
     * @throws ComponentException
     * @throws ReplayedOTPException
     */
    final public function verifyKey(KeyValue $Key)
    {

        $Parameter = $this->createParameter($Key);
        $Query = $this->createSignature($Parameter);

        $QueryList = array();
        foreach ((array)$this->YubiApiEndpoint as $YubiApiEndpoint) {
            $QueryList[] = 'http://'.$YubiApiEndpoint."?".$Query;
        }

        $Proxy = (new Proxy(new Http()))->getProxy();
        $Option = array(
            CURLOPT_PROXY        => $Proxy->getHost(),
            CURLOPT_PROXYPORT    => $Proxy->getPort(),
            CURLOPT_PROXYUSERPWD => ( $Proxy->getUsername() === null | $Proxy->getPassword() === null
                ? null
                : $Proxy->getUsername().':'.$Proxy->getPassword()
            ),
            CURLOPT_TIMEOUT      => $this->YubiApiTimeout
        );
        $Option = array_filter($Option);

        $Result = $this->getRequest($QueryList, $Option);

        $Decision = array();
        foreach ((array)$Result as $Response) {
            if (preg_match("/status=([a-zA-Z0-9_]+)/", $Response, $Status)) {
                /**
                 * Case 1.
                 * OTP or Nonce values doesn't match - ignore response.
                 */
                if (!preg_match("/otp=".$Key->getKeyOTP()."/", $Response) ||
                    !preg_match("/nonce=".$Key->getKeyNOnce()."/", $Response)
                ) {
                    continue;
                } /**
                 * Case 2.
                 * We have a HMAC key.  If signature is invalid - ignore response.
                 * Return if status=OK or status=REPLAYED_OTP.
                 */
                elseif (null !== $this->YubiApiKey) {
                    if ($this->checkSignature($Response, $Status[1])) {
                        $Decision[] = 1;
                    } else {
                        $Decision[] = 0;
                    }
                } /** Case 3.
                 * We check the status directly
                 * Return if status=OK or status=REPLAYED_OTP.
                 */
                else {
                    switch ($Status[1]) {
                        case 'OK':
                            $Decision[] = 1;
                            break;
                        case 'BAD_OTP':
                            throw new BadOTPException($Status[1]);
                            break;
                        case 'REPLAYED_OTP':
                            $Decision[] = 0;
                            break;
                        default:
                            throw new ComponentException($Status[1]);
                    }
                }
            }
        }
        /**
         *
         */
        $Decision = array_sum($Decision) / ( count($Decision) > 0 ? count($Decision) : 1 );

        if ($Decision > 0.5) {
            return true;
        } elseif ($Decision == 0) {
            throw new ReplayedOTPException();
        } else {
            return false;
        }
    }

    /**
     * @param KeyValue $KeyValue
     *
     * @return string
     */
    private function createParameter(KeyValue $KeyValue)
    {

        $Parameter = array(
            'id'    => $this->YubiApiId,
            'otp'   => $KeyValue->getKeyOTP(),
            'nonce' => $this->createNOnce()
        );
        $KeyValue->setKeyNOnce($Parameter['nonce']);
        ksort($Parameter);
        $Query = '';
        foreach ($Parameter as $Key => $Value) {
            $Query .= "&".$Key."=".$Value;
        }
        return ltrim($Query, "&");
    }

    /**
     * @return string
     */
    private function createNOnce()
    {

        return md5(uniqid(rand()));
    }

    /**
     * @param string $Parameter
     *
     * @return string
     */
    private function createSignature($Parameter)
    {

        if (null !== $this->YubiApiKey) {
            $Signature = base64_encode(hash_hmac('sha1', $Parameter, $this->YubiApiKey, true));
            $Signature = preg_replace('/\+/', '%2B', $Signature);
            $Parameter .= '&h='.$Signature;
        }
        return $Parameter;
    }

    /**
     * @param string|array $UrlRequestList
     * @param array        $CurlOptionList
     *
     * @return array
     */
    public function getRequest($UrlRequestList, $CurlOptionList = array())
    {

        $CurlHandleList = array();
        $ResultData = array();
        $CurlHandler = curl_multi_init();
        /**
         * Setup
         */
        foreach ((array)$UrlRequestList as $Identifier => $UrlRequest) {
            $CurlHandleList[$Identifier] = curl_init($UrlRequest);
            curl_setopt($CurlHandleList[$Identifier], CURLOPT_USERAGENT, "KREDA YubiKey");
            curl_setopt($CurlHandleList[$Identifier], CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($CurlHandleList[$Identifier], CURLOPT_VERBOSE, true);
            curl_setopt($CurlHandleList[$Identifier], CURLOPT_HEADER, false);
            curl_setopt($CurlHandleList[$Identifier], CURLOPT_FAILONERROR, true);
            if (!empty( $CurlOptionList )) {
                curl_setopt_array($CurlHandleList[$Identifier], $CurlOptionList);
            }
            curl_multi_add_handle($CurlHandler, $CurlHandleList[$Identifier]);
        }
        /**
         * Execute
         */
        $IsRunning = null;
        do {
            curl_multi_exec($CurlHandler, $IsRunning);
        } while ($IsRunning > 0);
        /**
         * Collect
         */
        foreach ($CurlHandleList as $Identifier => $CurlHandle) {
            $ResultData[$Identifier] = curl_multi_getcontent($CurlHandle);
            curl_multi_remove_handle($CurlHandler, $CurlHandle);
        }
        curl_multi_close($CurlHandler);

        return $ResultData;
    }

    /**
     * @param $Result
     * @param $Status
     *
     * @return bool
     */
    private function checkSignature($Result, $Status)
    {

        $Response = array();
        $ResultLineList = explode("\r\n", trim($Result));
        foreach ($ResultLineList as $ResultLine) {
            $ResultLine = preg_replace('/=/', '#', $ResultLine, 1);
            $PartList = explode("#", $ResultLine);
            $Response[$PartList[0]] = $PartList[1];
        }

        $ApiParameterList = array(
            'nonce',
            'otp',
            'sessioncounter',
            'sessionuse',
            'sl',
            'status',
            't',
            'timeout',
            'timestamp'
        );
        sort($ApiParameterList);

        $Query = null;
        foreach ($ApiParameterList as $Parameter) {
            if (array_key_exists($Parameter, $Response)) {
                if ($Query) {
                    $Query = $Query.'&';
                }
                $Query = $Query.$Parameter.'='.$Response[$Parameter];
            }
        }

        $Signature = base64_encode(hash_hmac('sha1', utf8_encode($Query), $this->YubiApiKey, true));

        if ($Response['h'] == $Signature) {
            if ($Status == 'REPLAYED_OTP') {
                return false;
            }
            if ($Status == 'OK') {
                return true;
            }
        }
        return false;
    }

    /**
     * @param array $Configuration
     */
    public function setConfiguration($Configuration)
    {

        $this->YubiApiId = $Configuration['ApiId'];
        if (null !== $Configuration['ApiKey']) {
            $this->YubiApiKey = base64_decode($Configuration['ApiKey']);
        }
    }

    /**
     * @return string
     */
    public function getConfiguration()
    {

        return 'YubiKey';
    }
}
