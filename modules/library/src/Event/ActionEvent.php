<?php

namespace Drupal\library\Event;

use Drupal\library\Entity\LibraryTransaction;
use Symfony\Component\EventDispatcher\Event;

class ActionEvent extends Event
{

  private $transaction;

  public function __construct(LibraryTransaction $transaction)
  {
    $this->transaction = $transaction;
  }

  /**
   * Returns the kernel in which this event was thrown.
   *
   * @return LibraryTransaction
   */
  public function getTransaction()
  {
    return $this->transaction;
  }

}
