<?php
/**
 * @file
 * Contains \Drupal\alias_updater\Form\SimpleConvertForm.
 */

namespace Drupal\alias_updater\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\taxonomy\Entity\Term;
use Drupal\node\Entity\Node;
use Drupal\Core\Path\AliasStorage;
use Drupal\redirect\Entity\Redirect;

class SimpleConvertForm extends FormBase {

  private $map = [
    'academicservices'=>'academic-services',
    'academicsuccess'=>'academic-success',
    'arp'=>'assessment-research-planning',
    'careerservices'=>'career-services',
    'esl'=>'english-as-a-second-language',
    'halloffame'=>'hall-of-fame',
    'righttime'=>'right-time',
    'wdce'=>'workforce-development-continuing-education',
    'nssc'=>'national-sustainable-structures-center',
    'pirc'=>'plastics-innovation-resource-center',
    'psn'=>'plastics-source-net',
    'rmce'=>'rotational-molding-center-of-excellence',
    'campuslife'=>'campus-life',
    'campushousing'=>'campus-housing',
    'disabilityservices'=>'disability-services',
    'its'=>'information-technology-services',
    'offcampus'=>'off-campus',
    'sexualmisconduct'=>'sexual-misconduct',
    'studentactivities'=>'student-activities',
    'studentpolicy'=>'student-policy',
    'campuslife'=>'campus-life',
    'counselingservices'=>'counseling-services',
    'glbtservices'=>'glbt-services',
    'newsandevents'=>'news-events',
    'welcomeweekend'=>'welcome-weekend',
    'collegehealthservices'=>'college-health-services',
    'studentaffairs'=>'student-affairs',
    'spendthenight'=>'spend-the-night',  
    'academicaffairs'=>'academic-affairs',
    'thepenncollegefund'=>'the-penn-college-fund',
    'corecourses'=>'core-courses',
    'sis'=>'student-information-system',
    'humanresources'=>'human-resources',
    'publicrelations'=>'public-relations',
    'facilitiesandevents'=>'facilities-events',
    'summercamps'=>'summer-camps',
    'collegehealth'=>'college-health',
    'degreesthatwork'=>'degrees-that-work',
    'emergencyresponse'=>'emergency-response',
    'otherprograms'=>'other-programs',
    'lejeunechef'=>'le-jeune-chef',
    'ljc'=>'le-jeune-chef',
    'militaryveterans' => 'military-veterans',
    'academicaffairs' => 'academic-affairs',
    'consumerinfo' =>'consumer-info',
    'nontrad' => 'nontraditional',
    
    //Cluster names...
    'accounting' =>'accounting-finance',
    'digitalmedia'=>'digital-media-marketing',
    'innovation' =>'innovation-entrepreneurism',
    
    'architecture'=>'architectural-technology-sustainable-design',
    'building'=>'building-construction',
    'civil'=>'civil-engineering-surveying',
    'construction'=>'construction-management',

    'bah'=>'applied-health-studies',
    'dental'=>'dental-hygiene',
    'healthit'=>'health-information-technology',
    'ota'=>'occupational-therapy-assistant',
    'pa'=>'physician-assistant',
    'pta'=>'physical-therapy-assistant',
    'imaging'=>'radiography-medical-imaging',
    'surgical'=>'surgical-technology',

    'applied'=>'applied-technology-studies',
    'automated'=>'automated-manufacturing-machining',
    'electronics'=>'electronics-computer-engineering-technology',
    'engineering'=>'engineering-industrial-design-technology',
    'it'=>'information-technology',
    'plastics'=>'plastics-polymer-engineering-technology',
    'welding'=>'welding-metal-fabrication',

    'brewing'=>'brewing-fermentation-science',
    'childhood'=>'early-childhood-education',
    'emergency'=>'emergency-management',
    'design'=>'graphic-design-art',
    'humanservices'=>'human-services',
    'general'=>'individualized-programs-study',

    'collision'=>'collision-repair-restoration',
    'diesel'=>'diesel-power-generation',
    'forest'=>'forestry',
    'heavyequipment'=>'heavy-equipment',
    'landscape'=>'landscape-horticulture',
  ];

  private $content_tables = [
    ['node__body', 'body_value'],
      ['node__field_css', 'field_css_value'],
      ['node__field_description', 'field_description_value'],
    ['node__field_intro', 'field_intro_value'],
    ['node__field_intro_html', 'field_intro_html_value'],
      ['node__field_js', 'field_js_value'],
      ['node__field_keywords', 'field_keywords_value'],
    ['node__field_thunderdome', 'field_thunderdome_value'],
    ['node_revision__body', 'body_value'],
      ['node_revision__field_css', 'field_css_value'],
      ['node_revision__field_description', 'field_description_value'],
    ['node_revision__field_intro', 'field_intro_value'],
    ['node_revision__field_intro_html', 'field_intro_html_value'],
      ['node_revision__field_js', 'field_js_value'],
      ['node_revision__field_keywords', 'field_keywords_value'],    
    ['node_revision__field_thunderdome', 'field_thunderdome_value'],
  ];

  private $content_paragraphs_dev = [
    ['background_image',['title','alt']],
    ['background_image_light',['title','alt']],
    ['caption',['value']],
    ['contact_email',['value']],
    ['contact_image',['title','alt']],
    ['contact_information',['value']],
    ['contact_location',['value']],
    ['contact_name',['value']],
    ['contact_phone',['value']],
    ['contact_staff_link',['uri','title']],
    ['contact_title',['value']],
    ['description',['value']],
    ['event_ad_title',['value']],
    ['event_text',['value']],
    ['event_title',['value']],
    ['feed_url',['value']],
    ['hero_content',['value']],
    ['image',['title','alt']],
    ['link',['value']],
    ['link_text',['value']],
    ['map_coordinates',['value']],
    ['primary_content',['value']],
    ['section_content',['value']],
    ['section_css_class',['value']],
    ['section_with_summary',['value','summary']],
    ['single_plain_content',['value']],
    ['slideshow',['alt','title']],
    ['title',['value']],
    ['video',['value']],
    ['wrapper_css_class',['value']],
  ];

  private $content_paragraphs_prod = [
    ['background_image',['title','alt']],
    ['background_image_light',['title','alt']],
    ['caption',['value']],
    ['event_ad_title',['value']],
    ['event_text',['value']],
    ['field_paragraph',['value']],
    ['hero_content',['value']],
    ['image',['title','alt']],
    ['link',['value']],
    ['link_text',['value']],
    ['section_css_class',['value']],
    ['wrapper_css_class',['value']],
  ];

  private $connection;

  /*
  * Constructor
  *
  * Include Acquia settings in case.
  */
  public function __construct(){
    if (file_exists('/var/www/site-php')) {
      require '/var/www/site-php/pct/pct-settings.inc';
    }

    $this->connection = \Drupal::database();
  }

  public function getFormId() {
    return 'alias_updater_simple_convert_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['description'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $this->t('Loop through all nodes, alter the URL alias, save the node.'),
    ];

    $form['test'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('This is not a test.'),
    ];

    $form['starting_nid'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Starting nid (Inclusive)'),
      '#size' => 5,
      '#maxlength' => 5,
      '#required' => TRUE,
      '#default_value' => 6901,
    ];
    
    $form['ending_nid'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Ending nid (Inclusive)'),
      '#size' => 5,
      '#maxlength' => 5,
      '#required' => TRUE,
      '#default_value' => 6901,
    ];

    $form['go'] = [
      '#type' => 'button',
      '#value' => t('Convert aliases'),
      '#ajax' => [
        'callback' => array($this, 'convertAliases'),
        'event' => 'click',
        'progress' => array(
          'type' => 'throbber',
          'message' => t('Processing nodes...'),
        ),
      ],
    ];

    $form['debug_info_heading'] = [
      '#type' => 'html_tag',
      '#tag' => 'h3',
      '#value' => $this->t('Debug Info'),
    ];

    $form['debug'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => 'placeholder',
      '#attributes' => [
        'id' => 'debug'
      ],
    ];

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
  
  }

  public function convertAliases(array &$form, FormStateInterface $form_state){

    $test = $form['test']['#value']; //1 if not test, 0 if just testing.
    $MAX_NODE_PROCESS = 200;
    $counter = 0;
    $log = '';
    $error = FALSE;

    //This fails even though it's in the constructor?
    if($this->connection === NULL){
      $this->connection = \Drupal::database();
    }

    $result = $this->connection->query("SELECT nid FROM {node} WHERE nid >= :nid_starting AND nid <= :nid_ending", [
      ':nid_starting' => $form['starting_nid']['#value'],
      ':nid_ending' => $form['ending_nid']['#value'],
    ]);
      
    //$result = $this->connection->query("SELECT nid FROM {node} WHERE nid >= 0 AND nid <= 180");

    foreach($result as $record){
      //Exit if more than we want.
      $counter++;
      if($counter > $MAX_NODE_PROCESS)
        break;

      $error = FALSE;

      //load node
      $node = Node::load($record->nid);
      $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'.$node->id());

      $path = explode('/', $alias);
      if(sizeof($path) > 1){
        $filename = array_pop($path);//remove last token
        $path = array_slice($path, 1);//remove first token
      }

      $log .= $record->nid.', ';

      //if no alias...
      if($path[0] === 'node'){
        $error = TRUE;
        $log .= 'FAIL, (no alias)';
      }

      if($node && !$error){
        $debug .= '<a target="_blank" href="'.$alias.'">$alias= '.$alias.'</a><br>';

        // Update the path using the map.
        for($i = 0; $i < sizeof($path); $i++){
          $path[$i] = strtolower($path[$i]); 
          if($this->map[$path[$i]]){
            $path[$i] = $this->map[$path[$i]];
          }
        }

        // Update the filename
        $new_filename = $this->getFilename($node->title->value);        

        // Put together new alias
        array_push($path, $new_filename);
        $new_alias = '/'.implode($path, '/');
        $debug .= '$new_alias= <strong>'.$new_alias.'</strong><br>';

        // Check if alias already exists
        $aliasStorage = \Drupal::service('path.alias_storage');
        $alias_exists = $aliasStorage->aliasExists($new_alias, 'en');

        if($alias_exists){
          $log .= 'FAIL, alias already exists ('.$new_alias.') ';
          $debug .= 'FAIL, alias already exists<br>';
          $error = TRUE;
        }

        if(!$error){
          if($test === 1){
            $aliasStorage->delete(['alias'=>$alias]);
            $status = $aliasStorage->save('/node/'.$node->id(), $new_alias, 'en');
          }

          if($status === FALSE){
            $log .= 'FAIL, failed removing old alias or saving new alias ('.$new_alias.')';
            $debug .= 'FAIL, failed removing old alias or saving new alias ('.$new_alias.')';
            $error = TRUE;
          }else{
            $log .= $alias.', '.$new_alias.', ';
            $debug .= 'Old alias removed and new one assigned<br>'; 
          }
        }
        
        if(!$error){
          if($test === 1){
            $status = $this->setRedirect($alias, $new_alias);
          }

          if($status == FALSE){
            $log .= ", FAIL failed creating redirect";
            $error = TRUE;
            $debug .= 'No redirect created<br>';
          }else{
            $log .= "Redirect created";
            $debug .= 'Redirect created<br>';
          }
        }

        if(!$error){
          if($test === 1){
            $replace_count = $this->findAndReplace($alias, $new_alias);
          }

          $debug .= 'Find and replace count= '.$replace_count.'<br>';
          $log .= ', '.$replace_count;
        }
        
        $debug .= 'Done on <a href="/node/'.$node->id().'/edit" target="_blank">('.$node->id().')</a>'.'<hr>';
      }

      $log .= "\n";
    }
    
    // Write log 
    file_save_data($log, "public://alias_updater/log".time().".txt", FILE_EXISTS_REPLACE);

    $response = new AjaxResponse();
    $response->addCommand(new HtmlCommand('#debug', $debug));
    return $response;
  }
  /**
   * Makes a redirect with the Redirect API.
   */
  private function setRedirect($from, $to):bool{
    if($from === $to){
      return TRUE;
    }

    // Use query to speed things up
    $r = $this->connection->query("SELECT rid FROM {redirect} WHERE hash LIKE :hash", [
      ':hash' => Redirect::generateHash(substr($from, 1), [], 'en')
    ]);

    if(sizeof($r->fetchAll()) === 0){
      $r = Redirect::create([
        'redirect_source' => substr($from, 1),
        'redirect_redirect' => 'internal:'.$to,
        'language' => 'en',
        'status_code' => '301',
      ])->save();
    }

    if($r === SAVED_NEW){
      return TRUE;
    }
    
    return FALSE;
  }

  /**
   * Creates a SEO friendly filename.
   */
  private function getFilename($str):String{
    $str = preg_replace('/<.*?>.*?<\/.*?>/i', '', $str);
    $str = preg_replace('/&(reg|rsquo);/i', '', $str);
    $str = preg_replace('/&amp;|&/i', '', $str);
    $str = preg_replace('/[\s\/]/i', '-', $str);
    $str = preg_replace('/[^a-zA-Z0-9\-"]/i', '', $str);
    while(strpos($str, '--') !== FALSE){
      $str = preg_replace('/\-\-/i', '-', $str);#remove chars
    }
    $str = preg_replace('/(\-$|^-)/i', '', $str);#remove trailing and preceding -
    $str = preg_replace('/-penn-college-magazine/i', '', $str);
    $str = strtolower($str);
    return $str;
  }

  /*
  * Find and replaces strings with format href="___" in content tables.
  */
  private function findAndReplace(string $find_value, string $replace_value):int{
    $rows_affected = 0;
    /*
    * Find and Repace
    */
    foreach($this->content_tables as $table){
      $update = \Drupal::database()->update($table[0]);
      $update->condition($table[1],  '%href="'.$find_value.'"%', 'LIKE');
      $update->expression($table[1], "REPLACE(".$table[1].", 'href=\"".$find_value."\"', 'href=\"".$replace_value."\"')");
      $rows_affected += $update->execute();
    }


    /*
    * Update paragraphs.
    */
    foreach($this->getParagraphTablesAndColumns(true) as $table){
      $update = \Drupal::database()->update(explode('.', $table)[0]);
      $update->condition(explode('.', $table)[1],  '%href="'.$find_value.'"%', 'LIKE');
      $update->expression(explode('.', $table)[1], "REPLACE(".explode('.', $table)[1].", 'href=\"".$find_value."\"', 'href=\"".$replace_value."\"')");
      $rows_affected += $update->execute();
    }

    return $rows_affected;
  }

  /*
  * Builds paragraph table names.
  * 
  * @return array Array of strings that represents paragraph
  * fields that contain human readable content. formatted
  * as table.column
  */
  private function getParagraphTablesAndColumns($revisions = true):array{
    $all_columns = [];
    $content_paragraphs = ($_ENV['AH_SITE_ENVIRONMENT'] == 'dev') ? $this->content_paragraphs_dev : $this->content_paragraphs_prod;
    foreach($content_paragraphs as $paragraph_table){
      $table_prefix = 'paragraph__field_';
      if($revisions)
        $table_revision_prefix = 'paragraph_revision__field_';

      $table = $table_prefix.$paragraph_table[0];
      if($revisions)
        $table_revision = $table_revision_prefix.$paragraph_table[0];

      foreach($paragraph_table[1] as $paragraph_col){
        $col_prefix = 'field_';
        $col = $col_prefix.$paragraph_table[0].'_'.$paragraph_col;

        $all_columns[] = $table.'.'.$col;
        if($revisions)
          $all_columns[] = $table_revision.'.'.$col;
      }

    }
    return $all_columns;
  }

}
