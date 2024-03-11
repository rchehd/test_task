<?php

namespace Drupal\test_task\Plugin\QueueWorker;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\test_task\BigEntityHelper;
use Drupal\test_task\BigEntityHelperInterface;
use Drupal\user\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines 'big_entity_processor' queue worker.
 *
 * @QueueWorker(
 *   id = "big_entity_processor",
 *   title = @Translation("BigEntityProcessor"),
 *   cron = {"time" = 60},
 * )
 */
final class BigEntityProcessor extends QueueWorkerBase implements ContainerFactoryPluginInterface {

  use StringTranslationTrait;

  /**
   * Constructs a new BigEntityProcessor instance.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected BigEntityHelperInterface $bigEntityHelper
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('test_task.big_entity_helper'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function processItem($data): void {
    foreach ($data as $big_entity_id) {
      /** @var \Drupal\Core\Entity\EntityInterface $big_entity */
      $big_entity = $this->entityTypeManager->getStorage('big_entity')->load($big_entity_id);
      $location = $big_entity->get('field_location')->getValue();
      $user = User::load($big_entity->uid());
      $admin = User::load(1);
      switch ($location) {
        case BigEntityHelper::LOCATION_OUTSIDE:
          $this->bigEntityHelper->addLogMessage($big_entity);
          $this->bigEntityHelper->sendMail($big_entity, $user, $this->t('Test'));
          $this->bigEntityHelper->setEntityProcessed($big_entity);
          break;

        case BigEntityHelper::LOCATION_INSIDE:
          $this->bigEntityHelper->addLogMessage($big_entity);
          $this->bigEntityHelper->sendMail($big_entity, $user, $this->t('Test'));
          $this->bigEntityHelper->sendMail($big_entity, $admin, $this->t('Admin'));
          $this->bigEntityHelper->setEntityProcessed($big_entity);
          break;
      }
    }
  }

}
