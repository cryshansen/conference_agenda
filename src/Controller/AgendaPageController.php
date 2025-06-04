<?php
namespace Drupal\conference_agenda\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Render\MarkupInterface;
use Drupal\Core\Render\RendererInterface;
use Symfony\Component\HttpFoundation\Response;

use PhpOffice\PhpSpreadsheet\IOFactory;

class AgendaPageController extends ControllerBase {

  protected $moduleHandler;
  protected $renderer;

  public function __construct(ModuleHandlerInterface $moduleHandler, RendererInterface $renderer) {
    $this->moduleHandler = $moduleHandler;
    $this->renderer = $renderer;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('module_handler'),
      $container->get('renderer')
    );
  }

  public function content() {
    // Get the path to the module's directory
    $module_path = $this->moduleHandler->getModule('conference_agenda')->getPath();

    // Get the path to the Excel file in the assets folder
    $file_path = $module_path . '/assets/test_agenda.xls';

    // Check if the file exists
    if (!file_exists($file_path)) {
      return ['#markup' => 'File not found'];
    }

    // Read the file content
   
    // Load the Excel file using PhpSpreadsheet
    $spreadsheet = IOFactory::load($file_path);
    $worksheet = $spreadsheet->getActiveSheet();

    // Prepare the table rows
    // Prepare the table rows
    $table_rows = [];
    foreach ($worksheet->getRowIterator() as $row) {
    $rowData = [];
    foreach ($row->getCellIterator() as $cell) {
        $rowData[] = $cell->getValue();
    }
    $table_rows[] = $rowData;
    }

    // Build the HTML table
    $header = [];
    if (!empty($table_rows)) {
    $header = array_shift($table_rows); // Extract headers from the first row
    }

    $rows = '';
    foreach ($table_rows as $row) {
    $cells = '';
    foreach ($row as $cell) {
        $cells .= '<td>' . htmlspecialchars($cell) . '</td>';
    }
    $rows .= '<tr>' . $cells . '</tr>';
    }

    $html_table = '<table><thead><tr>';
    foreach ($header as $column) {
    $html_table .= '<th>' . htmlspecialchars($column) . '</th>';
    }
    $html_table .= '</tr></thead><tbody>' . $rows . '</tbody></table>';

    return [
    '#markup' => $html_table,
    ];

  }

}
