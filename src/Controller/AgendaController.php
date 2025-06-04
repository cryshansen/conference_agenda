<?php

namespace Drupal\conference_agenda\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Controller for displaying appointment calendar.
 */
class AgendaController extends ControllerBase {
  
  protected $configFactory;

  
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;

  }

  public static function create(ContainerInterface $container) {
    return new static(
     
      $container->get('config.factory'),
    
    );
  }


  /**
   * Displays the Agenda Program information.
   *
   *
   * @return array
   *   A render array representing the calendar.
   */
  public function displayAgenda() {
    
    // Get the site name from configuration.
    $site_name = $this->configFactory->get('system.site')->get('name');
    // Get tab labels and tab panels from configuration.
    $config = $this->configFactory->get('conference_agenda.settings');

    $tab_labels = [];
    $tab_panels = [];

    // Loop through each tab to get its label and content.
    for ($i = 1; $i <= 4; $i++) {
      $tab_labels['tab' . $i] = $config->get('tab_label_' . $i);
      $tab_panels['tab' . $i] = [
        'label' => $config->get('tab_label_' . $i),
        'content' => $config->get('tab_description_' . $i),
      ];
    }

    // Create and return the render array for your modal content
    return [
      '#theme' => 'conference_agenda_tab_theme_hook',
      '#title' => $this->t('Agena Program'),
      '#description' => $this->t('Tabbed Date Agenda for conference'),
      '#tab_labels' => $tab_labels,
      '#tab_panels' => $tab_panels,
      '#site_name' => $site_name,
      '#attached' => [
        'library' => [
          'core/drupal.dialog.ajax',
          'appointment_calendar/calendar-scripts', // Include necessary libraries
        ],
      ],
    ];
    
  }
  public function content() {
    // Get the path to the Excel file in the assets folder
    $file_path = drupal_get_path('module', 'conference_agenda') . '/assets/ttest.xls';

    // Read the file content
    $file_content = file_get_contents($file_path);

    // Return a render array to display the file content (you can modify this based on your requirements)
    return [
      '#type' => 'markup',
      '#markup' => '<pre>' . htmlentities($file_content) . '</pre>',
    ];
  }
  public function getTabContent(){

    $tab_content='<div id="conference-agenda">Conference Agenda Tabs Displayed Here.</div>';

    return $tab_content;

  }



}
