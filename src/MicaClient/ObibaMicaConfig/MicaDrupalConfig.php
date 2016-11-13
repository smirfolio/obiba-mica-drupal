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

namespace ObibaMicaClient;

class MicaConfig implements MicaConfigInterface {
  const CLIENT_CURRENT_USER = "obiba_mica_commons_get_current_user";
  const CLIENT_CURRENT_LANG = "obiba_mica_commons_get_current_lang";
  const CLIENT_VARIABLE_GET_VALUE = "variable_get_value";
  const CLIENT_VARIABLE_SET_VALUE = "variable_set_value";

  function __construct() {
  }

  static public function getCurrentLang(){
    $getCurrentLang = self::CLIENT_CURRENT_LANG;
    return $getCurrentLang();
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

