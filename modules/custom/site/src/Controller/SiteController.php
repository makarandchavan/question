<?php

namespace Drupal\site\Controller;

use Drupal\Core\Controller\ControllerBase;
use \Drupal\node\Entity\Node;
use \Drupal\file\Entity\File;
use \Drupal\field_collection\Entity\FieldCollectionItem;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
  public function freelancers() {
    return [
      '#theme' => 'freelancers',
      '#site' => $this->t('site'),
    ];
  }

  /**
   * Quote form handle.
   */
  public function handleContact() {
    // Insert a record.
    // Create file object from remote URL.
    global $base_url;

    //Gather all main form data.
    foreach($_POST['contactFormData'] as $mainform) {
      if($mainform['name'] == 'name') {
        $name = $mainform['value'];
      }
      elseif($mainform['name'] == 'email') {
        $email = $mainform['value'];
      }
      elseif($mainform['name'] == 'entreprise') {
        $enterprise = $mainform['value'];
      }
      elseif($mainform['name'] == 'message') {
        $cmessage = $mainform['value'];
      }
    }

    // Send mails.
    $mailManager = \Drupal::service('plugin.manager.mail');
    // send mail to user.
    $module = 'site';
    $key = 'contact_mail';
    $to = $email;
    $params['subject'] = t('Thank you');
    $params['message'] = t('Dear user,<br><br><br>Thank you for the mail. We have received your request. Will contact you shortly.<br><br><br>Regards,<br>Poptranslation.');
    $langcode = \Drupal::currentUser()->getPreferredLangcode();
    $send = true;
    $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);

    // send mail to admin.
    $module = 'site';
    $key = 'contact_mail_admin';
    $to = 'isarkar77@gmail.com';
    $params['subject'] = t('New Contact received');
    $params['message'] = t('Dear admin,<br><br><br>Please find below details<br><br>Name: @usrname<br>Email: @usrmail<br>Entreprise: @usrent<br>Message: @usrinfo<br><br><br>Regards,<br>Poptranslation.', array('@usrname' => $name, '@usrmail' => $email, '@usrent' => $enterprise, '@usrinfo' => $cmessage));
    $attachment = array(
      'filepath' => $filepath, // or $uri
    );
    $params['attachment'] = $attachment;
    $langcode = \Drupal::currentUser()->getPreferredLangcode();
    $send = true;
    $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
    if ($result['result'] !== true) {
      drupal_set_message(t('There was a problem sending your message and it was not sent.'), 'error');
      return [
        '#markup' => '',
      ];
    }
    else {
      return [
        '#markup' => $this->t('Thank You. Your message has been sent successfully.'),
      ];
    }
  }
}

