<?php

namespace Drupal\site\Controller;

use Drupal\Core\Controller\ControllerBase;
use \Drupal\node\Entity\Node;
use \Drupal\file\Entity\File;
use \Drupal\field_collection\Entity\FieldCollectionItem;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Component\Serialization\Json;

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

    $html_response = '<div class="m-portlet m-portlet--primary m-portlet--head-solid-bg m-portlet--head-sm m-portlet--rounded question" m-portlet="true" id="m_portlet_tools_1">
              <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                  <div class="m-portlet__head-title">
                    <span class="m-portlet__head-icon">
                      <i class="fa fa-question-circle"></i>
                    </span>
                    <h3 class="m-portlet__head-text">
                      '.$nid.'
                      <small>' . $questions['title'] . '</small>
                    </h3>
                  </div>
                </div>
                <div class="m-portlet__head-tools">
                  <ul class="m-portlet__nav">
                    <li class="m-portlet__nav-item">
                      <a href="#" m-portlet-tool="toggle" class="m-portlet__nav-link m-portlet__nav-link--icon"><i class="la la-angle-down"></i></a>
                    </li>
                    <li class="m-portlet__nav-item">
                      <a href="#" m-portlet-tool="fullscreen" class="m-portlet__nav-link m-portlet__nav-link--icon"><i class="la la-expand"></i></a>
                    </li>
                  </ul>
                </div>
              </div>
              <div class="m-portlet__body">
                <div class="m-scrollable" data-scrollbar-shown="true" data-scrollable="true" data-height="300">
                  ' . $questions['question'] . '
                </div>
              </div>
              <div class="m-portlet__foot question-share">
                <a href="whatsapp://send?text=The text to share!" data-action="' . $base_url . '/node/'. $nid .'" title="Share on Whatsapp"><span class="fab fa-whatsapp"></span></a>
                <a href="https://telegram.me/share/url?url=' . $base_url . '/node/'. $nid .'" title="Share on Telegram"><span class="fab fa-telegram"></span></a>
                <a href="http://www.facebook.com/sharer.php?u=' . $base_url . '/node/'. $nid .'" title="Share on Facebook"><span class="flaticon-facebook-logo-button"></span></a>
                <a href="' . $base_url . '/node/'. $nid .'" title="Copy the link" class="copy-url"><span class="fa fa-copy"></span></a>
                <button type="button" class="btn btn-danger float-right" data-toggle="modal" data-target="#m_modal_5">Report an error</button>
              </div>
            </div>

            <!-- Answer starts -->
            <div class="m-portlet m-portlet--collapsed m-portlet--head-sm answer" m-portlet="true" id="m_portlet_tools_7">
              <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                  <div class="m-portlet__head-title">
                    <span class="m-portlet__head-icon">
                      <i class="fa fa-lightbulb"></i>
                    </span>
                    <h3 class="m-portlet__head-text">
                      Answer
                    </h3>
                  </div>
                </div>
                <div class="m-portlet__head-tools">
                  <ul class="m-portlet__nav">
                    <li class="m-portlet__nav-item">
                      <a href="#" m-portlet-tool="toggle" class="m-portlet__nav-link m-portlet__nav-link--icon"><i class="la la-angle-down"></i></a>
                    </li>
                    <li class="m-portlet__nav-item">
                      <a href="#" m-portlet-tool="fullscreen" class="m-portlet__nav-link m-portlet__nav-link--icon"><i class="la la-expand"></i></a>
                    </li>
                  </ul>
                </div>
              </div>
              <div class="m-portlet__body">
                <div class="m-scrollable" data-scrollbar-shown="true" data-scrollable="true" data-height="600" style="overflow:hidden; height: 300px">
                  <div class="m-portlet">
                    <div class="m-portlet__body">
                      <ul class="nav nav-pills" role="tablist">
                        <li class="nav-item">
                          <a class="nav-link active" data-toggle="tab" href="#m_tabs_3_1">Solution</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" data-toggle="tab" href="#m_tabs_3_2">Video Solution</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" data-toggle="tab" href="#m_tabs_3_3">Examiner\'s Review</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" data-toggle="tab" href="#m_tabs_3_4">Notes</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" data-toggle="tab" href="#m_tabs_3_5">Comment/Share Your Doubts</a>
                        </li>
                      </ul>
                      <div class="tab-content">
                        <div class="tab-pane active" id="m_tabs_3_1" role="tabpanel">
                          ' . $questions['solution'] . '
                        </div>
                        <div class="tab-pane" id="m_tabs_3_2" role="tabpanel">
                          <div style="text-align: center;">
                            ' . $questions['video_solution'] . '
                          </div>
                        </div>
                        <div class="tab-pane" id="m_tabs_3_3" role="tabpanel">
                          <div class="cont">
                            ' . $questions['question_review'] . '
                          </div>
                        </div>
                        <div class="tab-pane" id="m_tabs_3_4" role="tabpanel">
                          <!--Begin::Portlet-->
                        <div class="m-portlet m-portlet--full-height ">
                          <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                              <div class="m-portlet__head-title">
                                <h3 class="m-portlet__head-text">
                                  Notes
                                </h3>
                              </div>
                            </div>
                          </div>
                          <div class="m-portlet__body">
                            <div class="tab-content">
                              <div class="tab-pane active" id="m_widget2_tab1_content">

                                <!--Begin::Timeline 3 -->
                                <div class="m-timeline-3">
                                  <div class="m-timeline-3__items">
                                    <div class="m-timeline-3__item m-timeline-3__item--info">
                                      <span class="m-timeline-3__item-time">09-02-2019</span>
                                      <div class="m-timeline-3__item-desc">
                                        <span class="m-timeline-3__item-text">
                                          Lorem ipsum dolor sit amit,consectetur eiusmdd tempor
                                        </span><br>
                                        <span class="m-timeline-3__item-user-name">
                                          <a href="#" class="m-link m-link--metal m-timeline-3__item-link">
                                            By Bob
                                          </a>
                                        </span>
                                      </div>
                                    </div>
                                    <div class="m-timeline-3__item m-timeline-3__item--warning">
                                      <span class="m-timeline-3__item-time">09-02-2019</span>
                                      <div class="m-timeline-3__item-desc">
                                        <span class="m-timeline-3__item-text">
                                          Lorem ipsum dolor sit amit
                                        </span><br>
                                        <span class="m-timeline-3__item-user-name">
                                          <a href="#" class="m-link m-link--metal m-timeline-3__item-link">
                                            By Sean
                                          </a>
                                        </span>
                                      </div>
                                    </div>
                                    <div class="m-timeline-3__item m-timeline-3__item--brand">
                                      <span class="m-timeline-3__item-time">09-02-2019</span>
                                      <div class="m-timeline-3__item-desc">
                                        <span class="m-timeline-3__item-text">
                                          Lorem ipsum dolor sit amit eiusmdd tempor
                                        </span><br>
                                        <span class="m-timeline-3__item-user-name">
                                          <a href="#" class="m-link m-link--metal m-timeline-3__item-link">
                                            By James
                                          </a>
                                        </span>
                                      </div>
                                    </div>
                                    <div class="m-timeline-3__item m-timeline-3__item--success">
                                      <span class="m-timeline-3__item-time">09-02-2019</span>
                                      <div class="m-timeline-3__item-desc">
                                        <span class="m-timeline-3__item-text">
                                          Lorem ipsum dolor
                                        </span><br>
                                        <span class="m-timeline-3__item-user-name">
                                          <a href="#" class="m-link m-link--metal m-timeline-3__item-link">
                                            By James
                                          </a>
                                        </span>
                                      </div>
                                    </div>
                                    <div class="m-timeline-3__item m-timeline-3__item--danger">
                                      <span class="m-timeline-3__item-time">09-02-2019</span>
                                      <div class="m-timeline-3__item-desc">
                                        <span class="m-timeline-3__item-text">
                                          Lorem ipsum dolor sit amit,consectetur eiusmdd
                                        </span><br>
                                        <span class="m-timeline-3__item-user-name">
                                          <a href="#" class="m-link m-link--metal m-timeline-3__item-link">
                                            By Derrick
                                          </a>
                                        </span>
                                      </div>
                                    </div>
                                    <div class="m-timeline-3__item m-timeline-3__item--info">
                                      <span class="m-timeline-3__item-time">09-02-2019</span>
                                      <div class="m-timeline-3__item-desc">
                                        <span class="m-timeline-3__item-text">
                                          Lorem ipsum dolor sit amit,consectetur
                                        </span><br>
                                        <span class="m-timeline-3__item-user-name">
                                          <a href="#" class="m-link m-link--metal m-timeline-3__item-link">
                                            By Iman
                                          </a>
                                        </span>
                                      </div>
                                    </div>
                                    <div class="m-timeline-3__item m-timeline-3__item--brand">
                                      <span class="m-timeline-3__item-time">09-02-2019</span>
                                      <div class="m-timeline-3__item-desc">
                                        <span class="m-timeline-3__item-text">
                                          Lorem ipsum dolor sit consectetur eiusmdd tempor
                                        </span><br>
                                        <span class="m-timeline-3__item-user-name">
                                          <a href="#" class="m-link m-link--metal m-timeline-3__item-link">
                                            By Aziko
                                          </a>
                                        </span>
                                      </div>
                                    </div>
                                  </div>
                                </div>

                                <!--End::Timeline 3 -->
                                </div>
                                <div class="tab-pane" id="m_widget2_tab2_content">

                                  <!--Begin::Timeline 3 -->
                                  <div class="m-timeline-3">
                                    <div class="m-timeline-3__items">
                                      <div class="m-timeline-3__item m-timeline-3__item--info">
                                        <span class="m-timeline-3__item-time m--font-focus">09:00</span>
                                        <div class="m-timeline-3__item-desc">
                                          <span class="m-timeline-3__item-text">
                                            Contrary to popular belief, Lorem Ipsum is not simply random text.
                                          </span><br>
                                          <span class="m-timeline-3__item-user-name">
                                            <a href="#" class="m-link m-link--metal m-timeline-3__item-link">
                                              By Bob
                                            </a>
                                          </span>
                                        </div>
                                      </div>
                                      <div class="m-timeline-3__item m-timeline-3__item--warning">
                                        <span class="m-timeline-3__item-time m--font-warning">10:00</span>
                                        <div class="m-timeline-3__item-desc">
                                          <span class="m-timeline-3__item-text">
                                            There are many variations of passages of Lorem Ipsum available.
                                          </span><br>
                                          <span class="m-timeline-3__item-user-name">
                                            <a href="#" class="m-link m-link--metal m-timeline-3__item-link">
                                              By Sean
                                            </a>
                                          </span>
                                        </div>
                                      </div>
                                      <div class="m-timeline-3__item m-timeline-3__item--brand">
                                        <span class="m-timeline-3__item-time m--font-primary">11:00</span>
                                        <div class="m-timeline-3__item-desc">
                                          <span class="m-timeline-3__item-text">
                                            Contrary to popular belief, Lorem Ipsum is not simply random text.
                                          </span><br>
                                          <span class="m-timeline-3__item-user-name">
                                            <a href="#" class="m-link m-link--metal m-timeline-3__item-link">
                                              By James
                                            </a>
                                          </span>
                                        </div>
                                      </div>
                                      <div class="m-timeline-3__item m-timeline-3__item--success">
                                        <span class="m-timeline-3__item-time m--font-success">12:00</span>
                                        <div class="m-timeline-3__item-desc">
                                          <span class="m-timeline-3__item-text">
                                            The standard chunk of Lorem Ipsum used since the 1500s is reproduced.
                                          </span><br>
                                          <span class="m-timeline-3__item-user-name">
                                            <a href="#" class="m-link m-link--metal m-timeline-3__item-link">
                                              By James
                                            </a>
                                          </span>
                                        </div>
                                      </div>
                                      <div class="m-timeline-3__item m-timeline-3__item--danger">
                                        <span class="m-timeline-3__item-time m--font-warning">14:00</span>
                                        <div class="m-timeline-3__item-desc">
                                          <span class="m-timeline-3__item-text">
                                            Latin words, combined with a handful of model sentence structures.
                                          </span><br>
                                          <span class="m-timeline-3__item-user-name">
                                            <a href="#" class="m-link m-link--metal m-timeline-3__item-link">
                                              By Derrick
                                            </a>
                                          </span>
                                        </div>
                                      </div>
                                      <div class="m-timeline-3__item m-timeline-3__item--info">
                                        <span class="m-timeline-3__item-time m--font-info">15:00</span>
                                        <div class="m-timeline-3__item-desc">
                                          <span class="m-timeline-3__item-text">
                                            Contrary to popular belief, Lorem Ipsum is not simply random text.
                                          </span><br>
                                          <span class="m-timeline-3__item-user-name">
                                            <a href="#" class="m-link m-link--metal m-timeline-3__item-link">
                                              By Iman
                                            </a>
                                          </span>
                                        </div>
                                      </div>
                                      <div class="m-timeline-3__item m-timeline-3__item--brand">
                                        <span class="m-timeline-3__item-time m--font-danger">17:00</span>
                                        <div class="m-timeline-3__item-desc">
                                          <span class="m-timeline-3__item-text">
                                            Lorem Ipsum is therefore always free from repetition, injected humour.
                                          </span><br>
                                          <span class="m-timeline-3__item-user-name">
                                            <a href="#" class="m-link m-link--metal m-timeline-3__item-link">
                                              By Aziko
                                            </a>
                                          </span>
                                        </div>
                                      </div>
                                    </div>
                                  </div>

                                  <!--End::Timeline 3 -->
                                </div>
                              </div>
                            </div>
                          </div>
                          <form>
                            <div class="form-group">
                              <label for="message-text" class="form-control-label">Notes</label>
                              <textarea class="form-control" id="review" rows="6"></textarea>
                            </div>
                            <div class="form-group">
                              <button type="button" class="btn btn-primary">Submit</button>
                            </div>
                          </form>
                        </div>
                        <div class="tab-pane" id="m_tabs_3_5" role="tabpanel">
                          <div class="m-portlet m-portlet--full-height ">
                            <div class="m-portlet__head">
                              <div class="m-portlet__head-caption">
                                <div class="m-portlet__head-title">
                                  <h3 class="m-portlet__head-text">
                                    Comments
                                  </h3>
                                </div>
                              </div>
                            </div>
                            <div class="m-portlet__body">
                              <div class="m-widget3">
                                <div class="m-widget3__item">
                                  <div class="m-widget3__header">
                                    <div class="m-widget3__user-img">
                                      <img class="m-widget3__img" src="assets/app/media/img/users/user4.jpg" alt="">
                                    </div>
                                    <div class="m-widget3__info">
                                      <span class="m-widget3__username">
                                        Melania Trump
                                      </span><br>
                                      <span class="m-widget3__time">
                                        2 day ago
                                      </span>
                                    </div>
                                  </div>
                                  <div class="m-widget3__body">
                                    <p class="m-widget3__text">
                                      Lorem ipsum dolor sit amet,consectetuer edipiscing elit,sed diam nonummy nibh euismod tinciduntut laoreet doloremagna aliquam erat volutpat.
                                    </p>
                                  </div>
                                </div>
                                <div class="m-widget3__item">
                                  <div class="m-widget3__header">
                                    <div class="m-widget3__user-img">
                                      <img class="m-widget3__img" src="assets/app/media/img/users/user4.jpg" alt="">
                                    </div>
                                    <div class="m-widget3__info">
                                      <span class="m-widget3__username">
                                        Lebron King James
                                      </span><br>
                                      <span class="m-widget3__time">
                                        1 day ago
                                      </span>
                                    </div>
                                  </div>
                                  <div class="m-widget3__body">
                                    <p class="m-widget3__text">
                                      Lorem ipsum dolor sit amet,consectetuer edipiscing elit,sed diam nonummy nibh euismod tinciduntut laoreet doloremagna aliquam erat volutpat.Ut wisi enim ad minim veniam,quis nostrud exerci tation ullamcorper.
                                    </p>
                                  </div>
                                </div>
                                <div class="m-widget3__item">
                                  <div class="m-widget3__header">
                                    <div class="m-widget3__user-img">
                                      <img class="m-widget3__img" src="assets/app/media/img/users/user4.jpg" alt="">
                                    </div>
                                    <div class="m-widget3__info">
                                      <span class="m-widget3__username">
                                        Deb Gibson
                                      </span><br>
                                      <span class="m-widget3__time">
                                        3 weeks ago
                                      </span>
                                    </div>
                                  </div>
                                  <div class="m-widget3__body">
                                    <p class="m-widget3__text">
                                      Lorem ipsum dolor sit amet,consectetuer edipiscing elit,sed diam nonummy nibh euismod tinciduntut laoreet doloremagna aliquam erat volutpat.
                                    </p>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>

                          <!--end:: Widgets/Support Tickets -->
                          <form>
                            <div class="form-group">
                              <label for="message-text" class="form-control-label">Comment/Share Your Doubts</label>
                              <textarea class="form-control" id="review" rows="6"></textarea>
                            </div>
                            <div class="form-group">
                              <button type="button" class="btn btn-primary">Submit Doubts</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Answer ends -->';
    // Return HTML output.
    echo $html_response;
    exit;
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

