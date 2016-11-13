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

namespace ObibaMicaClient\MicaConfig;

class MicaDrupalConfig implements MicaConfigInterface {
  const CLIENT_CURRENT_USER = "obiba_mica_commons_get_current_user";
  const CLIENT_ADD_HTTP_HEADER = "drupal_add_http_header";
  const CLIENT_WATCH_DOG = "watchdog";
  const CLIENT_VARIABLE_GET_VALUE = "variable_get_value";
  const CLIENT_VARIABLE_SET_VALUE = "variable_set_value";
  const CLIENT_GET_CACHE = "obiba_mica_commons_get_cache";
  const CLIENT_SET_CACHE = "obiba_mica_commons_set_cache";
  const GET_HTTP_CLIENT = 'obiba_mica_commons_get_http_client';
  const GET_HTTP_CLIENT_REQUEST_METHOD = 'obiba_mica_commons_get_http_client_request_method';
  const GET_HTTP_CLIENT_REQUEST = 'obiba_mica_commons_get_http_client_request';
  const GET_HTTP_CLIENT_EXCEPTION = '\HttpClientException';

  function __construct() {
  }

  public function micaGetConfigTest($key){
   return "the key is" .  $key ;
  }

  public function micaGetConfig($key) {
    $variableGetValue = self::CLIENT_VARIABLE_GET_VALUE;
    return $variableGetValue($key);
  }

  public function micaSetConfig($key, $value) {
    $variableSetValue = self::CLIENT_VARIABLE_SET_VALUE;
    $variableSetValue($key, $value);
  }

}
