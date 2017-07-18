<?php

namespace Drupal\library;

use Drupal\Core\Form\FormStateInterface;
use Drupal\library\Entity\LibraryItem;
use Drupal\library\Plugin\Field\FieldWidget\LibraryItemFieldWidget;
use Drupal\node\Entity\Node;

/**
 * Performs widget submission.
 *
 * Widgets don't save changed entities, nor do they delete removed entities.
 * Instead, they flag them so that changes are only applied when the main form
 * is submitted.
 */
class WidgetSubmit {

  /**
   * Attaches the widget submit functionality to the given form.
   *
   * @param array $form
   *   The form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public static function attach(&$form, FormStateInterface $form_state) {
    foreach ($form['actions'] as $key => $action) {
      if (isset($form['actions'][$key]['#submit'])) {
        $form['actions'][$key]['#submit'][] = [get_called_class(), 'doSubmit'];
      }
    }
  }

  /**
   * Submits the widget elements, saving and deleted entities where needed.
   *
   * @param array $form
   *   The form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public static function doSubmit(array $form, FormStateInterface $form_state) {
    //TODO: Make field name dynamic
    $submittedValues = $form_state->getValue('library_item');
    $callback = $form_state->getFormObject();
    /** @var Node $node */
    $node = $callback->getEntity();
    $nodeChanged = FALSE;
    $savedReferences = $node->get('library_item')->getValue();
    foreach ($submittedValues as $delta => $value) {
      if (isset($value['library']['item_id'])) {
        $entity = LibraryItem::load($value['library']['item_id']);
      } else {
        $entity = LibraryItem::create();
      }
      //TODO: Make field name dynamic

      $generate = \Drupal::state()->get('library.barcode_base.node.library_item');
      if ($generate && empty($entity->get('barcode')->value)) {
        $entity->set('barcode', LibraryItemFieldWidget::findHighestBarcode() + 1);
      } else {
        $entity->set('barcode', $value['library']['barcode']);
      }

      if (!isset($value['library']['nid'])) {
        $entity->set('nid', ['target_id' => $node->id()]);
      }

      $entity->set('library_status', $value['library']['library_status']);
      $entity->set('notes', $value['library']['notes']);
      $entity->set('in_circulation', $value['library']['in_circulation']);
      foreach ($savedReferences as $key => $reference) {
        if (isset($value['library']['item_id']) && $reference['target_id'] == $value['library']['item_id'] && $value['remove'] == 1) {
          unset($savedReferences[$key]);
          //TODO: Make field name dynamic
          $nodeChanged = TRUE;
        }
      }
      if ($value['remove'] == 1) {
        $entity->delete();
      } else {
        $entity->save();
      }
      if (!isset($value['library']['item_id'])) {
        $savedReferences[] = ['target_id' => $entity->id()];
        //TODO: Make field name dynamic
        $nodeChanged = TRUE;
      }
    }
    if ($nodeChanged == TRUE) {
      $node->set('library_item', $savedReferences);
      $node->save();
    }
  }

}
