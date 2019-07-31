<?php
/**
 * @file
 * Contains \Drupal\gdk\Controller\sakController.
 */
namespace Drupal\gdk\Controller;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
/**
 * sakController for the gdk module.
 */
class GdkController extends ControllerBase {
  /**
   * Function to render JSON output for page node type.
   *
   * @param $api_key
   *   A String passed from the request URL
   *
   * @param $id
   *   A integer passed from the request URL
   *
   * @return JsonResponse
   *
   * A Json Response containing Node info or Error Message
   *
   */
  public function render_json($api_key, $id) {
	$site_config = $this->config('system.site');
    // Get the Site API Key from configuration
    $site_api_key = $site_config->get('siteapikey');
    // Check if the API Key entered in the URL is Valid
    if ( $api_key === $site_api_key ) {
      // Check if the Node ID entered in the URL is Numeric and greater than 0.
      if(is_numeric($id) && $id > 0) {
        // Load the Node using the Node id from the request URL
        $node = \Drupal::entityTypeManager()->getStorage('node')->load($id);
        // Check if the node is loaded and it is of type 'page'
        if( !empty($node) && $node->getType() === 'page' ){
          // Get node title, body, type.
          $node_title = $node->getTitle();
          $node_body = $node->get('body')->getValue();
          $node_type = $node->getType();
          // Build appropriate JSON response
          $json_response = array(
            'Node ID' => $id,
            'title' => $node_title,
            'Contents' => $node_body,
            'Node Type' => $node_type,
            'Node Access' => 'Granted',
          );
          // Return the JSON Response.
          return new JsonResponse($json_response);
        }
        // Generate appropriate Response as Node ID does not exist or is not of type `Page`
        else {
          // Build appropriate JSON response
          $json_response = array(
            'Access Denied',
            'Invalid NodeID, does not exist! or not of type `page`'
          );
          // Return the JSON Response.
          return new JsonResponse($json_response);
        }
      }
      // Generate appropriate Response as Node ID is not Numeric
      else {
        // Build appropriate JSON response
        $json_response = array(
          'Access Denied',
          'Invalid Node ID. Please enter numeric Node ID value only'
        );
        // Return the JSON Response.
        return new JsonResponse($json_response);
      }
    }
    // Generate appropriate Response as API Key is not Valid
    else {
      // Build appropriate JSON response
      $json_response = array(
        'Access Denied',
        'Invalid API Key'
      );
      // Return the JSON Response.
      return new JsonResponse($json_response);
    }
  }
}