<?php

declare(strict_types=1);

namespace Drupal\hello\Access;

use DateTime;
use DateTimeInterface;
use Drupal\Component\Datetime\Time;


use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\Routing\Route;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Component\Datetime\TimeInterface;


/**
 * Checks access based on user account age and custom route requirement.
 */
final class HelloAccessChecker implements AccessInterface {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The time service.
   *
   * @var \Drupal\Core\Datetime\TimeInterface
   */
  protected TimeInterface $time;

  /**
   * Constructs a new HelloAccessChecker.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Datetime\TimeInterface $time
   *   The time service.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, TimeInterface $time)
{
    $this->entityTypeManager = $entityTypeManager;
    $this->time = $time;
}


  /**
   * Access callback to check if user account is older than required hours.
   *
   * @param \Symfony\Component\Routing\Route $route
   *   The route object.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The current user account.
   *
   * @return \Drupal\Core\Access\AccessResult
   *   The access result.
   */
  public function access(Route $route, AccountInterface $account): AccessResult {
    if ($account->isAnonymous()) {
      return AccessResult::forbidden();
    }

    $nbr_heures = (int) $route->getRequirement('_hello');

    /** @var \Drupal\user\Entity\User $user */
    $user = $this->entityTypeManager->getStorage('user')->load($account->id());

    if (!$user) {
      return AccessResult::forbidden();
    }

    $created = $user->getCreatedTime();
    $now = $this->time->getRequestTime();

    return AccessResult::allowedIf(($now - $created) > $nbr_heures * 3600);
  }

}
