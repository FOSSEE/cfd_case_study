<?php

/**
 * @file
 * Contains \Drupal\cfd_case_study\Form\CfdCaseStudyProposalStatusForm.
 */

namespace Drupal\cfd_case_study\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class CfdCaseStudyProposalStatusForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'cfd_case_study_proposal_status_form';
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $user = \Drupal::currentUser();
    /* get current proposal */
    $proposal_id = (int) arg(3);
    $query = \Drupal::database()->select('case_study_proposal');
    $query->fields('case_study_proposal');
    $query->condition('id', $proposal_id);
    $proposal_q = $query->execute();
    $query_abstract = \Drupal::database()->select('case_study_submitted_abstracts_file');
    $query_abstract->fields('case_study_submitted_abstracts_file');
    $query_abstract->condition('proposal_id', $proposal_id);
    $query_abstract->condition('filetype', 'A');
    $query_abstract_pdf = $query_abstract->execute()->fetchObject();
    if ($proposal_q) {
      if ($proposal_data = $proposal_q->fetchObject()) {
        /* everything ok */
      } //$proposal_data = $proposal_q->fetchObject()
      else {
        \Drupal::messenger()->addError(t('Invalid proposal selected. Please try again.'));
        drupal_goto('case-study-project/manage-proposal');
        return;
      }
    } //$proposal_q
    else {
      \Drupal::messenger()->addError(t('Invalid proposal selected. Please try again.'));
      drupal_goto('case-study-project/manage-proposal');
      return;
    }
    if ($proposal_data->faculty_name == '') {
      $faculty_name = 'NA';
    }
    else {
      $faculty_name = $proposal_data->faculty_name;
    }
    if ($proposal_data->faculty_department == '') {
      $faculty_department = 'NA';
    }
    else {
      $faculty_department = $proposal_data->faculty_department;
    }
    if ($proposal_data->faculty_email == '') {
      $faculty_email = 'NA';
    }
    else {
      $faculty_email = $proposal_data->faculty_email;
    }
    $query = \Drupal::database()->select('case_study_software_version');
    $query->fields('case_study_software_version');
    $query->condition('id', $proposal_data->version_id);
    $version_data = $query->execute()->fetchObject();
    if (!$version_data) {
      $version = 'NA';
    }
    else {
      $version = $version_data->case_study_version;
    }
    $query = \Drupal::database()->select('case_study_simulation_type');
    $query->fields('case_study_simulation_type');
    $query->condition('id', $proposal_data->simulation_type_id);
    $simulation_type_data = $query->execute()->fetchObject();
    if (!$simulation_type_data) {
      $simulation_type = 'NA';
    }
    else {
      $simulation_type = $simulation_type_data->simulation_type;
    }
    // @FIXME
    // l() expects a Url object, created from a route name or external URI.
    // $form['contributor_name'] = array(
    //         '#type' => 'item',
    //         '#markup' => l($proposal_data->name_title . ' ' . $proposal_data->contributor_name, 'user/' . $proposal_data->uid),
    //         '#title' => t('Student name'),
    //     );

    $form['student_email_id'] = [
      '#title' => t('Student Email'),
      '#type' => 'item',
      '#markup' => \Drupal::entityTypeManager()->getStorage('user')->load($proposal_data->uid)->mail,
      '#title' => t('Email'),
    ];
    $form['university'] = [
      '#type' => 'item',
      '#markup' => $proposal_data->university,
      '#title' => t('University/Institute'),
    ];
    $form['how_did_you_know_about_project'] = [
      '#type' => 'item',
      '#markup' => $proposal_data->how_did_you_know_about_project,
      '#title' => t('How did you know about the project'),
    ];
    $form['faculty_name'] = [
      '#type' => 'item',
      '#markup' => $faculty_name,
      '#title' => t('Name of the faculty'),
    ];
    $form['faculty_department'] = [
      '#type' => 'item',
      '#markup' => $faculty_department,
      '#title' => t('Department of the faculty'),
    ];
    $form['faculty_email'] = [
      '#type' => 'item',
      '#markup' => $faculty_email,
      '#title' => t('Email of the faculty'),
    ];
    $form['country'] = [
      '#type' => 'item',
      '#markup' => $proposal_data->country,
      '#title' => t('Country'),
    ];
    $form['all_state'] = [
      '#type' => 'item',
      '#markup' => $proposal_data->state,
      '#title' => t('State'),
    ];
    $form['city'] = [
      '#type' => 'item',
      '#markup' => $proposal_data->city,
      '#title' => t('City'),
    ];
    $form['pincode'] = [
      '#type' => 'item',
      '#markup' => $proposal_data->pincode,
      '#title' => t('Pincode/Postal code'),
    ];
    $form['project_title'] = [
      '#type' => 'item',
      '#markup' => $proposal_data->project_title,
      '#title' => t('Title of the Case Study Project'),
    ];
    $form['version'] = [
      '#type' => 'item',
      '#markup' => $version,
      '#title' => t('Version used'),
    ];
    $form['simulation_type'] = [
      '#type' => 'item',
      '#markup' => $simulation_type,
      '#title' => t('Simulation Type'),
    ];
    $form['solver_used'] = [
      '#type' => 'item',
      '#markup' => $proposal_data->solver_used,
      '#title' => t('Solver used'),
    ];
    /************************** reference link filter *******************/
    $url = '~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i';
    $reference = preg_replace($url, '<a href="$0" target="_blank" title="$0">$0</a>', $proposal_data->reference);
    /******************************/
    /*$form['reference'] = array(
    '#type' => 'item',
    '#markup' => $reference,
    '#title' => t('References')
    );*/
    if (($query_abstract_pdf->filename != "") && ($query_abstract_pdf->filename != 'NULL')) {
      $str = substr($query_abstract_pdf->filename, strrpos($query_abstract_pdf->filename, '/'));
      $resource_file = ltrim($str, '/');

      // @FIXME
      // l() expects a Url object, created from a route name or external URI.
      // $form['abstract_file_path'] = array(
      //             '#type' => 'item',
      //             '#title' => t('Abstract file '),
      //             '#markup' => l($resource_file, 'case-study-project/download/project-file/' . $proposal_id) . "",
      //         );

    } //$proposal_data->user_defined_compound_filepath != ""
    else {
      $form['abstract_file_path'] = [
        '#type' => 'item',
        '#title' => t('Abstract file '),
        '#markup' => "Not uploaded<br><br>",
      ];
    }
    $proposal_status = '';
    switch ($proposal_data->approval_status) {
      case 0:
        $proposal_status = t('Pending');
        break;
      case 1:
        $proposal_status = t('Approved');
        break;
      case 2:
        $proposal_status = t('Dis-approved');
        break;
      case 3:
        $proposal_status = t('Completed');
        break;
      case 5:
        $approval_status = t('On Hold');
        break;
      default:
        $proposal_status = t('Unkown');
        break;
    }
    $form['proposal_status'] = [
      '#type' => 'item',
      '#markup' => $proposal_status,
      '#title' => t('Proposal Status'),
    ];
    if ($proposal_data->approval_status == 0) {
      // @FIXME
// l() expects a Url object, created from a route name or external URI.
// $form['approve'] = array(
//             '#type' => 'item',
//             '#markup' => l('Click here', 'case-study-project/manage-proposal/approve/' . $proposal_id),
//             '#title' => t('Approve'),
//         );

    } //$proposal_data->approval_status == 0
    if ($proposal_data->approval_status == 1) {
      $form['completed'] = [
        '#type' => 'checkbox',
        '#title' => t('Completed'),
        '#description' => t('Check if user has provided all the required files and pdfs.'),
      ];
    } //$proposal_data->approval_status == 1
    if ($proposal_data->approval_status == 2) {
      $form['message'] = [
        '#type' => 'item',
        '#markup' => $proposal_data->message,
        '#title' => t('Reason for disapproval'),
      ];
    } //$proposal_data->approval_status == 2
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('Submit'),
    ];
    // @FIXME
    // l() expects a Url object, created from a route name or external URI.
    // $form['cancel'] = array(
    //         '#type' => 'markup',
    //         '#markup' => l(t('Cancel'), 'case-study-project/manage-proposal/all'),
    //     );

    return $form;
  }

  public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $user = \Drupal::currentUser();
    /* get current proposal */
    $proposal_id = (int) arg(3);
    //$proposal_q = db_query("SELECT * FROM {case_study_proposal} WHERE id = %d", $proposal_id);
    $query = \Drupal::database()->select('case_study_proposal');
    $query->fields('case_study_proposal');
    $query->condition('id', $proposal_id);
    $proposal_q = $query->execute();
    if ($proposal_q) {
      if ($proposal_data = $proposal_q->fetchObject()) {
        /* everything ok */
      } //$proposal_data = $proposal_q->fetchObject()
      else {
        \Drupal::messenger()->addError(t('Invalid proposal selected. Please try again.'));
        drupal_goto('case-study-project/manage-proposal');
        return;
      }
    } //$proposal_q
    else {
      \Drupal::messenger()->addError(t('Invalid proposal selected. Please try again.'));
      drupal_goto('case-study-project/manage-proposal');
      return;
    }
    /* set the book status to completed */
    if ($form_state->getValue(['completed']) == 1) {
      $up_query = "UPDATE case_study_proposal SET approval_status = :approval_status , actual_completion_date = :expected_completion_date WHERE id = :proposal_id";
      $args = [
        ":approval_status" => '3',
        ":proposal_id" => $proposal_id,
        ":expected_completion_date" => time(),
      ];
      $result = \Drupal::database()->query($up_query, $args);
      CreateReadmeFileCaseStudyProject($proposal_id);
      if (!$result) {
        \Drupal::messenger()->addError('Error in update status');
        return;
      } //!$result
        /* sending email */
      $user_data = \Drupal::entityTypeManager()->getStorage('user')->load($proposal_data->uid);
      $email_to = $user_data->mail;
      // @FIXME
      // // @FIXME
      // // This looks like another module's variable. You'll need to rewrite this call
      // // to ensure that it uses the correct configuration object.
      // $from = variable_get('case_study_from_email', '');

      // @FIXME
      // // @FIXME
      // // This looks like another module's variable. You'll need to rewrite this call
      // // to ensure that it uses the correct configuration object.
      // $bcc = $user->mail . ', ' . variable_get('case_study_emails', '');

      // @FIXME
      // // @FIXME
      // // This looks like another module's variable. You'll need to rewrite this call
      // // to ensure that it uses the correct configuration object.
      // $cc = variable_get('case_study_cc_emails', '');

      $params['case_study_proposal_completed']['proposal_id'] = $proposal_id;
      $params['case_study_proposal_completed']['user_id'] = $proposal_data->uid;
      $params['case_study_proposal_completed']['headers'] = [
        'From' => $from,
        'MIME-Version' => '1.0',
        'Content-Type' => 'text/plain; charset=UTF-8; format=flowed; delsp=yes',
        'Content-Transfer-Encoding' => '8Bit',
        'X-Mailer' => 'Drupal',
        'Cc' => $cc,
        'Bcc' => $bcc,
      ];
      if (!drupal_mail('case_study', 'case_study_proposal_completed', $email_to, language_default(), $params, $from, TRUE)) {
        \Drupal::messenger()->addError('Error sending email message.');
      }

      \Drupal::messenger()->addStatus('Congratulations! CFD Case Study proposal has been marked as completed. User has been notified of the completion.');
    }
    drupal_goto('case-study-project/manage-proposal');
    return;

  }

}
?>
