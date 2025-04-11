<?php

declare(strict_types=1);

namespace Drupal\hello\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a Hello form.
 */
final class ExampleForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'hello_example';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $users = \Drupal::entityTypeManager()->getStorage('user')->loadMultiple();
    $options =[];
    foreach($users as $user){
      $options[$user->id()]=$user->getDisplayName();
    }

    // Récupération de la date de la dernière soumission
  $timestamp = \Drupal::state()->get('hello_form_submit');
  $last_submission = $timestamp ? \Drupal::service('date.formatter')->format($timestamp, 'short') : $this->t('No submission yet');

  // Titre avec date de dernière soumission
  $form['#title'] = $this->t('Formulaire');
  $form['last_submission'] = [
    '#markup' => '<p><em>' . $this->t('Last submission: @date', ['@date' => $last_submission]) . '</em></p>',
  ];


    $form['author'] = [
      '#type' => 'select',
      '#options'=>$options,
      '#title' => $this->t('Author'),
      '#required' => TRUE,
    ];
    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#required' => TRUE,
      '#maxlength'=> 15,
    ];


    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->t('Send'),
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    // @todo Validate the form here.
    // Example:
    // @code
    //   if (mb_strlen($form_state->getValue('message')) < 10) {
    //     $form_state->setErrorByName(
    //       'message',
    //       $this->t('Message should be at least 10 characters.'),
    //     );
    //   }
    // @endcode
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $node=\Drupal::entityTypeManager()->getStorage('node')->create([
      'type' => 'article',
      'status'=>1,
      'title' => $form_state->getValue('title'),
      'uid'=>$form_state->getValue('author'),

    ]);
    $node->save();


    $this->messenger()->addStatus($this->t('The message has been sent.'));
    $form_state->setRedirect('entity.node.canonical', ['node'=>$node->id()]);

    $now =\Drupal::time()->getCurrentTime();
    \Drupal::state()->set('hello_form_submit',$now);
  }

}
