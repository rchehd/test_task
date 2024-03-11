<?php

namespace Drupal\test_task;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\user\UserInterface;

/**
 * Class of BigEntityHelper service.
 */
class BigEntityHelper implements BigEntityHelperInterface {

  use StringTranslationTrait;

  /**
   * Location "outside".
   */
  const LOCATION_OUTSIDE = 'outside';

  /**
   * Location "inside".
   */
  const LOCATION_INSIDE = 'inside';

  /**
   * The logger.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected LoggerChannelInterface $logger;

  /**
   * Constructs a BigEntityHelper object.
   */
  public function __construct(
    protected EntityTypeManagerInterface $entityTypeManager,
    protected LoggerChannelFactoryInterface $loggerFactory,
    protected TimeInterface $datetimeTime,
    protected MailManagerInterface $pluginManagerMail,
    protected MessengerInterface $messenger,
  ) {
    $this->logger = $loggerFactory->get('test_task');
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityIdsForProcessing(): array {
    $query = $this->entityTypeManager->getStorage('big_entity')->getQuery();
    $time = strtotime('-1 day', $this->datetimeTime->getCurrentTime());
    return $query
      ->accessCheck()
      ->condition('field_time_created', $time, '>=')
      ->condition('field_processed', 1, '<>')
      ->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function addLogMessage(EntityInterface $entity): void {
    $this->logger->notice('Processed entity ' . $entity->id());
  }

  /**
   * {@inheritdoc}
   */
  public function sendMail(EntityInterface $entity, UserInterface $addressee, string $subject): void {
    $module = 'test_task';
    $key = 'big_entity_processing';
    $to = $addressee->getEmail();
    $params = [
      'message' => $this->t('Test'),
      'subject' => $subject,
    ];
    $langcode = $addressee->getPreferredLangcode();
    $result = $this->pluginManagerMail->mail($module, $key, $to, $langcode, $params);
    if ($result['result']) {
      $message = $this->t('There was a problem sending your email notification to @email.', array('@email' => $to));
      $this->logger->error($message);
    }
    else {
      $message = $this->t('An email notification has been sent to @email ', array('@email' => $to));
      $this->logger->notice($message);
    }

    $this->messenger->addMessage($message);
  }

  /**
   * {@inheritdoc}
   */
  public function setEntityProcessed(EntityInterface $entity): void {
    $entity->set('field_processed', TRUE);
    try {
      $entity->save();
    }
    catch (EntityStorageException $e) {
      $this->logger->error($e->getMessage());
    }
  }

}
