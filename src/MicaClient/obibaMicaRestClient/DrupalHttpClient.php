<?php
/**
 * Created by PhpStorm.
 * User: samir
 * Date: 16-11-13
 * Time: 17:04
 */

namespace ObibaMicaClient;

class DrupalHttpClient {
  const CLIENT_ADD_HTTP_HEADER = "drupal_add_http_header";
  const GET_HTTP_CLIENT = 'obiba_mica_commons_get_http_client';
  const GET_HTTP_CLIENT_REQUEST = 'obiba_mica_commons_get_http_client_request';
  const GET_HTTP_CLIENT_EXCEPTION = '\HttpClientException';

  const METHOD_GET = 'GET';
  const METHOD_POST = 'POST';
  const METHOD_PUT = 'PUT';
  const METHOD_DELETE = 'DELETE';

  const AUTHORIZATION_HEADER = 'Authorization';
  const COOKIE_HEADER = 'Cookie';
  const SET_COOKIE_HEADER = 'Set-Cookie';
  const OBIBA_COOKIE = 'obibaid';
  const MICA_COOKIE = 'micasid';
  const HEADER_BINARY = 'application/octet-stream';
  const HEADER_JSON = 'application/json';
  const HEADER_CSV = 'text/csv';
  const HEADER_EXCEL_SHEET = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
  const HEADER_TEXT = 'text/plain';
  const HEADER_TEXT_HTML = 'text/html';
  const HEADER_APP_HTML_XML = 'application/xhtml+xml';
  const HEADER_APP_XML = 'application/xml;q=0.9';
  const HEADER_IMG_WEB = 'image/webp';
  const HEADER_MIXIN = '*/*;q=0.8';

  public $resource;
  public $drupalWatchDog;
  public $drupalConfig;
  public $headers = [];
  public $parameters = [];
  public $drupalMicaHttpClient;

  protected $httpType;
  protected $acceptHeaders;
  protected $lastResponse;
  protected $micaUrl;
  function __construct(MicaConfigInterface $micaConfig,
                       MicaWatchDogInterface $micaWatchDog) {
    $this->drupalConfig = $micaConfig;
    $this->drupalWatchDog = $micaWatchDog;
    $this ->micaUrl =  $micaConfig->micaGetConfig('mica_url') . '/ws';
  }

  function getMicaHttpClient($authentication = NULL, $formatter = NULL, $request_alter = FALSE, $delegate = NULL) {
    $getHttpClient = self::GET_HTTP_CLIENT;
    $http_client = $getHttpClient($authentication, $formatter, $request_alter, $delegate);
    return $http_client;
  }

  function getMicaHttpClientRequest($url, $values = array()) {
    $getHttpClientRequest = self::GET_HTTP_CLIENT_REQUEST;
    $http_client_request = $getHttpClientRequest($url, $values);
    return $http_client_request;
  }

  static public function getMicaHttpClientConst($const) {
    return constant("self::$const");
  }

  function MicaClientAddHttpHeader($headerParameter, $value) {
    $addHttpHeader = self::CLIENT_ADD_HTTP_HEADER;
    $addHttpHeader($headerParameter, $value);
  }


  function getMicaHttpClientException() {
    $clientHttpException = self::GET_HTTP_CLIENT_EXCEPTION;
    return $clientHttpException;
  }

  /**
   * Set Accept  http headers.
   *
   * @param $acceptType
   * @return $this
   */
  public function httpSetAcceptHeaders($acceptType) {
    $this->httpSetHeaders(array('Accept' => $acceptType));
    return $this;
  }

  /**
   * Set some http headers.
   *
   * @param $headers
   * @return $this
   */
  public function httpSetHeaders($headers) {
    foreach ($this->headers as $keyHeader => $valueHeader) {
      if (key($headers) == $keyHeader) {
        return $this;
      }
    }
    $this->headers = array_merge($this->headers, $headers);
    return $this;
  }

  /**
   * Perform the http query.
   *
   * @param $parameters
   * @param $ajax
   * @return $this
   */
  public function send($parameters = NULL, $ajax = FALSE) {
    $url = $this->micaUrl . $this->resource;
    $requestOptions = array(
      'method' => $this->httpType,
      'headers' => $this->headers,
    );
    if (!empty($parameters)) {
      $requestOptions = array_merge_recursive($requestOptions, $parameters);
    }
    $request = $this->getMicaHttpClientRequest($url, $requestOptions);
    $client = $this->client();
    try {
      $dataResponse = $client->execute($request);
      $this->lastResponse = $client->lastResponse;
      if ($ajax) {
        $headers = $this->httpGetLastResponseHeaders();
        if (!empty($headers) && !empty($headers['Location'])) {
          $this->micaClientAddHttpHeader('Location', $headers['Location'][0]);
        }
        $this->micaClientAddHttpHeader('Status', $this->httpGetLastResponseStatusCode());
        //   return $this->lastResponse->body;
      }
      return $dataResponse;
    } catch (\HttpClientException $e) {
      $this->drupalWatchDog->MicaWatchDog('MicaClient', 'Connection to server fail,  Error serve code : @code, message: @message',
        array(
          '@code' => $e->getCode(),
          '@message' => $e->getMessage(),
        ), $this->drupalWatchDog->MicaWatchDogSeverity('WARNING'));
      $this->lastResponse = $client->lastResponse;
      unset($this->dataResponse);
      if ($ajax) {
        $this->micaClientAddHttpHeader('Status', $e->getCode());
        return json_encode(array(
          'code' => $e->getCode(),
          'message' => $e->getMessage(),
        ));
      }
      else {
        $message_parameters['message'] = 'Connection to server fail,  Error serve code : @code, message: @message';
        $message_parameters['placeholders'] = array(
          '@code' => $e->getCode(),
          '@message' => $e->getMessage()
        );
        $message_parameters['severity'] = 'error';
        if ($e->getCode() == 500 || $e->getCode() == 503 || $e->getCode() == 0) {
          DrupalMicaError::DrupalMicaErrorHandler(TRUE, $message_parameters);
        }
        drupal_set_message(t($message_parameters['message'], $message_parameters['placeholders']), $message_parameters['severity']);
      }
    }
    return NULL;
  }

  /**
   * The client object construction overriding the httpClient->client() method.
   *
   * @return object
   *   The overridden object.
   */
  protected
  function client() {
    $default_headers_setter = function ($request) {
      $request->setHeader('Accept-Encoding', 'gzip, deflate');
    };
    $getHttpClient = self::GET_HTTP_CLIENT;
    $client = $getHttpClient(FALSE, FALSE, $default_headers_setter);

    if (!isset($client->options['curlopts'])) {
      $client->options['curlopts'] = array();
    }

    $client->options['curlopts'] += array(
      CURLOPT_SSL_VERIFYHOST => FALSE,
      CURLOPT_SSL_VERIFYPEER => FALSE,
      CURLOPT_ENCODING => TRUE,
    );
    return $client;
  }


  /**
   * Add authorization headers.
   *
   * @return array
   *   The transformed initial headers.
   */
  public function httpAuthorizationHeader() {

    if (isset($_SESSION[self::OBIBA_COOKIE])) {
      // Authenticate by cookies coming from request (case of regular user
      // logged in via Agate).
      $this->httpUpdateCookieHeaders($_SESSION[self::OBIBA_COOKIE],
        isset($_SESSION[self::MICA_COOKIE]) ?
          $_SESSION[self::MICA_COOKIE] : NULL);
    }
    else {

      $current_anonymous_pass = $this->drupalConfig->micaGetConfig('mica_anonymous_password');
      $saved_anonymous_pass = $this->drupalConfig->micaGetConfig('mica_anonymous_password_saved');

      // always append credentials in case of mica session has expired
      $credentials = $this->drupalConfig->micaGetConfig('mica_anonymous_name') . ':' . $current_anonymous_pass;
      $this->httpSetHeaders(array(
        self::AUTHORIZATION_HEADER => array(
          'Basic ' . base64_encode($credentials),
        )
      ));
      // detect if anonymous password was changed
      if ($current_anonymous_pass == $saved_anonymous_pass) {
        if (isset($_SESSION[self::MICA_COOKIE])) {
          $this->httpUpdateCookieHeaders(NULL,
            $_SESSION[self::MICA_COOKIE]);
        }
      }
      else {
        session_unset();
        $this->drupalConfig->micaSetConfig('mica_anonymous_password_saved',
          $this->drupalConfig->micaGetConfig('mica_anonymous_password'));
      }
    }
    return $this;
  }

  /**
   * Add the cookie Id/value to the header.
   *
   * @param string $obibaId
   *   The obibaid cookie id.
   * @param string $micaId
   *   The micaid cookie id.
   *
   * @return array
   *   The transformed initial headers.
   */
  protected function httpUpdateCookieHeaders($obibaId = NULL, $micaId = NULL) {
    $cookie = $this->HttpCookieHeaderValue($obibaId, $micaId);
    if (isset($this->headers[self::COOKIE_HEADER])) {
      array_push($this->headers[self::COOKIE_HEADER], $cookie);
    }
    else {
      $this->headers[self::COOKIE_HEADER] = array($cookie);
    }
    return $this;
  }

  /**
   * Add authorization by cookies header.
   *
   * @param string $obibaId
   *   The obibaid cookie id.
   * @param string $micaId
   *   The micaid cookie id.
   *
   * @return string
   *   The Value of the cooke.
   */
  protected function HttpCookieHeaderValue($obibaId = NULL, $micaId = NULL) {
    $cookie_parts = array();

    if (isset($obibaId)) {
      $cookie_parts[] = self::OBIBA_COOKIE . '=' . $obibaId;
    }

    if (isset($micaId)) {
      $cookie_parts[] = self::MICA_COOKIE . '=' . $micaId;
    }

    return implode("; ", $cookie_parts);
  }

  static function acceptHeaderArray($acceptType) {
    $acceptTypeHeaders = $acceptType;
    if (is_array($acceptType)) {
      $acceptTypeHeaders = array_map(function ($type) {
        $types = [];
        if (is_array($type)) {
          $types = array_map(function ($item) {
            return self::getMicaHttpClientConst($item);
          }, $type);
          return implode(', ', $types);
        }
        else {
          return self::getMicaHttpClientConst($type);
        }
      }, $acceptType);
      return $acceptTypeHeaders;
    }
    return array(self::getMicaHttpClientConst($acceptTypeHeaders));
  }

  /**
   * Set Accept  http headers.
   *
   * @param $acceptType
   * @return $this
   */
  public function httpSetContentTypeHeaders($acceptType) {
    $this->httpSetHeaders(array('Content-Type' => $acceptType));
    return $this;
  }

  /**
   * Get the last reposnse status code
   *
   * @return
   *   The status code
   */
  function httpGetLastResponseStatusCode() {
    if ($this->lastResponse != NULL) {
      return $this->lastResponse->responseCode;
    }
    return NULL;
  }

  /**
   * Get the last response headers (if any).
   *
   * @return array
   *   The header response server.
   */
  function httpGetLastResponseHeaders() {
    if ($this->lastResponse) {
      return $this->HttpGetHeaders($this->lastResponse->headers);
    }

    return array();
  }

  /**
   * Get a set of headers
   * @param $headers
   * @return mixed
   */
  function httpGetHeaders($headers) {
    if ($headers != NULL) {
      $result = array();
      foreach (explode("\r\n", $headers) as $header) {
        $h = explode(":", $header, 2);
        if (count($h) == 2) {
          if (!array_key_exists($h[0], $result)) {
            $result[$h[0]] = array();
          }
          array_push($result[$h[0]], trim($h[1]));
        }
      }
      return $result;
    }
    return array();
  }

}