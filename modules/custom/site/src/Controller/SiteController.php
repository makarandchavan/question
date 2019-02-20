<?php

namespace Drupal\site\Controller;

use Drupal\Core\Controller\ControllerBase;
use \Drupal\node\Entity\Node;
use \Drupal\file\Entity\File;
use \Drupal\field_collection\Entity\FieldCollectionItem;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Database\Connection;

/**
 * Controller routines for site example routes.
  */
class SiteController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  protected function getModuleName() {
    return 'site';
  }
  
  /**
   * Login form handle.
   */
  public function handleLogin() {
    global $base_url;
    $mail = $_POST['email'];
    $password = $_POST['password'];

    if(isset($mail)) {
      $users = \Drupal::entityTypeManager()->getStorage('user')->loadByProperties(['mail' => $mail]);
      $user = reset($users);
       if(!empty($user)) {
        $username = $user->getUsername();

        if(!empty($username)) {
          $uid = \Drupal::service('user.auth')->authenticate($username, $password);
          $logged_in = User::load($uid);
          if(!empty($logged_in)) {
            user_login_finalize($logged_in);
            $user_destination = \Drupal::destination()->get();
          }
        }
      }
      if ($user_destination) {
        echo $base_url . '/user/' . $user->id() . '/edit';
      }
      else {
        echo '0';
      }
      exit;
    }
  }

  /**
   * Register form handle.
   */
  public function handleRegister() {
    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();

    $form_values = $_POST;

    // Check if user exists already.
    $users = \Drupal::entityTypeManager()->getStorage('user')->loadByProperties(['mail' => $form_values['email']]);
    $user = reset($users);

    if(!empty($user)) {
      echo '0';
    }
    else {
      $user = \Drupal\user\Entity\User::create();

      // Mandatory.
      $user->setPassword($form_values['password']);
      $user->enforceIsNew();
      $user->setEmail($form_values['email']);
      $user->setUsername($form_values['email']);

      // Optional.
      $user->set('init', 'email');
      $user->set('langcode', $language);
      $user->set('preferred_langcode', $language);
      $user->set('preferred_admin_langcode', $language);


      $user->set('field_first_name', $form_values['firstname']);
      $user->set('field_last_name', $form_values['lastname']);
      $user->set('field_mobile_number', $form_values['phone']);
      
      $user->addRole($form_values['who_am_i']);

      // Save user account.
      $result = $user->save();

      if (!empty($user)) {
        // Send confirmation email.
        \Drupal::service('email_confirmer')->confirm($form_values['email']);
      }

      if($result) {
        echo '1';
      }
    }
    exit;
  }

  /**
   * Profile form handle.
   */
  public function handleProfile() {
    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();

    $form_values = $_POST;

    $user = User::load(\Drupal::currentUser()->id());
    
    if(empty($user)) {
      echo '0';
    }
    else {
      $user->set('field_first_name', $form_values['fname']);
      $user->set('field_last_name', $form_values['lname']);
      $user->set('field_mobile_number', $form_values['phone']);
      $user->set('field_date_of_birth', $form_values['dob']);
      $user->set('field_gender', $form_values['gender']);
      $user->set('field_course', $form_values['course']);
      $user->set('field_pincode', $form_values['pincode']);
      $user->set('field_city', $form_values['city']);
      $user->set('field_state', $form_values['state']);

      if (in_array('professor', $user->getRoles())) {
        $user->set('field_work', $form_values['work']);
        $user->set('field_subjects', $form_values['subjects']);
      }

      // Save user account.
      $result = $user->save();

      if($result) {
        echo '1';
      }
    }
    exit;
  }

  /**
   * Questions page handle.
   */
  public function populateQuestions() {
    return [
      '#markup' => '',
    ];
  }

  /**
   * Question list.
   */
  public function getQuestion() {
    global $base_url;
    $nid = $_GET['qid'];

    // Get all content related to questions.
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'question')
      ->condition('nid', $nid)
      ->condition('status', 1);
    $result = $query->execute();

    $question = \Drupal\node\Entity\Node::loadMultiple($result);
    $question = reset($question);

    $questions['nid'] = $nid;
    $questions['title'] = $question->get('title')->value;
    $questions['question'] = $question->get('field_question_body')->value;
    $questions['solution'] = $question->get('body')->value;
    $questions['video_solution'] = $question->get('field_video_solution')->value;
    $questions['question_review'] = $question->get('field_examiner_review_')->value;
    
    echo Json::encode($questions);
    exit;

    return [
      '#markup' => 'Done',
    ];
  }

  function reportError() {
    $get_parameters = $_GET;
    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());

    // Send mail to admin.
    $mailManager = \Drupal::service('plugin.manager.mail');
    // send mail to user.
    $module = 'site';
    $key = 'report_mail';
    $to = \Drupal::config('system.site')->get('mail');
    $params['subject'] = $get_parameters['qid'] . ' - ' . $get_parameters['title'];
    $params['message'] = 'Dear admin,<br><br><br>There is error reported on the question by user '. $user->get('name')->value .'. Please have a look.<br><br><br>Regards,<br> CAEXAM.';
    $langcode = \Drupal::currentUser()->getPreferredLangcode();
    $send = true;
    $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);

    return [
      '#markup' => 'Done',
    ];
  }

  function saveQuestions() {
    if(!empty($_POST['list'])) {
      // Save user question list.
      $uid = \Drupal::currentUser()->id();
      $connection = \Drupal::database();

      $result = $connection->merge('user_custom_questions')
      ->key(['uid' => $uid])
      ->fields([
       'list' => serialize($_POST['list']),
       'timestamp' => REQUEST_TIME,
      ])
      ->execute();
    }
    
    return [
      '#markup' => 'Done',
    ];
  }

  /**
   * Quote file handle.
   */
  public function uploadFile() {
    $ds = DIRECTORY_SEPARATOR;  //1 
    $storeFolder = '../../../../../sites/default/files';   //2
    if (!empty($_FILES)) {   
      $tempFile = $_FILES['file']['tmp_name'];          //3             
      $targetPath = dirname( __FILE__ ) . $ds. $storeFolder . $ds;  //4
      $targetFile =  $targetPath. $_FILES['file']['name'];  //5
      if(move_uploaded_file($tempFile,$targetFile)) { //6
        return [
          '#markup' => $targetFile,
        ];
      }
      else {
        return [
          '#markup' => '',
        ];
      }
    }
  }

  /**
   * Quote file handle.
   */
  public function populateDataTable() {
    $render_array = array();
    $exam_filter = $_POST['query']['exam'];
    $attempt_filter = $_POST['query']['attempt'];
    $subject_filter = $_POST['query']['subject'];
    $general_filter = $_POST['query']['generalSearch'];

    $query = \Drupal::entityQuery('node');
    $query->condition('type', 'data_table');
    $query->condition('status', 1);
    if(!empty($exam_filter)) {
      $query->condition('field_exam', $exam_filter, '=');
    }
    if(!empty($attempt_filter)) {
      $query->condition('field_attempt', $attempt_filter, '=');
    }
    if(!empty($subject_filter)) {
      $query->condition('field_subjects', $subject_filter, '=');
    }

    if(!empty($general_filter)) {
      $query->condition('field_user', db_like($general_filter) . '%', 'like');
    }
    $result = $query->execute();
    if(!empty($result)) {
      $render_array['meta'] = array(
        'page' => 1,
        'pages' => 1,
        'perpage' => -1,
        'total' => count($result),
        'sort' => 'asc',
        'field' => 'srno'
      );

      $count = 1;
      foreach ($result as $key => $nid) {
        $data = \Drupal\node\Entity\Node::loadMultiple(array($nid));
        $data = reset($data);
        $render_array['data'][] = array(
          'srno' => $count,
          'date' => $data->get('field_date')->value,
          'user' => $data->get('field_user')->value,
          'exam' => '<span class="m-badge m-badge--brand m-badge--wide">' . $data->get('field_exam')->value . '</span>',
          'attempt' => $data->get('field_attempt')->value,
          'subject' => '<span class="m--font-bold m--font-primary">' . $data->get('field_subjects')->value . '</span>',
          'notification' => $data->get('field_notification_update')->value,
          'notes' => $data->get('field_notes')->value,
        );

        $count++;
      }

      // Return JSON output.
      echo Json::encode($render_array);
      exit;
    }
  }

}

