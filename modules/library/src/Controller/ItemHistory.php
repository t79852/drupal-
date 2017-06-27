<?php

namespace Drupal\library\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\library\Entity\LibraryAction;
use Drupal\library\Entity\LibraryItem;
use Drupal\library\Entity\LibraryTransaction;
use Drupal\node\NodeInterface;
use Drupal\user\Entity\User;

/**
 * Class ItemHistory.
 *
 * @package Drupal\library\Controller
 */
class ItemHistory extends ControllerBase {
  /**
   * Show.
   *
   * @param NodeInterface $node
   * @return string Return Hello string.
   * Return Hello string.
   */
  public function show(NodeInterface $node) {
    $output = [];
    foreach ($node->get('library_item')->getValue() as $item) {
      if (isset($item['target_id'])) {
        $itemEntity = LibraryItem::load($item['target_id']);
        if ($itemEntity) {
          $output[] = $this->showHistoryForItem($itemEntity);
        }
      }
    }

    if (empty($output)) {
      $output = [
        '#markup' => $this->t('No transactions found.'),
      ];
    }

    return $output;
  }

  private function showHistoryForItem(LibraryItem $item) {
    $data['heading'] = [
      '#markup' => '<h2>' . $item->get('barcode')->value . '</h2>',
    ];
    $data['elements'] = array(
      '#type' => 'table',
      '#title' => t('Item history'),
      '#header' => array('Librarian', 'Patron', 'Date', 'Action', 'Due Date', 'Notes'),
    );
    $transactions = \Drupal::entityQuery('library_transaction')
    ->condition('library_item', $item->id())
    ->execute();

    foreach ($transactions as $transaction) {
      $transactionEntity = LibraryTransaction::load($transaction);

      $due = '';
      if ($transactionEntity->get('due_date')->value > 0) {
        $due = \Drupal::service('date.formatter')->format($transactionEntity->get('due_date')->value);
      }

      $data['elements']['#rows'][$transactionEntity->get('id')->value] = [
        'librarian' => $this->formatUser($transactionEntity->get('librarian_id')),
        'patron' => $this->formatUser($transactionEntity->get('uid')),
        'date' => \Drupal::service('date.formatter')->format($transactionEntity->get('created')->value),
        'action' => $this->formatAction(
          $transactionEntity->get('action')->value,
          $transactionEntity->get('legacy_state_change')->value
        ),
        'due' => $due,
        'notes' => $transactionEntity->get('notes')->value,
      ];
    }

    if (isset( $data['elements']['#rows'])) {
      // Sort table by transaction date descending.
      $data['elements']['#rows'] = array_reverse($data['elements']['#rows']);
    } else {
      $data['elements'] = [
        '#markup' => '<h3>' . t('No transactions recorded.') . '</h3>',
      ];
    }

    return $data;
  }

  private function formatUser($idField) {
    $name = '';

    if ($idField) {
      if ($idField->getValue()) {
        $user = User::load($idField->getValue()[0]['target_id']);
        if ($user) {
          $name = $user->getDisplayName();
        }
      }
    }
    return $name;
  }

  private function formatAction($action, $legacy_state_change) {
    $actionLabel = '';
    if ($action) {
      $actionEntity = LibraryAction::load($action);
      if ($actionEntity) {
        $actionLabel = $actionEntity->label();
      } else {
        if ($legacy_state_change == LibraryAction::CHANGE_TO_AVAILABLE) {
          $actionLabel = $this->t('Item became available');
        } else if ($legacy_state_change == LibraryAction::CHANGE_TO_UNAVAILABLE) {
          $actionLabel = $this->t('Item became unavailable');
        }
      }
    }

    return $actionLabel;
  }

}
