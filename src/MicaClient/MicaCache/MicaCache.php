<?php
/**
 * Created by PhpStorm.
 * User: samir
 * Date: 16-11-13
 * Time: 23:37
 */

namespace ObibaMicaClient;


class MicaCache implements  MicaCacheInterface{

  const CLIENT_GET_CACHE = "obiba_mica_commons_get_cache";
  const CLIENT_SET_CACHE = "obiba_mica_commons_set_cache";

  function clientGetCache($key) {
    $client_get_cache = self::CLIENT_GET_CACHE;
    $cached_result = $client_get_cache($key);
    if (!empty($cached_result)) {
      return $cached_result;
    }
    return FALSE;
  }

  function clientSetCache($key, $value) {
    $client_set_cache = self::CLIENT_SET_CACHE;
    $client_set_cache($key, $value);
  }

  public function IsNotEmptyStoredData($resources, $stored_data) {
    $entity_resource = explode('/', $resources);
    switch ($entity_resource[1]) {
      case 'taxonomy':
        if (!empty($stored_data)) {
          return TRUE;
        }
        break;
      case 'taxonomies' :
        if (!empty($stored_data)) {
          return TRUE;
        }
        break;
      case 'variables' :
        $coverage_resource = explode('?', $entity_resource[2]);
        if ($coverage_resource[0] !== '_coverage') {
          if (!empty($stored_data->variableResultDto->totalHits)) {
            return TRUE;
          }
        }
        else {
          if (!empty($stored_data->taxonomyHeaders) ||
            !empty($stored_data->vocabularyHeaders) ||
            !empty($stored_data->termHeaders) ||
            !empty($stored_data->rows)
          ) {
            return TRUE;
          }
        }
        break;
      case 'datasets' :
        if (!empty($stored_data->datasetResultDto->totalHits)) {
          return TRUE;
        }
        break;
      case 'studies' :
        if (!empty($stored_data->studyResultDto->totalHits)) {
          return TRUE;
        }
        break;
      case 'networks' :
        if (!empty($stored_data->networkResultDto->totalHits)) {
          return TRUE;
        }
        break;
      case 'network' :
        $stored_data;
        if (!empty($stored_data->networkSummaries)) {
          return TRUE;
        }
        break;
      default :
        return FALSE;
    }
  }
}