<?php

/**
 * @file
 * Contains Drupal\library\Plugin\Field\FieldFormatter\LibraryItemFieldFormatter.
 */

namespace Drupal\library\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\SafeMarkup;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\library\Entity\LibraryAction;
use Drupal\library\Item;
use Drupal\library\LibraryItemInterface;

/**
 * Plugin implementation of the 'library_item_field_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "library_item_field_formatter",
 *   label = @Translation("Library item formatter"),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class LibraryItemFieldFormatter extends FormatterBase {
  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      // Implement default settings.
    ) + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    return array(
      // Implement settings form.
    ) + parent::settingsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = array(
      '#type' => 'table',
      '#title' => t('Library items'),
      '#header' => array('Barcode', 'Library status', 'Notes', 'Actions'),
    );
    $rows = [];
    foreach ($items as $delta => $target) {
      $item = entity_load('library_item', $target->getValue()['target_id']);
      if ($item->barcode || $item->in_circulation) {
        $rows[$delta]['barcode'] = nl2br(SafeMarkup::checkPlain($item->get('barcode')->value));
        $rows[$delta]['library_status'] = $this->checkAvailability($item->get('in_circulation')->value, $item->get('library_status')->value);
        $rows[$delta]['notes'] = nl2br(SafeMarkup::checkPlain($item->get('notes')->value));

        $actions = $this->getActions($item->get('in_circulation')->value, $item->get('library_status')->value, $target->getValue()['target_id']);
        if ($actions) {
          $actions = [
            '#type' => 'operations',
            '#links' => $actions,
          ];
          $rows[$delta]['actions'] = drupal_render($actions);
        } else {
          unset($elements['#header'][3]);
        }
      }
    }
    $elements['#rows'] = $rows;
    return $elements;
  }

  protected function checkAvailability($in_circulation, $status) {
    if ($in_circulation == LibraryItemInterface::REFERENCE_ONLY) {
      return t('Reference only');
    } else {
      if ($status == LibraryItemInterface::ITEM_AVAILABLE) {
        return t('Item available');

      } else {
        return t('Item unavailable');
      }
    }
  }


  protected function getActions($in_circulation, $status, $item) {
    $actions = [];
    if ($in_circulation == LibraryItemInterface::IN_CIRCULATION) {
      if ($status == LibraryItemInterface::ITEM_AVAILABLE) {
        $availableActions = \Drupal::entityQuery('library_action')
          ->condition('action', LibraryAction::CHANGE_TO_UNAVAILABLE)
          ->execute();
        $actions = $this->renderAction($availableActions, $item);
      } else {
        $query = \Drupal::entityQuery('library_action');
        $group = $query->orConditionGroup()
          ->condition('action', LibraryAction::CHANGE_TO_AVAILABLE)
          ->condition('action', LibraryAction::NO_CHANGE);
        $availableActions = $query
          ->condition($group)
          ->execute();
        $actions = $this->renderAction($availableActions, $item);
      }
    }
    return $actions;
  }

  private function renderAction($actions, $item) {
    $output = [];
    foreach ($actions as $action) {
      $actionEntity = LibraryAction::load($action);
      if ($actionEntity) {
        $output[$actionEntity->id()] = [
          'title' => $actionEntity->label(),
          'url' => Url::fromRoute('library.single_transaction', ['action' => $actionEntity->id(), 'item' => $item]),
        ];
      }
    }
    return $output;
  }

}
