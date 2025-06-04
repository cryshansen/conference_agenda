<?php
namespace Drupal\conference_agenda\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ConferenceAgendaSettingsForm extends ConfigFormBase implements ContainerInjectionInterface {


  protected $logger;

  public function __construct(LoggerChannelInterface $logger) {
    $this->logger = $logger;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('logger.factory')->get('conference_agenda')
    );
  }

  protected function getEditableConfigNames() {
    return ['conference_agenda.settings'];
  }

  public function getFormId() {
    return 'conference_agenda_settings_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('conference_agenda.settings');
  
    // Add upload file element to the form
    $form['file_upload'] = [
      '#type' => 'file',
      '#title' => $this->t('Upload Excel File'),
      '#description' => $this->t('Upload the Excel file to display its contents.'),
      '#attributes' => ['accept' => '.xls'],
    ];
  
    // Add submit button
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Upload and Process'),
    ];
  
    // Check if there's processed data to display
    $processed_data = $form_state->get('processed_data');
    if (!empty($processed_data)) {
      // Render data in a table within the form
      $form['excel_table'] = [
        '#type' => 'table',
        '#header' => array_keys($processed_data[0]), // Use keys of the first row as header
        '#rows' => $processed_data,
        '#attributes' => ['id' => 'excel-table'],
      ];
    }

    // Get the processed data from the configuration
    $processed_data = $config->get('processed_data');
    if (!empty($processed_data)) {
      // Render data in a table within the form
      $form['excel_table'] = [
        '#type' => 'table',
        '#header' => array_keys($processed_data[0]), // Use keys of the first row as header
        '#rows' => $processed_data,
        '#attributes' => ['id' => 'excel-table'],
      ];
    }

  
    return parent::buildForm($form, $form_state);
  }
  
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $file = $form_state->getValue('file_upload');
    // Log the processed data for debugging
    \Drupal::logger('conference_agenda')->info('file data: @file', ['@file' => print_r($file, true)]);
    \Drupal::logger('conference_agenda')->info('Number of uploaded files: @count', ['@count' => count($file)]);
    
    if ($file instanceof Symfony\Component\HttpFoundation\File\UploadedFile && !$file->getError()) {

      // Get the first file in the array (if multiple files are allowed)
      $file = $file[0];
      \Drupal::logger('conference_agenda')->info('file data not empty: @file', ['@file' => print_r($file, true)]);
      // Load Excel data using PhpSpreadsheet
      $spreadsheet = IOFactory::load($file->getFileTemporaryPath());
      $worksheet = $spreadsheet->getActiveSheet();
  
      // Extract data from Excel and prepare for table rendering
      $tableRows = [];
      foreach ($worksheet->getRowIterator() as $row) {
        $rowData = [];
        foreach ($row->getCellIterator() as $cell) {
          $rowData[$cell->getColumn()] = $cell->getValue();
          \Drupal::logger('conference_agenda')->info('file data not empty: @column', ['@column' => $cell->getColumn() ] );
          \Drupal::logger('conference_agenda')->info('file data not empty: @value', ['@value' => $rowData[$cell->getColumn()] ] );
        }
        $tableRows[] = $rowData;
      }
  
      // Log the processed data for debugging
      \Drupal::logger('conference_agenda')->info('Processed data: @data', ['@data' => print_r($tableRows, true)]);
  
      // Save processed data to form state for rebuilding
      $form_state->set('processed_data', $tableRows);
    }
  
    parent::submitForm($form, $form_state);
  }
  
}