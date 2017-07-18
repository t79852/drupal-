<?php

namespace Drupal\library\Helper;


use Drupal\library\Entity\LibraryAction;
use Drupal\library\Entity\LibraryItem;

class LibraryItemHelper {

  /**
   * @param $barcode
   * @return bool|LibraryItem
   */
  public static function findByBarcode($barcode) {
    $items = [];
    $results = \Drupal::entityQuery('library_item')
      ->condition('barcode', $barcode)
      ->execute();
    foreach ($results as $result) {
      $items[] = LibraryItem::load($result);
    }
    if (count($items) == 1 && $items[0] instanceof LibraryItem) {
      return $items[0];
    } else {
      return FALSE;
    }
  }

  /**
   * @param integer $item
   * @param string $action
   */
  public static function updateItemAvailability($item, $action) {
    $action = LibraryAction::load($action);
    $item = LibraryItem::load($item);
    if ($action->action() == LibraryAction::CHANGE_TO_UNAVAILABLE) {
      $item->set('library_status', LibraryItem::ITEM_UNAVAILABLE);
      $item->save();
    } else if ($action->action() == LibraryAction::CHANGE_TO_AVAILABLE) {
      $item->set('library_status', LibraryItem::ITEM_AVAILABLE);
      $item->save();
    }
  }

  /**
   * @param integer $item
   *
   * @return int
   */
  public static function computeDueDate($action) {
    $action = LibraryAction::load($action);
    $due = 0;
    if ($action->action() != LibraryAction::CHANGE_TO_AVAILABLE) {
      // TODO: Make due configurable
      $due = strtotime('today') + (86400 * 30);
    }
    return $due;
  }
}