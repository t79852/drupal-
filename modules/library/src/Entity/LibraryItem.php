<?php

namespace Drupal\library\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\library\LibraryItemInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Library item entity.
 *
 * @ingroup library
 *
 * @ContentEntityType(
 *   id = "library_item",
 *   label = @Translation("Library item"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\library\LibraryItemListBuilder",
 *     "views_data" = "Drupal\library\Entity\LibraryItemViewsData",
 *     "access" = "Drupal\library\LibraryItemAccessControlHandler",
 *   },
 *   base_table = "library_item",
 *   admin_permission = "administer library item entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *   }
 * )
 */
class LibraryItem extends ContentEntityBase implements LibraryItemInterface {
  use EntityChangedTrait;
  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += array(
      'user_id' => \Drupal::currentUser()->id(),
    );
  }

  public function setOwnerId($uid) {
    // Intentionally left blank.
  }

  public function getOwnerId() {
    // Intentionally left blank.
  }

  public function setOwner(UserInterface $account) {
    // Intentionally left blank.
  }

  public function getOwner() {
    // Intentionally left blank.
  }


  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the Library item entity.'))
      ->setReadOnly(TRUE);
    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Library item entity.'))
      ->setReadOnly(TRUE);

    $fields['nid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Node of item'))
      ->setDescription(t('The user ID of author of the Library item entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'node')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ),
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['barcode'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Barcode'))
      ->setDescription(t('The barcode of the Library item entity.'))
      ->setSettings(array(
        'max_length' => 50,
        'text_processing' => 0,
      ))
      ->setDefaultValue('')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => -4,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['in_circulation'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Reference only status'))
      ->setDescription(t('A boolean indicating whether the library item can be checked out.'))
      ->setDefaultValue(FALSE)
      ->setDisplayOptions('form', array(
        'type' => 'boolean_checkbox',
        'weight' => -4,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['library_status'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('The current status of the item.'))
      ->setDefaultValue(0);

    $fields['notes'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Notes'))
      ->setDescription(t('Special remarks for this item.'))
      ->setSettings(array(
        'max_length' => 254,
        'text_processing' => 0,
      ))
      ->setDefaultValue('')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => -4,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language code'))
      ->setDescription(t('The language code for the Library item entity.'))
      ->setDisplayOptions('form', array(
        'type' => 'language_select',
        'weight' => 10,
      ))
      ->setDisplayConfigurable('form', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
