
<?php
require_once __DIR__ . '/vendor/autoload.php';

define('APPLICATION_NAME', 'Google Calendar API PHP Quickstart');
define('SERVICE_ACCOUNT_PATH', __DIR__ . '/calendar_secrets.json'); // where the Service Accounts credentials file lies

define('SCOPES', implode(' ', array(
  Google_Service_Calendar::CALENDAR)
));

class kalenderIKM {

  // Notes: for google calendar API references, consult: https://developers.google.com/google-apps/calendar/v3/reference/
  function __construct() {

    // Get the API client
    $this->client = $this->getClient();
    $this->client->addScope(Google_Service_Calendar::CALENDAR);

    // set the default timezone, to avoid GoogleCalendar API error
    date_default_timezone_set("Asia/Jakarta");

    // set calendar Id
    $this->calendarId = 'r54pt8hqhv8l05hm31pk7vrsfk@group.calendar.google.com'; // Google Calendar Id
  }

  /**
   * Returns list of events occuring between time range.
   * @param $startRange string PHP-accepted time string format describing start of the desired time range.
   * @param $endRange timestamp PHP-accepted time string format describing end of the time range.
   * @return googleAPIresponse Variable that contains response body as described in google's API
   */
  function getEventsBetweenTimeRange($startRange, $endRange) {

    // construct service object
    if(empty($this->service))
      $this->service = new Google_Service_Calendar($this->client);
    $service = $this->service;

    // Events on the user's calendar that lies between $startRange..$endRange
    $optParams = array(
      'orderBy' => 'startTime',
      'singleEvents' => TRUE,
      'timeMin' => date('c', strtotime($startRange)), // menampilkan acara yang BERAKHIR SETELAH timeMin
      'timeMax' => date('c', strtotime($endRange)), // menampilkan acara yang DIMULAI SEBELUM timeMax
    );
    $results = $service->events->listEvents($this->calendarId, $optParams);

    return $results;
  }

  /**
   * Verifikasi dalam rentang waktu yang diberikan apakah suatu event baru boleh dilaksanakan
   * @param $startRange string Waktu mulai dalam format PHP-accepted
   * @param $endRange string Waktu selesai dalam format PHP-accepted
   * @param $eventType integer Menyatakan tipe event sesuai structure.md. Terbagi menjadi:
   *    (1) Target peserta Umum. [color = 11, merah]
   *    (2) Target peserta Seluruh IKM. [color = 10, hijau]
   *    (3) Target peserta internal. [color = 5, kuning]
   *    (4) Target peserta lainnya. [color = unset | lainnya]
   *
   * @return boolean Apakah event dengan tipe $eventType boleh dilaksanakan di rentang waktu tersebut.
   */
  function isEventAllowed($startRange, $endRange, $eventType) {

    $this->events = $events = $this->getEventsBetweenTimeRange( $startRange, $endRange );

    if($eventType == 1 || $eventType == 2)
    {
      // tidak boleh bertabrakan dengan apapun
      if( count($events->getItems()) ) {
        $this->error = "Tipe event {$eventType} tidak boleh bertabrakan dengan event lain. ";
        $this->error .= "Dalam rentang waktu ini, terdapat " . count($events->getItems()) . " event lainnya.";
        return false;
      } else {
          return true;
      }
    } else if ($eventType == 3 || $eventType == 4)
    {
      // tidak boleh bertabrakan dengan event tipe (1) atau (2)
      $boleh = true;
      foreach($events->getItems() as $e) {
        if($e->getColorId() == 10 || $e->getColorId() == 11) {
          $this->error = "Bertabrakan dengan acara untuk umum/untuk IKM : " . $e->getSummary();
          $this->errorTrigger = $e;
          return false;
        }
      }
      return $boleh;
    }
  }

  /**
   * Membuat event baru di kalender
   *
   * @param $penyelenggara string Badan/Himpunan penyelenggara.
   * @param $judul string Judul acara.
   * @param $waktuMulai Waktu mulai acara, dalam PHP-accepted format.
   * @param $waktuSelesai Waktu selesai acara, dalam PHP-accepted format.
   * @param $tipe tipe_event
   *
   * @return eventObject Objek event sesuai GoogleCalendarAPI, jika berhasil, atau NULL jika gagal.
   */
  function createEvent($penyelenggara, $judul, $waktuMulai, $waktuSelesai, $tipe) {
    if(empty($this->service))
      $this->service = new Google_Service_Calendar($this->client);

    // set Summary
    $summary = "[{$penyelenggara}] $judul";

    // set $target variable for Description
    $target = "Target Peserta : ";
    if($tipe == 1) $target .= "Umum";
    else if($tipe == 2) $target .= "IKM";
    else if($tipe == 3) $target .= "Internal {$penyelenggara}";
    else $target .= "Lainnya";
    $target .= ".";

    if( !$this->isEventAllowed($waktuMulai, $waktuSelesai, $tipe) ) {
      // tidak boleh karena bentrok, tunjukkan error.
      // echo $this->error . "\n";
      return NULL;
    }

    $event = new Google_Service_Calendar_Event(array(
      'summary' => $summary,
      'description' => $target,
      'colorId' => $this->eventTypeToColorId($tipe),
      'start' => array(
        'dateTime' => date('c', strtotime($waktuMulai)),
        'timeZone' => 'Asia/Jakarta',
      ),
      'end' => array(
        'dateTime' => date('c', strtotime($waktuSelesai)),
        'timeZone' => 'Asia/Jakarta',
      )
    ));

    $event = $this->service->events->insert($this->calendarId, $event);
    // printf('Event created: %s\n%s\n\n', $event->htmlLink, var_export($event, 1));
    return $event;
  }

  /**
   * Mengembalikan tipe event sesuai dengan kode warna, as described in isEventAllowed().
   *
   * @param $colorId integer id warna yang ingin diubah
   * @return integer tipe event sesuai dengan warna
   */
  function colorIdToEventType($colorId) {
    if($colorId == 11) return 1;
    else if ($colorId == 10) return 2;
    else if ($colorId == 5) return 3;
    return 4;
  }

  /**
   * Mengembalikan kode warna sesuai dengan tipe event. Invers dari colorIdToEventType().
   *
   * @param $colorId integer Tipe event yang hendak di-convert.
   * @return integer Kode warna sesuai dengan tipe event.
   */
  function eventTypeToColorId($eType) {
    if($eType == 1) return 11;
    else if($eType == 2) return 10;
    else if($eType == 3) return 5;
    return 7; // default random colorId for other Events.
  }

  /**
   * Returns an authorized API client.
   * @return Google_Client the authorized client object
   */
  private function getClient() {
    $client = new Google_Client();
    $client->setApplicationName(APPLICATION_NAME);
    $client->addScope(SCOPES);
    $client->setAccessType('offline');

    putenv('GOOGLE_APPLICATION_CREDENTIALS=' . SERVICE_ACCOUNT_PATH);
    $client = new Google_Client();
    $client->useApplicationDefaultCredentials();

    return $client;

    /*
     * See:
     * https://github.com/google/google-api-php-client/blob/master/README.md#installation
     * https://developers.google.com/api-client-library/php/
     * https://developers.google.com/api-client-library/php/auth/service-accounts
     *
     * Origin:
     * http://stackoverflow.com/questions/8995451/how-do-i-connect-to-the-google-calendar-api-without-the-oauth-authentication
     */
  }

  function error() { return $this->error; }
}

$kalender = new kalenderIKM();

// echo "==== Now, we try to CREATE an Event ====\n";
// $es = $kalender->createEvent ('TBM', 'Rapat Bulanan', 'today 18.30 GMT+7', 'today 19.30 GMT+7', 3);
// if(!$es) {
//   echo $es->error . "\n";
// }
// printf("{%d}\n", $es);
