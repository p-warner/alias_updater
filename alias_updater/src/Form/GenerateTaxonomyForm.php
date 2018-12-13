<?php
/**
 * @file
 * Contains \Drupal\alias_updater\Form\GenerateTaxonomyForm.
 */

namespace Drupal\alias_updater\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\taxonomy\Entity\Term;

class GenerateTaxonomyForm extends FormBase {

  /*
  * Constructor
  *
  * Include Acquia settings in case.
  */
  public function __construct(){
    if (file_exists('/var/www/site-php')) {
      require '/var/www/site-php/pct/pct-settings.inc';
    }
  }

  public function getFormId() {
    return 'alias_updater_generate_taxonomy_form';
  }

  public function saveTour(array &$form, FormStateInterface $form_state){

    $response = new AjaxResponse();

    $css = ['border' => '1px solid green'];

    $message = $this->t('Tour saved.');

    //$response->addCommand(new CssCommand('.tour-save-message', $css));
    $response->addCommand(new HtmlCommand('.tour-save-message', $message));

    return $response;
  }

    public function buildForm(array $form, FormStateInterface $form_state) {

    /* keep form element around for a little... */
    $form['vocabulary_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Path Vocabulary name'),
      '#default_value' => '',
      '#placeholder' => 'ex. Category',
      '#attributes' => [
        'class' => ['lfloat'],
      ],
      //'#suffix' => '<p class="tour-save-message"></p>'
    ];
    

    $form['description'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $this->t('Generate taxonomy based upon current URL paths.'),
    ];

    $form['test'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('This is not a test.'),
    ];

    $form['go'] = [
      '#type' => 'button',
      '#value' => t('Generate terms'),
      '#ajax' => [
        'callback' => array($this, 'generateTerms'),
        'event' => 'click',
        'progress' => array(
          'type' => 'throbber',
          'message' => t('Generating terms...'),
        ),
      ],
    ];

    /*$form['response_area'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $this->t('Response area.'),
      '#attributes' => [
        'id' => 'response_area'
      ],
    ];*/

    $form['vocabulary_info_heading'] = [
      '#type' => 'html_tag',
      '#tag' => 'h3',
      '#value' => $this->t('Vocabulary information'),
    ];

    $form['vocabulary_info'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => 'placeholder',
      '#attributes' => [
        'id' => 'vocabulary_info'
      ],
    ];

    $form['vocabulary_term_info_heading'] = [
      '#type' => 'html_tag',
      '#tag' => 'h3',
      '#value' => $this->t('Vocabulary terms'),
    ];

    $form['vocabulary_term_info'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => 'placeholder',
      '#attributes' => [
        'id' => 'vocabulary_term_info'
      ],
    ];

    $form['debug'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => 'placeholder',
      '#attributes' => [
        'id' => 'debug'
      ],
    ];

    $form['vocabulary_term_list'] = [
      '#type' => 'html_tag',
      '#tag' => 'ul',
      '#value' => '<li>terms placeholder</li>',
      '#attributes' => [
        'id' => 'vocabulary_term_list'
      ],
    ];

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    #There's no submit button... lol.
  }

  /**
   * Load term by name.
   */
  private function getTidByName($name = NULL, $vocabulary = NULL) {
    $properties = [];
    if (!empty($name)) {
      $properties['name'] = $name;
    }
    if (!empty($vocabulary)) {
      $properties['vid'] = $vocabulary;
    }
    $terms = \Drupal::entityManager()->getStorage('taxonomy_term')->loadByProperties($properties);
    $term = reset($terms);
    return !empty($term) ? $term->id() : 0;
  }

  public function generateTerms(array &$form, FormStateInterface $form_state){
    #
    # Create Vocabulary
    #
    //$vid = strtolower(str_replace(' ', '_', $form['vocabulary_name']['#value']));
    //$name = $form['vocabulary_name']['#value'];
    $test = $form['test']['#value'];//1 if not test, 0 if just testing.
    $vocab_id = 'category2';
    $name = "Category2";
    $vocabularies = \Drupal\taxonomy\Entity\Vocabulary::loadMultiple();
    $vocabCreated = FALSE;

    if (!isset($vocabularies[$vocab_id])) {
      $vocabulary = \Drupal\taxonomy\Entity\Vocabulary::create(array(
            'vid' => $vocab_id,
            //'machine_name' => $vid,
            'description' => '',
            'name' => $name,
      ));
      $vocabulary->save();
      $vocabCreated = TRUE;
    }
    else {
      // Vocabulary Already exist
      $query = \Drupal::entityQuery('taxonomy_term');
      $query->condition('vid', $vocab_id);
      $tids = $query->execute();
    }

    $vids = \Drupal::entityQuery('node')->condition('status', 1)->condition('type', 'page')->execute();

    $number_of_nodes = count($vids);
    $term_list = [];
    $human_term_list = '';
    $MAX_NODE_PROCESS = 100;
    $counter = 0;

    foreach($vids as $vid) {
      //Exit if more than we want.
      $counter++;
      if($counter > $MAX_NODE_PROCESS)
        break;

      //load node, the query gives revision IDs, not node IDs.
      //$node = \Drupal\node\Entity\Node::load($vid);
      $node = \Drupal::entityTypeManager()->getStorage('node')->loadRevision($vid);

      $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'.$node->id());

      $path = explode('/', $alias);//tokenize path on /
      if(sizeof($path) > 1){
        array_pop($path);//remove last token
        $path = array_slice($path, 1);//remove first token
      }

      $last_tid = NULL;
      for($i=0; $i < sizeof($path); $i++){

        $tid = $this->getTidByName($path[$i], $vocab_id);

        if($tid == 0){

          $term = Term::create([
            'name' => $path[$i],
            'vid' => $vocab_id,
          ]);
          $term->save();
        
          if($last_tid){
            $term->parent = ['target_id' => $last_tid];
            $term->save();
          }

          if(!is_int($term))
            $last_tid = $term->id();

        }else{
          $last_tid = $tid;
        }
      }

      if($node){
        $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($tid);
        $title = $term->name->value;
        $debug .= 'Term '.$title.' ('.$last_tid.') being applied to '.$node->toUrl()->toString().'. ';
        // is it already tagged with this term?
        $tids = $node->get('field_category_nondynamic')->getValue();

        $already_tagged = FALSE;
        $debug .= '<br>Checking existing terms...';
        $tag_count = 0;
        foreach($tids as $tid_r){
          //dump empty tags?
          $debug .= '<br>Looking up tid '.$tid_r['target_id'].': ';
          $existing_term = Term::load($tid_r['target_id']);
          if($existing_term != NULL){
            $debug .= 'tagName='.$existing_term->getName().',';
            $tag_count++;//the listItem array is rekeyed... so removing one will offset every item by -1
          }else{
            $debug .= 'NULL. '.$tidr_r['target_id'].' does not exist. Removing reference.';
            $node->get('field_category_nondynamic')->removeItem($tag_count);
          }

          if(in_array($last_tid, $tid_r)){//node is already tagged with last term uncovered from path.
            $already_tagged = TRUE;
          }
        }

        if(!$already_tagged){
          $debug .= '<br>Applying term to node.';
          $node->get('field_category_nondynamic')->appendItem(['target_id' => $last_tid]);
        }else{
          $debug .= '<br>Already tagged, not tagging.';
        }

        if($test == 1){
          $node->save();
        }
      }
      $debug .= '<br>Done on '.$alias.'. <a href="/node/'.$node->id().'/edit" target="_blank">('.$node->id().')</a>'.'<hr>';
      //\Drupal::entityManager()->getStorage('node')->resetCache(array($vid));
    }
    
    $response = new AjaxResponse();
    //$message = $this->t('Generated terms.');//$response->addCommand(new CssCommand('.tour-save-message', $css));

    if($vocabCreated)
      $message_vocabulary = $this->t('Vocabulary created.');
    else
      $message_vocabulary = $this->t('Vocabulary '.$name.'('.$vocab_id.') already exists.');
    $response->addCommand(new HtmlCommand('#vocabulary_info', $message_vocabulary));

    $response->addCommand(new HtmlCommand('#debug', $debug));

    $message_terms = $this->t('Number of nodes to look through: '.$number_of_nodes);
    $response->addCommand(new HtmlCommand('#vocabulary_term_info', $message_terms));

    $response->addCommand(new HtmlCommand('#vocabulary_term_list', $human_term_list));

    return $response;
  }

}