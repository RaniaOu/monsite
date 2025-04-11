<?php

declare(strict_types=1);

namespace Drupal\hello\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\node\Plugin\views\filter\Access;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a hello block.
 */
#[Block(
  id: 'hello_hello',
  admin_label: new TranslatableMarkup('Hello'),
  category: new TranslatableMarkup('Hello'),
)]
final class HelloBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs the plugin instance.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly Connection $connection,
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
      $container->get('database'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    //$current_user = \Drupal::currentUser();
   // $user_name = $current_user->getAccountName();
   $message = \Drupal::config('hello.settings')->get('message');


    $build['content'] = [
      '#theme'=> 'hello-block',
      '#message'=> $message,
      '#cache'=> [
        'tags'=>['config:hello.settings'],
      ]


    ];


    return $build;
  }

  protected function blockAccess(AccountInterface $account): AccessResult {
    return AccessResult::allowedIfHasPermission($account, 'Access Hello');

  }

}
