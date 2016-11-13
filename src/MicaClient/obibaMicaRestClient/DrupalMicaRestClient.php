<?php
/**
 * Copyright (c) 2016 OBiBa. All rights reserved.
 *
 * This program and the accompanying materials
 * are made available under the terms of the GNU Public License v3.0.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @file
 * MicaClient class
 */


namespace ObibaMicaClient;

class MicaRestClient extends DrupalHttpClient implements
  MicaRestClientInterface {
  function __construct(MicaConfigInterface $micaConfig,
                       MicaWatchDogInterface $micaWatchDog) {
    parent::__construct($micaConfig, $micaWatchDog);
    return $this;
  }


  function httpGet($resource, $parameters = NULL, $acceptType = NULL) {
    $this->resource = $resource;
    $this->parameters = !empty($parameters) ? $parameters : NULL;
    $this->httpType = $this->getMicaHttpClientConst('METHOD_GET');
    $this->httpSetAcceptHeaders(self::acceptHeaderArray($acceptType))
      ->httpAuthorizationHeader();
    if (!empty($parameters['headers']['Content-Type'])) {
      $this->httpSetContentTypeHeaders(self::acceptHeaderArray($parameters['headers']['Content-Type']));
      unset($parameters['headers']);
    }
    return $this;
  }

  function httpPost() {
    // TODO: Implement httpPost() method.
  }

  function httpPut() {
    // TODO: Implement httpPut() method.
  }

  function httpDelete() {
    // TODO: Implement httpDelete() method.
  }

  function httpDownload() {
    // TODO: Implement httpDownload() method.
  }

  function httpUpload() {
    // TODO: Implement httpUpload() method.
  }
}
