<?php

/**
 * @file
 * Contains \Drupal\cfd_case_study\Form\CfdCaseStudyEditUploadAbstractCodeForm.
 */

namespace Drupal\cfd_case_study\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class CfdCaseStudyEditUploadAbstractCodeForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'cfd_case_study_edit_upload_abstract_code_form';
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $user = \Drupal::currentUser();
    $form['#attributes'] = ['enctype' => "multipart/form-data"];
    /* get current proposal */
    $proposal_id = (int) arg(3);
    $uid = $user->uid;
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
        drupal_goto('case-study-project/manage-proposal/edit-upload-file');
        return;
      }
    } //$proposal_q
    else {
      \Drupal::messenger()->addError(t('Invalid proposal selected. Please try again.'));
      drupal_goto('case-study-project/manage-proposal/edit-upload-file');
      return;
    }
    $query = \Drupal::database()->select('case_study_submitted_abstracts');
    $query->fields('case_study_submitted_abstracts');
    $query->condition('proposal_id', $proposal_data->id);
    $abstracts_q = $query->execute()->fetchObject();
    $form['project_title'] = [
      '#type' => 'item',
      '#markup' => $proposal_data->project_title,
      '#title' => t('Title of the Case Study Project'),
    ];
    $form['contributor_name'] = [
      '#type' => 'item',
      '#markup' => $proposal_data->contributor_name,
      '#title' => t('Contributor Name'),
    ];
    $existing_uploaded_A_file = default_value_for_uploaded_files("A", $proposal_data->id);
    if (!$existing_uploaded_A_file) {
      $existing_uploaded_A_file = new stdClass();
      $existing_uploaded_A_file->filename = "No file uploaded";
    } //!$existing_uploaded_A_file
    // @FIXME
    // // @FIXME
    // // This looks like another module's variable. You'll need to rewrite this call
    // // to ensure that it uses the correct configuration object.
    // $form['upload_case_study_abstract'] = array(
    //         '#type' => 'file',
    //         '#title' => t('Upload the Case study abstract'),
    //         //'#required' => TRUE,
    //         '#description' => t('<span style="color:red;">Current File :</span> ' . $existing_uploaded_A_file->filename . '<br />Separate filenames with underscore. No spaces or any special characters allowed in filename.') . '<br />' . t('<span style="color:red;">Allowed file extensions : ') . variable_get('resource_upload_extensions', '') . '</span>',
    //     );

    $existing_uploaded_S_file = default_value_for_uploaded_files("S", $proposal_data->id);
    if (!$existing_uploaded_S_file) {
      $existing_uploaded_S_file = new stdClass();
      $existing_uploaded_S_file->filename = "No file uploaded";
    } //!$existing_uploaded_S_file
    // @FIXME
    // // @FIXME
    // // This looks like another module's variable. You'll need to rewrite this call
    // // to ensure that it uses the correct configuration object.
    // $form['upload_case_study_developed_process'] = array(
    //         '#type' => 'file',
    //         '#title' => t('Upload the Case Directory'),
    //         //'#required' => TRUE,
    //         '#description' => t('<span style="color:red;">Current File :</span> ' . $existing_uploaded_S_file->filename . '<br />Separate filenames with underscore. No spaces or any special characters allowed in filename.') . '<br />' . t('<span style="color:red;">Allowed file extensions : ') . variable_get('case_study_project_files_extensions', '') . '</span>',
    //     );

    $form['prop_id'] = [
      '#type' => 'hidden',
      '#value' => $proposal_data->id,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('Submit'),
      '#submit' => [
        'cfd_case_study_edit_upload_abstract_code_form_submit'
        ],
    ];
    // @FIXME
    // l() expects a Url object, created from a route name or external URI.
    // $form['cancel'] = array(
    //         '#type' => 'item',
    //         '#markup' => l(t('Cancel'), 'case-study-project/manage-proposal/edit-upload-file'),
    //     );

    return $form;
  }

  public function validateForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {

    if (!($_FILES['files']['name']['upload_case_study_abstract'] || $_FILES['files']['name']['upload_case_study_developed_process'])) {
      \Drupal::messenger()->addError('No files uploaded');
      return;
    }
    if (isset($_FILES['files'])) {
      /* check for valid filename extensions */
      foreach ($_FILES['files']['name'] as $file_form_name => $file_name) {
        if ($file_name) {
          /* checking file type */
          if (strstr($file_form_name, 'upload_case_study_abstract')) {
            $file_type = 'A';
          }
          else {
            if (strstr($file_form_name, 'upload_case_study_developed_process')) {
              $file_type = 'S';
            }
            else {
              $file_type = 'U';
            }
          }

          $allowed_extensions_str = '';
          switch ($file_type) {
            case 'A':
              // @FIXME
              // // @FIXME
              // // This looks like another module's variable. You'll need to rewrite this call
              // // to ensure that it uses the correct configuration object.
              // $allowed_extensions_str = variable_get('resource_upload_extensions', '');

              break;
            case 'S':
              // @FIXME
              // // @FIXME
              // // This looks like another module's variable. You'll need to rewrite this call
              // // to ensure that it uses the correct configuration object.
              // $allowed_extensions_str = variable_get('case_study_project_files_extensions', '');

              break;
          } //$file_type
                /* checking file type */
          $allowed_extensions = explode(',', $allowed_extensions_str);
          $fnames = explode('.', strtolower($_FILES['files']['name'][$file_form_name]));
          $temp_extension = end($fnames);
          if (!in_array($temp_extension, $allowed_extensions)) {
            $form_state->setErrorByName($file_form_name, t('Only file with ' . $allowed_extensions_str . ' extensions can be uploaded.'));
          }

          if ($_FILES['files']['size'][$file_form_name] <= 0) {
            $form_state->setErrorByName($file_form_name, t('File size cannot be zero.'));
          }

          /* check if valid file name */
          if (!cfd_case_study_check_valid_filename($_FILES['files']['name'][$file_form_name])) {
            $form_state->setErrorByName($file_form_name, t('Invalid file name specified. Only alphabets and numbers are allowed as a valid filename.'));
          }

        } //$file_name
      } //$_FILES['files']['name'] as $file_form_name => $file_name
    }
    else {
      \Drupal::messenger()->addError('No files uploaded');
      return $form_state;
    }

  }

  public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $user = \Drupal::currentUser();
    $v = $form_state->getValues();
    $root_path = cfd_case_study_path();
    $query = \Drupal::database()->select('case_study_proposal');
    $query->fields('case_study_proposal');
    $query->condition('id', $v['prop_id']);
    $proposal_q = $query->execute();
    $proposal_data = $proposal_q->fetchObject();
    $proposal_id = $proposal_data->id;
    if (!$proposal_data) {
      drupal_goto('');
      return;
    } //!$proposal_data
    $proposal_id = $proposal_data->id;
    $proposal_directory = $proposal_data->directory_name;
    /* create proposal folder if not present */
    //$dest_path = $proposal_directory . '/';
    $dest_path_project_files = $proposal_directory . '/';
    $proposal_id = $proposal_data->id;
    foreach ($_FILES['files']['name'] as $file_form_name => $file_name) {

      if ($file_name) {
        /* uploading file */
        /* checking file type */
        if (strstr($file_form_name, 'upload_case_study_abstract')) {
          $file_type = 'A';
          $abs_file_name = $_FILES['files']['name'][$file_form_name];
        }
        else {
          $abs_file_name = "Not updated";
        }
        if (strstr($file_form_name, 'upload_case_study_developed_process')) {
          $file_type = 'S';
          $proj_file_name = $_FILES['files']['name'][$file_form_name];
        }
        else {
          $proj_file_name = "Not updated";
        }
        if (move_uploaded_file($_FILES['files']['tmp_name'][$file_form_name], $root_path . $dest_path_project_files . $_FILES['files']['name'][$file_form_name])) {
          $query_ab_f = "SELECT * FROM case_study_submitted_abstracts_file WHERE proposal_id = :proposal_id AND filetype =
				:filetype";
          $args_ab_f = [
            ":proposal_id" => $proposal_id,
            ":filetype" => $file_type,
          ];
          $query_ab_f_result = \Drupal::database()->query($query_ab_f, $args_ab_f)->fetchObject();
          unlink($root_path . $dest_path_project_files . $query_ab_f_result->filename);
          $query = "UPDATE {case_study_submitted_abstracts_file} SET filename = :filename, filepath=:filepath, filemime=:filemime, filesize=:filesize, timestamp=:timestamp WHERE proposal_id = :proposal_id AND filetype = :filetype";
          $args = [
            ":filename" => $_FILES['files']['name'][$file_form_name],
            ":filepath" => $file_path . $_FILES['files']['name'][$file_form_name],
            ":filemime" => mime_content_type($root_path . $dest_path_project_files . $_FILES['files']['name'][$file_form_name]),
            ":filesize" => $_FILES['files']['size'][$file_form_name],
            ":timestamp" => time(),
            ":proposal_id" => $proposal_id,
            ":filetype" => $file_type,
          ];
          \Drupal::database()->query($query, $args, $query);

          \Drupal::messenger()->addStatus($file_name . ' file updated successfully.');

        }
        else {
          \Drupal::messenger()->addError($file_name . ' file not updated successfully.');
        }
      }
    } //$_FILES['files']['name'] as $file_form_name => $file_name
    /* sending email */
    $email_to = $user->mail;
    // @FIXME
    // // @FIXME
    // // This looks like another module's variable. You'll need to rewrite this call
    // // to ensure that it uses the correct configuration object.
    // $from = variable_get('case_study_from_email', '');

    // @FIXME
    // // @FIXME
    // // This looks like another module's variable. You'll need to rewrite this call
    // // to ensure that it uses the correct configuration object.
    // $bcc = variable_get('case_study_emails', '');

    // @FIXME
    // // @FIXME
    // // This looks like another module's variable. You'll need to rewrite this call
    // // to ensure that it uses the correct configuration object.
    // $cc = variable_get('case_study_cc_emails', '');

    $params['abstract_edit_file_uploaded']['proposal_id'] = $proposal_id;
    $params['abstract_edit_file_uploaded']['user_id'] = $user->uid;
    $params['abstract_edit_file_uploaded']['abs_file'] = $abs_file_name;
    $params['abstract_edit_file_uploaded']['proj_file'] = $proj_file_name;
    $params['abstract_edit_file_uploaded']['headers'] = [
      'From' => $from,
      'MIME-Version' => '1.0',
      'Content-Type' => 'text/plain; charset=UTF-8; format=flowed; delsp=yes',
      'Content-Transfer-Encoding' => '8Bit',
      'X-Mailer' => 'Drupal',
      'Cc' => $cc,
      'Bcc' => $bcc,
    ];
    if (!drupal_mail('case_study', 'abstract_edit_file_uploaded', $email_to, language_default(), $params, $from, TRUE)) {
      \Drupal::messenger()->addError('Error sending email message.');
    }
    drupal_goto('case-study-project/abstract-code/edit-upload-files');
  }

}
?>
