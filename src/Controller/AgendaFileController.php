<?php

namespace Drupal\conference_agenda\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AgendaFileController extends ControllerBase {

  protected $moduleHandler;

  public function __construct(ModuleHandlerInterface $moduleHandler) {
    $this->moduleHandler = $moduleHandler;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('module_handler')
    );
  }

  public function content() {
    // Get the path to the module's directory
    $module_path = $this->moduleHandler->getModule('conference_agenda')->getPath();

    // Get the path to the Excel file in the assets folder
    $file_path = $module_path . '/assets/test.xls';

    // Read the file content
    $file_content = file_get_contents($file_path);

    // Return a render array to display the file content
    return [
      '#type' => 'markup',
      '#markup' => '<pre>' . htmlentities($file_content) . '</pre>',
    ];
  }

}
