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

class MicaWatchDog implements MicaWatchDogInterface {
  const EMERGENCY = 0;
  const ALERT = 1;
  const CRITICAL = 2;
  const ERROR = 3;
  const WARNING = 4;
  const NOTICE = 5;
  const INFO = 6;
  const DEBUG = 7;
  const CLIENT_WATCH_DOG = "watchdog";


  function MicaWatchDogSeverity($severity = NULL) {
    return constant("self::$severity");
  }

  function MicaWatchDog($context, $message, $messageParameters = NULL, $severity = 6) {
    $drupalWatchDog = self::CLIENT_WATCH_DOG;
    $drupalWatchDog($context, $message, $messageParameters, $severity);
  }
}