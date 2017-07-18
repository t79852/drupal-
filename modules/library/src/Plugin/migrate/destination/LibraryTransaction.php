<?php

/**
 * @file
 * Contains \Drupal\book\Plugin\migrate\destination\Book.
 */

namespace Drupal\library\Plugin\migrate\destination;

use Drupal\Core\Entity\EntityInterface;
use Drupal\migrate\Plugin\migrate\destination\EntityContentBase;
use Drupal\migrate\Row;

/**
 * @MigrateDestination(
 *   id = "library_transaction",
 *   provider = "library"
 * )
 */
class LibraryTransaction extends EntityContentBase {

  /**
   * {@inheritdoc}
   */
  protected static function getEntityTypeId($plugin_id) {
    return 'library_transaction';
  }

}
