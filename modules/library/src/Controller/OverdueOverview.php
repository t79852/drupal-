<?php

namespace Drupal\library\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\library\Entity\LibraryItem;
use Drupal\library\Entity\LibraryTransaction;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;

/**
 * Class OverdueOverview.
 *
 * @package Drupal\library\Controller
 */
class OverdueOverview extends ControllerBase {
  /**
   * List.
   *
   * @return string
   *   Return Hello string.
   */
  public function listing() {

    $data['elements'] = [
      '#type' => 'table',
      '#title' => t('Item history'),
      '#header' => ['Item', 'Patron', 'Due Date', 'Last comment'],
    ];

    $items = \Drupal::entityQuery('library_item')
      ->condition('library_status', LibraryItem::ITEM_UNAVAILABLE)
      ->execute();

    $loadedItems = entity_load_multiple('library_item', $items);

    foreach ($loadedItems as $item) {
      $format_title = '';
      if ($item->get('nid')->getValue()) {
        $node = Node::load($item->get('nid')->getValue()[0]['target_id']);
        $label =  $node->getTitle();

        if ($item->get('barcode')->value) {
          $label .= ' (' . $item->get('barcode')->value . ')';
        }
        $format_title = [
          '#type' => 'link',
          '#title' =>$label,
          '#url' => Url::fromRoute('entity.node.canonical',['node' => $node->id()]),
        ];
        $format_title = drupal_render($format_title);

      }

      $transaction = \Drupal::entityQuery('library_transaction')
        ->condition('library_item', $item->id())
        ->condition('due_date', time(), '<')
        ->condition('due_date', 0, '>')
        ->sort('id', 'DESC')
        ->range(0,1)
        ->execute();

      if ($transaction && count($transaction) == 1) {
        $loadedTransaction = LibraryTransaction::load(array_pop($transaction));
        $patronName = '';
        if ($loadedTransaction->get('uid')->getValue()) {
          $patron = User::load($loadedTransaction->get('uid')->getValue()[0]['target_id']);
          if ($patron) {
            $patronName = [
                '#type' => 'link',
                '#title' => $patron->getDisplayName(),
                '#url' => Url::fromRoute('entity.user.canonical',['user' => $patron->id()]),
              ];
            $patronName = drupal_render($patronName);

            $patronId = $patron->id();
          }
        }

        $due = '';
        if ($loadedTransaction->get('due_date')->value > 0) {
          $due = \Drupal::service('date.formatter')->format($loadedTransaction->get('due_date')->value, 'short');
        }

        $data['elements']['#rows'][$patronId . '_' . $item->id()] = [
          $format_title,
          $patronName,
          $due,
          $loadedTransaction->get('notes')->value,
        ];
        ksort($data['elements']['#rows']);
      }
    }

    return $data;
  }

}
