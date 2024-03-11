<?php declare(strict_types = 1);

namespace Drupal\test_task;

use Drupal\Core\Entity\EntityInterface;
use Drupal\user\UserInterface;

/**
 * @todo Add interface description.
 */
interface BigEntityHelperInterface {

  /**
   * Get Big Entities Ids for processing.
   *
   * @return array
   *   The array of entity ids.
   */
  public function getEntityIdsForProcessing(): array;

  /**
   * Add log message.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The Big entity.
   */
  public function addLogMessage(EntityInterface $entity): void;

  /**
   * Send mail.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The big entity.
   * @param \Drupal\user\UserInterface $addressee
   *   The addressee.
   */
  public function sendMail(EntityInterface $entity, UserInterface $addressee, string $subject): void;

  /**
   * Mark entity as processed.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The big entity.
   */
  public function setEntityProcessed(EntityInterface $entity): void;

}
