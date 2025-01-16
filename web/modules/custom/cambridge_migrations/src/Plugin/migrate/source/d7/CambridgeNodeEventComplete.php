<?php

namespace Drupal\cambridge_migrations\Plugin\migrate\source\d7;

use Drupal\node\Plugin\migrate\source\d7\NodeComplete;
use Drupal\migrate\Row;
use RRule\RfcParser;
use RRule\RRule;

/**
 * Drupal 7 all node revisions source, including translation revisions.
 *
 * For available configuration keys, refer to the parent classes.
 *
 * @see \Drupal\migrate\Plugin\migrate\source\SqlBase
 * @see \Drupal\migrate\Plugin\migrate\source\SourcePluginBase
 *
 * @MigrateSource(
 *   id = "d7_cam_node_event_complete",
 *   source_module = "node"
 * )
 */
class CambridgeNodeEventComplete extends NodeComplete {

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $return = parent::prepareRow($row);
    $field_event_date = $row->getSourceProperty('field_event_date');

    // We want to process only the first delta!
    if (!empty($field_event_date) && count($field_event_date) > 0) {
      $row->setSourceProperty('field_event_date', [0 => $this->getEventDate($field_event_date)]);
    }

    return $return;
  }

  /**
   * Clean up the original Drupal 7 values for the rrule.
   *
   * @param array $field_event_date
   *   The value from drupal 7.
   *
   * @return array
   *   The compatible value for drupal 10.
   */
  private function getEventDate(array $field_event_date) {
    $dtStartString = $field_event_date[0]['value'];
    $lines = [];

    if (!empty($field_event_date[0]['rrule'])) {
      try {
        $parts = $this->getRuleParts($field_event_date[0]['rrule']);
        $rrule = 'RRULE:' . $parts['RRULE'][0];
        $dtStart = \DateTime::createFromFormat('Y-m-d H:i:s', $dtStartString);
        $event_time = date('His\Z', $dtStart->getTimestamp());
      }
      catch (\Exception $e) {
      }
    } else {
      // Return as it is, there is no rrule to change.
      return $field_event_date[0];
    }

    // Parse "Include dates".
    $include_dates = [];
    if (!empty($parts['RDATE'])) {
      $dates = RfcParser::parseRDate('RDATE:' . $parts['RDATE'][0]);
      foreach ($dates as $date) {
        $include_dates[] = date('Ymd\T', $date->getTimestamp()) . $event_time;
      }
    }

    // Parse "Exclude dates".
    $exclude_dates = [];
    if (!empty($parts['EXDATE'])) {
      $excludes = RfcParser::parseExDate('EXDATE:' . $parts['EXDATE'][0]);
      foreach ($excludes as $date) {
        $exclude_dates[] = date('Ymd\T', $date->getTimestamp()) . $event_time;
      }
    }

    try {
      $rule_parts = RfcParser::parseRRule($rrule, $dtStart);
      $rule_parts = array_change_key_case($rule_parts, CASE_UPPER);
    }
    catch (\Exception $e) {
      // Return as it is, without any change.
      return $field_event_date[0];
    }

    // If we have "Exclude dates" then increase the count and re-generate the rrule string.
    if (isset($rule_parts['COUNT']) && is_numeric($rule_parts['COUNT']) && count($exclude_dates) > 0) {
      // Increase the count because we have "Exclude dates".
      $new_count = intval($rule_parts['COUNT']) + count($exclude_dates);
      $rrule = str_replace('COUNT=' . $rule_parts['COUNT'], 'COUNT=' . $new_count, $rrule);
    }

    $lines[] = $rrule;

    // Import the "Exclude dates" from drupal 7.
    if (count($exclude_dates) > 0) {
      $lines[] = 'EXDATE:' . implode(',', $exclude_dates);
    }

    // Import the "Include dates" from drupal 7.
    if (count($include_dates) > 0) {
      $lines[] = 'RDATE:' . implode(',', $include_dates);
    }

    $field_event_date[0]['rrule'] = implode("\n", $lines);
    return $field_event_date[0];
  }

  /**
   * Clean up the original Drupal 7 rrule parts.
   *
   * @param $string
   *   The Rrule from drupal 7.
   *
   * @return array
   *   The compatible rrule parts for drupal 10.
   */
  private function getRuleParts($string) {

    $parts = [
      'RRULE' => [],
      'RDATE' => [],
      'EXRULE' => [],
      'EXDATE' => [],
    ];

    $lines = explode("\n", $string);
    foreach ($lines as $n => $line) {
      $line = trim($line);

      if (str_contains($line, ':') === FALSE) {
        continue;
      }

      [$part, $partValue] = explode(':', $line, 2);
      $parts[$part][] = $partValue;
    }

    return $parts;
  }

}
