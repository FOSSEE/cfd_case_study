<?php

/**
 * @file
 * Contains \Drupal\cfd_case_study\Form\CfdCaseStudyProposalForm.
 */

namespace Drupal\cfd_case_study\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Database\Database;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Mail\MailManager;
use Drupal\user\Entity\User;


class CfdCaseStudyProposalForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'cfd_case_study_proposal_form';
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state, $no_js_use = NULL) {
    $user = \Drupal::currentUser();
    /************************ start approve book details ************************/
    if ($user->id() == 0) {
      $msg = \Drupal::messenger()->addError(t('It is mandatory to @login_link on this website to access the case study proposal form. If you are a new user, please create a new account first.', [
        '@login_link' => Link::fromTextAndUrl(t('login'), Url::fromRoute('user.page'))->toString(),
      ]));
      $response = new RedirectResponse(Url::fromRoute('user.login', [], [
        'query' => \Drupal::destination()->getAsArray(),
      ])->toString());
      
      return $response;
      return $msg;
    } //$user->uid == 0
    $query = \Drupal::database()->select('case_study_proposal');
    $query->fields('case_study_proposal');
    $query->condition('uid', $user->uid);
    $query->orderBy('id', 'DESC');
    $query->range(0, 1);
    $proposal_q = $query->execute();
    $proposal_data = $proposal_q->fetchObject();
    if ($proposal_data) {
      if ($proposal_data->approval_status == 0 || $proposal_data->approval_status == 1) {
        \Drupal::messenger()->addStatus(t('We have already received your proposal.'));
        $response = new RedirectResponse(Url::fromRoute('<front>')->toString());
  
  // Send the redirect response
// ->send();
        // drupal_goto('');
      
        return $response;
      } //$proposal_data->approval_status == 0 || $proposal_data->approval_status == 1
    } //$proposal_data
    $form['#attributes'] = [
      'enctype' => "multipart/form-data"
      ];

    $form['name_title'] = [
      '#type' => 'select',
      '#title' => t('Title'),
      '#options' => [
        'Dr' => 'Dr',
        'Prof' => 'Prof',
        'Mr' => 'Mr',
        'Ms' => 'Ms',
      ],
      '#required' => TRUE,
    ];
    $form['contributor_name'] = [
      '#type' => 'textfield',
      '#title' => t('Name of the contributor'),
      '#size' => 250,
      '#attributes' => [
        'placeholder' => t('Enter your full name.....')
        ],
      '#maxlength' => 250,
      '#required' => TRUE,
    ];
    $form['contributor_email_id'] = [
      '#type' => 'textfield',
      '#title' => t('Email'),
      '#size' => 30,
      '#value' => $user ? $user->getEmail() : '',
      '#disabled' => TRUE,
    ];
    $form['contributor_contact_no'] = [
      '#type' => 'textfield',
      '#title' => t('Contact No.'),
      '#size' => 10,
      '#attributes' => [
        'placeholder' => t('Enter your contact number')
        ],
      '#maxlength' => 250,
    ];
    $form['university'] = [
      '#type' => 'textfield',
      '#title' => t('University'),
      '#size' => 80,
      '#maxlength' => 200,
      '#required' => TRUE,
      '#attributes' => [
        'placeholder' => 'Insert full name of your university.... '
        ],
    ];
    $form['institute'] = [
      '#type' => 'textfield',
      '#title' => t('Institute'),
      '#size' => 80,
      '#maxlength' => 200,
      '#required' => TRUE,
      '#attributes' => [
        'placeholder' => 'Insert full name of your institute.... '
        ],
    ];
    $form['how_did_you_know_about_project'] = [
      '#type' => 'select',
      '#title' => t('How did you come to know about the Case Study Project?'),
      '#options' => [
        'Poster' => 'Poster',
        'Website' => 'Website',
        'Email' => 'Email',
        'Others' => 'Others',
      ],
      '#required' => TRUE,
    ];
    $form['others_how_did_you_know_about_project'] = [
      '#type' => 'textfield',
      '#title' => t('If ‘Other’, please specify'),
      '#maxlength' => 50,
      '#description' => t('<span style="color:red">Maximum character limit is 50</span>'),
      '#states' => [
        'visible' => [
          ':input[name="how_did_you_know_about_project"]' => [
            'value' => 'Others'
            ]
          ]
        ],
    ];
    $form['faculty_name'] = [
      '#type' => 'textfield',
      '#title' => t('Name of the Faculty Member of your Institution, if any, who helped you with this Case Study Project'),
      '#size' => 50,
      '#maxlength' => 50,
      '#validated' => TRUE,
      '#description' => t('<span style="color:red">Maximum character limit is 50</span>'),
    ];
    $form['faculty_department'] = [
      '#type' => 'textfield',
      '#title' => t('Department of the Faculty Member of your Institution, if any, who helped you with this Case Study Project'),
      '#size' => 50,
      '#maxlength' => 50,
      '#validated' => TRUE,
      '#description' => t('<span style="color:red">Maximum character limit is 50</span>'),
    ];
    $form['faculty_email'] = [
      '#type' => 'textfield',
      '#title' => t('Email id of the Faculty Member of your Institution, if any, who helped you with this Case Study Project'),
      '#size' => 255,
      '#maxlength' => 255,
      '#validated' => TRUE,
      '#description' => t('<span style="color:red">Maximum character limit is 255</span>'),
    ];
    $form['country'] = [
      '#type' => 'select',
      '#title' => t('Country'),
      '#options' => [
        'India' => 'India',
        'Others' => 'Others',
      ],
      '#required' => TRUE,
      '#tree' => TRUE,
      '#validated' => TRUE,
    ];
    $form['other_country'] = [
      '#type' => 'textfield',
      '#title' => t('Other than India'),
      '#size' => 100,
      '#attributes' => [
        'placeholder' => t('Enter your country name')
        ],
      '#states' => [
        'visible' => [
          ':input[name="country"]' => [
            'value' => 'Others'
            ]
          ]
        ],
    ];
    $form['other_state'] = [
      '#type' => 'textfield',
      '#title' => t('State other than India'),
      '#size' => 100,
      '#attributes' => [
        'placeholder' => t('Enter your state/region name')
        ],
      '#states' => [
        'visible' => [
          ':input[name="country"]' => [
            'value' => 'Others'
            ]
          ]
        ],
    ];
    $form['other_city'] = [
      '#type' => 'textfield',
      '#title' => t('City other than India'),
      '#size' => 100,
      '#attributes' => [
        'placeholder' => t('Enter your city name')
        ],
      '#states' => [
        'visible' => [
          ':input[name="country"]' => [
            'value' => 'Others'
            ]
          ]
        ],
    ];
    $form['all_state'] = [
      '#type' => 'select',
      '#title' => t('State'),
      '#options' => _df_list_of_states(),
      '#validated' => TRUE,
      '#states' => [
        'visible' => [
          ':input[name="country"]' => [
            'value' => 'India'
            ]
          ]
        ],
    ];
    $form['city'] = [
      '#type' => 'select',
      '#title' => t('City'),
      '#options' => _df_list_of_cities(),
      '#states' => [
        'visible' => [
          ':input[name="country"]' => [
            'value' => 'India'
            ]
          ]
        ],
    ];
    $form['pincode'] = [
      '#type' => 'textfield',
      '#title' => t('Pincode'),
      '#size' => 6,
    ];
    /***************************************************************************/
    $form['hr'] = [
      '#type' => 'item',
      '#markup' => '<hr>',
    ];

    $list_case_study = _cs_list_of_case_studies();
    if (!empty($list_case_study)) {
      $form['cfd_project_title_check'] = [
        '#type' => 'radios',
        '#title' => t('Is the proposed CFD Case study from the list of available CFD Case studies?'),
        '#options' => [
          '1' => 'Yes',
          '0' => 'No',
        ],
        '#required' => TRUE,
        '#validated' => TRUE,
      ];
      $form['cfd_case_study_name_dropdown'] = [
        '#type' => 'select',
        '#title' => t('Select the name of available cfd'),
        '#required' => TRUE,
        '#options' => _cs_list_of_case_studies(),
        '#validated' => TRUE,
        '#states' => [
          'visible' => [
            ':input[name="cfd_project_title_check"]' => [
              'value' => '1'
              ]
            ]
          ],
      ];
      $form['project_title'] = [
        '#type' => 'textfield',
        '#title' => t('Project Title'),
        '#size' => 250,
        '#description' => t('Maximum character limit is 250'),
        '#required' => TRUE,
        '#validated' => TRUE,
        '#states' => [
          'visible' => [
            ':input[name="cfd_project_title_check"]' => [
              'value' => '0'
              ]
            ]
          ],
      ];
    }
    else {
      $form['project_title'] = [
        '#type' => 'textfield',
        '#title' => t('Project Title'),
        '#size' => 250,
        '#description' => t('Maximum character limit is 250'),
        '#required' => TRUE,
        '#validated' => TRUE,
      ];
    }
    $version_options = _cs_list_of_versions();
    $form['version'] = [
      '#type' => 'select',
      '#title' => t('Version used'),
      '#options' => $version_options,
      '#required' => TRUE,
    ];
    $simulation_type_options = _cs_list_of_simulation_types();
    $form['simulation_type'] = [
      '#type' => 'select',
      '#title' => t('Simulation Type used'),
      '#options' =>_cs_list_of_simulation_types(),
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '::ajax_solver_used_callback'
        ],
    ];
    $simulation_id = $form_state->getValue('simulation_type') ?: key($simulation_type_options);
    // $simulation_id = !$form_state->getValue(['simulation_type']) ? $form_state->getValue([
    //   'simulation_type'
    //   ]) : key($simulation_type_options);
    if ($simulation_id < 19) {
      $form['solver_used'] = [
        '#type' => 'select',
        '#title' => t('Select the Solver to be used'),
        '#options' => _cs_list_of_solvers($simulation_id),
        '#prefix' => '<div id="ajax-solver-replace">',
        '#suffix' => '</div>',
        '#states' => [
          'invisible' => [
            ':input[name="simulation_type"]' => [
              'value' => 19
              ]
            ]
          ],
        '#required' => TRUE,
      ];
    }
    // var_dump(_cs_list_of_solvers());die;
    //else if ($simulation_id == 19){
    $form['solver_used_text'] = [
      '#type' => 'textfield',
      '#title' => t('Enter the Solver to be used'),
      '#size' => 100,
      '#description' => t('Maximum character limit is 50'),
      //'#required' => TRUE,
		'#prefix' => '<div id="ajax-solver-text-replace">',
      '#suffix' => '</div>',
      '#states' => [
        'visible' => [
          ':input[name="simulation_type"]' => [
            'value' => 19
            ]
          ]
        ],
    ];
    //}
    $form['abstract_file'] = [
      '#type' => 'fieldset',
      '#title' => t('Submit an Abstract'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];
    
    $form['abstract_file']['abstract_file_path'] = [
      '#type' => 'file',
      '#size' => 48,
      '#description' => t('<span style="color:red;">Upload filenames with allowed extensions only. No spaces or any special characters allowed in filename.</span>') . '<br />' . t('<span style="color:red;">Allowed file extensions: ') . \Drupal::config('cfd_case_study.settings')->get('resource_upload_extensions') . '</span>',
  ];
  // var_dump(\Drupal::config('cfd_case_study.settings')->get('default_allowed_extensions'));die;
    $form['date_of_proposal'] = [
      '#type' => 'date',
      '#title' => t('Date of Proposal'),
      '#default_value' => date("Y-m-d H:i:s"),
      '#date_format' => 'd M Y',
      '#disabled' => TRUE,
      '#date_label_position' => '',
    ];
    $form['expected_date_of_completion'] = [
      '#type' => 'date',
      '#title' => t('Expected Date of Completion'),
      '#date_label_position' => '',
      '#description' => '',
      '#default_value' => '',
      '#date_format' => 'd-M-Y',
      //'#date_increment' => 0,
      //'#minDate' => '+0',
		'#date_year_range' => '0 : +1',
      '#required' => TRUE,
    ];
    $form['term_condition'] = [
      '#type' => 'checkboxes',
      '#title' => t('Terms And Conditions'),
      '#options' => [
        'status' => t('<a href="/case-study-project/term-and-conditions" target="_blank">I agree to the Terms and Conditions</a>')
        ],
      '#required' => TRUE,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('Submit'),
    ];
    return $form;
  }

  function ajax_solver_used_callback(array &$form, FormStateInterface $form_state) {
    return  $form['solver_used'];
  }
  // function ajax_solver_used_callback(array &$form, FormStateInterface $form_state) {
  //     $simulation_type_options = _cs_simulation_type_options(); // Assuming this is a function returning simulation options
  //     $simulation_id = $form_state->getValue('simulation_type', key($simulation_type_options));
      
  //     $response = new AjaxResponse();
  
  //     if ($simulation_id < 19) {
  //         // Update the 'solver_used' field options dynamically.
  //         $form['solver_used']['#options'] = _cs_list_of_solvers($simulation_id);
  //         $form['solver_used']['#required'] = TRUE;
  //         $form['solver_used']['#validated'] = TRUE;
  
  //         // Replace the 'solver_used' section of the form.
  //         $response->addCommand(new ReplaceCommand('#ajax-solver-replace', $form['solver_used']));
  //         // Clear any existing text in the solver text section.
  //         $response->addCommand(new HtmlCommand('#ajax-solver-text-replace', ''));
  //     } else {
  //         // Clear the 'solver_used' section.
  //         $response->addCommand(new HtmlCommand('#ajax-solver-replace', ''));
  
  //         // Make 'solver_used_text' required and validated.
  //         $form['solver_used_text']['#required'] = TRUE;
  //         $form['solver_used_text']['#validated'] = TRUE;
  
  //         // Replace the 'solver_used_text' section of the form.
  //         $response->addCommand(new ReplaceCommand('#ajax-solver-text-replace', $form['solver_used_text']));
  //     }
  
  //     return $response;
  // }
  

  public function validateForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    //var_dump($form_state['values']['solver_used']);die;
    if ($form_state->getValue([
      'cfd_project_title_check'
      ]) == 1) {
      $project_title = $form_state->getValue(['cfd_case_study_name_dropdown']);
    }
    else {

      $project_title = $form_state->getValue(['project_title']);
    }
    if ($form_state->getValue(['term_condition']) == '1') {
      $form_state->setErrorByName('term_condition', t('Please check the terms and conditions'));
      // $form_state['values']['country'] = $form_state['values']['other_country'];
    } //$form_state['values']['term_condition'] == '1'
    if ($form_state->getValue([
      'country'
      ]) == 'Others') {
      if ($form_state->getValue(['other_country']) == '') {
        $form_state->setErrorByName('other_country', t('Enter country name'));
        // $form_state['values']['country'] = $form_state['values']['other_country'];
      } //$form_state['values']['other_country'] == ''
      else {
        $form_state->setValue(['country'], $form_state->getValue([
          'other_country'
          ]));
      }
      if ($form_state->getValue(['other_state']) == '') {
        $form_state->setErrorByName('other_state', t('Enter state name'));
        // $form_state['values']['country'] = $form_state['values']['other_country'];
      } //$form_state['values']['other_state'] == ''
      else {
        $form_state->setValue(['all_state'], $form_state->getValue([
          'other_state'
          ]));
      }
      if ($form_state->getValue(['other_city']) == '') {
        $form_state->setErrorByName('other_city', t('Enter city name'));
        // $form_state['values']['country'] = $form_state['values']['other_country'];
      } //$form_state['values']['other_city'] == ''
      else {
        $form_state->setValue(['city'], $form_state->getValue(['other_city']));
      }
    } //$form_state['values']['country'] == 'Others'
    else {
      if ($form_state->getValue(['country']) == '') {
        $form_state->setErrorByName('country', t('Select country name'));
        // $form_state['values']['country'] = $form_state['values']['other_country'];
      } //$form_state['values']['country'] == ''
      if ($form_state->getValue([
        'all_state'
        ]) == '') {
        $form_state->setErrorByName('all_state', t('Select state name'));
        // $form_state['values']['country'] = $form_state['values']['other_country'];
      } //$form_state['values']['all_state'] == ''
      if ($form_state->getValue([
        'city'
        ]) == '') {
        $form_state->setErrorByName('city', t('Select city name'));
        // $form_state['values']['country'] = $form_state['values']['other_country'];
      } //$form_state['values']['city'] == ''
    }
    //Validation for project title
    $form_state->setValue(['project_title'], trim($form_state->getValue([
      'project_title'
      ])));
    if ($form_state->getValue(['project_title']) != '') {
      if (strlen($form_state->getValue(['project_title'])) > 250) {
        $form_state->setErrorByName('project_title', t('Maximum charater limit is 250 charaters only, please check the length of the project title'));
      } //strlen($form_state['values']['project_title']) > 250
      else {
        if (strlen($form_state->getValue(['project_title'])) < 10) {
          $form_state->setErrorByName('project_title', t('Minimum charater limit is 10 charaters, please check the length of the project title'));
        }
      } //strlen($form_state['values']['project_title']) < 10
    } //$form_state['values']['project_title'] != ''
	/*else
	{
		form_set_error('project_title', t('Project title shoud not be empty'));
	}*/

    if ($form_state->getValue(['simulation_type']) < 19) {
      if ($form_state->getValue(['solver_used']) == '0') {
        $form_state->setErrorByName('solver_used', t('Please select an option'));
      }
    }
    else {
      if ($form_state->getValue(['simulation_type']) == 19) {
        if ($form_state->getValue(['solver_used_text']) != '') {
          if (strlen($form_state->getValue(['solver_used_text'])) > 100) {
            $form_state->setErrorByName('solver_used_text', t('Maximum charater limit is 100 charaters only, please check the length of the solver used'));
          } //strlen($form_state['values']['project_title']) > 250
          else {
            if (strlen($form_state->getValue(['solver_used_text'])) < 7) {
              $form_state->setErrorByName('solver_used_text', t('Minimum charater limit is 7 charaters, please check the length of the solver used'));
            }
          } //strlen($form_state['values']['project_title']) < 10
        }
        else {
          $form_state->setErrorByName('solver_used_text', t('Solver used cannot be empty'));
        }
      }
    }
    if (strtotime(date($form_state->getValue(['expected_date_of_completion']))) < time()) {
      $form_state->setErrorByName('expected_date_of_completion', t('Completion date should not be earlier than proposal date'));
    }

    if ($form_state->getValue(['how_did_you_know_about_project']) == 'Others') {
      if ($form_state->getValue(['others_how_did_you_know_about_project']) == '') {
        $form_state->setErrorByName('others_how_did_you_know_about_project', t('Please enter how did you know about the project'));
        // $form_state['values']['country'] = $form_state['values']['other_country'];
      } //$form_state['values']['other_country'] == ''
      else {
        $form_state->setValue(['how_did_you_know_about_project'], $form_state->getValue([
          'others_how_did_you_know_about_project'
          ]));
      }
    }
    /*if ($form_state['values']['faculty_name'] != '' || $form_state['values']['faculty_name'] != "NULL") {
		if($form_state['values']['faculty_email'] == '' || $form_state['values']['faculty_email'] == "NULL")
		{
			form_set_error('faculty_email', t('Please enter the email id of your faculty'));
		}
		if($form_state['values']['faculty_department'] == '' || $form_state['values']['faculty_department'] == 'NULL'){
			form_set_error('faculty_department', t('Please enter the Department of your faculty'));
		}
	}*/

    if (isset($_FILES['files'])) {
      /* check if atleast one source or result file is uploaded */
      if (!($_FILES['files']['name']['abstract_file_path'])) {
        $form_state->setErrorByName('abstract_file_path', t('Please upload the abstract file'));
      }
      /* check for valid filename extensions */
      foreach ($_FILES['files']['name'] as $file_form_name => $file_name) {
        if ($file_name) {
          /* checking file type */
          // @FIXME
// // @FIXME
// // This looks like another module's variable. You'll need to rewrite this call
// // to ensure that it uses the correct configuration object.
$allowed_extensions_str = \Drupal::config('cfd_case_study.settings')->get('resource_upload_extensions');

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
          if (!textbook_companion_check_valid_filename($_FILES['files']['name'][$file_form_name])) {
            $form_state->setErrorByName($file_form_name, t('Invalid file name specified. Only alphabets and numbers are allowed as a valid filename.'));
          }
        } //$file_name
      } //$_FILES['files']['name'] as $file_form_name => $file_name
    }
    return $form_state;
  }

  public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $user = \Drupal::currentUser();
    $root_path = cfd_case_study_path();
    if (!$user->id()) {
      \Drupal::messenger()->addError('It is mandatory to login on this website to access the proposal form');
      return;
    }
    if ($form_state->getValue(['cfd_project_title_check']) == 1) {
      $project_title = $form_state->getValue(['cfd_case_study_name_dropdown']);
    }
    else {

      $project_title = $form_state->getValue(['project_title']);
    }
    if ($form_state->getValue(['how_did_you_know_about_project']) == 'Others') {
      $how_did_you_know_about_project = $form_state->getValue(['others_how_did_you_know_about_project']);
    }
    else {
      $how_did_you_know_about_project = $form_state->getValue(['how_did_you_know_about_project']);
    }
    /* inserting the user proposal */
    $v = $form_state->getValues();
    $project_title = trim($project_title);
    $proposar_name = $v['name_title'] . ' ' . $v['contributor_name'];
    $university = $v['university'];
    $directory_name = _df_dir_name($project_title, $proposar_name);
    $simulation_id = $v['simulation_type'];
    if ($simulation_id < 19) {
      $solver = $v['solver_used'];
    }
    else {
      $solver = $v['solver_used_text'];
    }
    $result = "INSERT INTO {case_study_proposal} 
    (
    uid, 
    approver_uid,
    name_title, 
    contributor_name,
    contact_no,
    university,
    institute,
    how_did_you_know_about_project,
    faculty_name,
    faculty_department,
    faculty_email,
    city, 
    pincode, 
    state, 
    country,
    project_title, 
    version_id,
    simulation_type_id,
    solver_used,
    directory_name,
    approval_status,
    is_completed, 
    dissapproval_reason,
    creation_date, 
    expected_date_of_completion,
    approval_date
    ) VALUES
    (
    :uid, 
    :approver_uid, 
    :name_title, 
    :contributor_name, 
    :contact_no,
    :university, 
    :institute,
    :how_did_you_know_about_project,
    :faculty_name,
    :faculty_department,
    :faculty_email,
    :city, 
    :pincode, 
    :state,  
    :country,
    :project_title, 
    :version_id,
    :simulation_type_id,
    :solver_used,
    :directory_name,
    :approval_status,
    :is_completed, 
    :dissapproval_reason,
    :creation_date, 
    :expected_date_of_completion,
    :approval_date
    )";
    $args = [
      ":uid" => $user->id(),
      ":approver_uid" => 0,
      ":name_title" => $v['name_title'],
      ":contributor_name" => _df_sentence_case(trim($v['contributor_name'])),
      ":contact_no" => $v['contributor_contact_no'],
      ":university" => $v['university'],
      ":institute" => _df_sentence_case($v['institute']),
      ":how_did_you_know_about_project" => trim($how_did_you_know_about_project),
      ":faculty_name" => $v['faculty_name'],
      ":faculty_department" => $v['faculty_department'],
      ":faculty_email" => $v['faculty_email'],
      ":city" => $v['city'],
      ":pincode" => $v['pincode'],
      ":state" => $v['all_state'],
      ":country" => $v['country'],
      ":project_title" => $project_title,
      ":version_id" => $v['version'],
      ":simulation_type_id" => $simulation_id,
      ":solver_used" => $solver,
      ":directory_name" => $directory_name,
      ":approval_status" => 0,
      ":is_completed" => 0,
      ":dissapproval_reason" => "NULL",
      ":creation_date" => time(),
      ":expected_date_of_completion" => strtotime(date($v['expected_date_of_completion'])),
      ":approval_date" => 0,
    ];
    $result1 = \Drupal::database()->query($result, $args);
    //var_dump($result1->id);die;
    $query_pro = \Drupal::database()->select('case_study_proposal');
    $query_pro->fields('case_study_proposal');
    //	$query_pro->condition('id', $proposal_data->id);
    $abstracts_pro = $query_pro->execute()->fetchObject();
    //	$proposal_id = $abstracts_pro->id;
    $dest_path = $directory_name . '/';
    $dest_path1 = $root_path . $dest_path;
    if (!is_dir($root_path . $dest_path)) {
      mkdir($root_path . $dest_path);
    }
    /* uploading files */
    foreach ($_FILES['files']['name'] as $file_form_name => $file_name) {
      if ($file_name) {
        /* checking file type */
        //$file_type = 'S';
        if (file_exists($root_path . $dest_path . $_FILES['files']['name'][$file_form_name])) {
          \Drupal::messenger()->addError(t("Error uploading file. File !filename already exists.", [
            '!filename' => $_FILES['files']['name'][$file_form_name]
            ]));
          //unlink($root_path . $dest_path . $_FILES['files']['name'][$file_form_name]);
        } //file_exists($root_path . $dest_path . $_FILES['files']['name'][$file_form_name])
			/* uploading file */
        if (move_uploaded_file($_FILES['files']['tmp_name'][$file_form_name], $root_path . $dest_path . $_FILES['files']['name'][$file_form_name])) {
          $query_pro = \Drupal::database()->select('case_study_proposal');
          $query_pro->fields('case_study_proposal');
          //$query_pro->condition('id', $proposal_data->id);
          $abstracts_pro = $query_pro->execute()->fetchObject();
          //$proposal_id = $abstracts_pro->id;
          //var_dump($proposal_id);die;
          //$proposal_id = $result1->id;
          $query_abstracts = "INSERT INTO {case_study_submitted_abstracts} (
	proposal_id,
	approver_uid,
	abstract_approval_status,
	abstract_upload_date,
	abstract_approval_date,
	is_submitted) VALUES (:proposal_id, :approver_uid, :abstract_approval_status,:abstract_upload_date, :abstract_approval_date, :is_submitted)";
          $args = [
            ":proposal_id" => $result1,
            ":approver_uid" => 0,
            ":abstract_approval_status" => 0,
            ":abstract_upload_date" => time(),
            ":abstract_approval_date" => 0,
            ":is_submitted" => 0,
          ];
          $submitted_abstract_id = \Drupal::database()->query($query_abstracts, $args, $query_abstracts);
          $query = "INSERT INTO {case_study_submitted_abstracts_file} (submitted_abstract_id, proposal_id, uid, approvar_uid, filename, filepath, filemime, filesize, filetype, timestamp)
          VALUES (:submitted_abstract_id, :proposal_id, :uid, :approvar_uid, :filename, :filepath, :filemime, :filesize, :filetype, :timestamp)";
          $args = [
            ":submitted_abstract_id" => $submitted_abstract_id,
            ":proposal_id" => $result1,
            ":uid" => $user->uid,
            ":approvar_uid" => 0,
            ":filename" => $_FILES['files']['name'][$file_form_name],
            ":filepath" => $_FILES['files']['name'][$file_form_name],
            ":filemime" => mime_content_type($root_path . $dest_path . $_FILES['files']['name'][$file_form_name]),
            ":filesize" => $_FILES['files']['size'][$file_form_name],
            ":filetype" => 'A',
            ":timestamp" => time(),
          ];

          /*$query = "UPDATE {case_study_proposal} SET abstract_file_path = :abstract_file_path WHERE id = :id";
				$args = array(
					":abstract_file_path" => $dest_path . $_FILES['files']['name'][$file_form_name],
					":id" => $result1
				);*/

          $updateresult = \Drupal::database()->query($query, $args);
          //var_dump($args);die;

          \Drupal::messenger()->addStatus($file_name . ' uploaded successfully.');
        } //move_uploaded_file($_FILES['files']['tmp_name'][$file_form_name], $root_path . $dest_path . $_FILES['files']['name'][$file_form_name])
        else {
          \Drupal::messenger()->addError('Error uploading file : ' . $dest_path . '/' . $file_name);
        }
      } //$file_name
    } //$_FILES['files']['name'] as $file_form_name => $file_name
    if (!$result1) {
      \Drupal::messenger()->addError(t('Error receiving your proposal. Please try again.'));
      return;
    } //!$proposal_id
	/* sending email */
    $email_to = $user->mail;
    // @FIXME
    // // @FIXME
    // // This looks like another module's variable. You'll need to rewrite this call
    // // to ensure that it uses the correct configuration object.
    // $form = variable_get('case_study_from_email', '');

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

    // $params['case_study_proposal_received']['result1'] = $result1;
    // $params['case_study_proposal_received']['user_id'] = $user->uid;
    // $params['case_study_proposal_received']['headers'] = [
    //   'From' => $form,
    //   'MIME-Version' => '1.0',
    //   'Content-Type' => 'text/plain; charset=UTF-8; format=flowed; delsp=yes',
    //   'Content-Transfer-Encoding' => '8Bit',
    //   'X-Mailer' => 'Drupal',
    //   'Cc' => $cc,
    //   'Bcc' => $bcc,
    // ];
    // if (!\Drupal::service('plugin.manager.mail')->mail('case_study', 'case_study_proposal_received', $email_to, 'en', $params, $form, TRUE)) {
    //   \Drupal::messenger()->addError('Error sending email message.');
    // }
    $response = new RedirectResponse(Url::fromRoute('<front>')->toString());
    // Send the redirect response
    $response->send();
    \Drupal::messenger()->addStatus(t('We have received your case study proposal. We will get back to you soon.'));
    // drupal_goto('');
  }

}
?>
