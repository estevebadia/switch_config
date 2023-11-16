<?php
namespace ltisource_switch_config;

require_once __DIR__ . '/kaltura_client.php';

global $CFG;

class kaltura_api {
  protected $client;
  protected $logger;

  function __construct($logger) {
    $this->client = self::buildClient();
    $this->logger = $logger;
  }

  protected static function buildClient() {
    global $USER;
    // Get some config from local_kaltura plugin.
    $adminsecret = get_config('ltisource_switch_config', 'adminsecret');
    $partner_id = get_config('ltisource_switch_config', 'partner_id');
    $url = get_config('ltisource_switch_config', 'api_url');

    $user = isset($USER) && isset($USER->email) ? $USER->email : '';

    $config = new \KalturaConfiguration();
    $config->serviceUrl = $url;
    $config->format = \KalturaClientBase::KALTURA_SERVICE_FORMAT_JSON;

    $client = new RetryKalturaClient($config);
    $ks = $client->generateSession($adminsecret, $user, \KalturaSessionType::ADMIN, $partner_id);
    $client->setKs($ks);

    return $client;
  }

  /**
   * Fetches a category given its full name.
   * Eg: "Moodle>site>channels>2-50"
   */
  public function getCategoryByFullName($fullName) {
    $filter = new \KalturaCategoryFilter();
    $filter->fullNameEqual = $fullName;
    $pager = null;
    $result = $this->client->category->listAction($filter, $pager);
    return count($result->objects) > 0 ? $result->objects[0] : false;
  }

  /**
   * Creates a new category.
   * @param object $category the category object to be created.
   */
  public function createCategory($category) {
    // Build new category object
    $fields = ['name','parentId', 'description', 'tags', 'privacy',
    'inheritanceType', 'defaultPermissionLevel', 'owner', 'referenceId',
    'contributionPolicy', 'privacyContext', 'partnerSortValue', 'partnerData',
    'defaultOrderBy', 'moderation', 'isAggregationCategory', 'aggregationCategories'];
    $newcategory = new \KalturaCategory();
    foreach($fields as $field) {
      $newcategory->{$field} = $category->{$field};
    }
    // Create new empty category.
    try {
      $newcategory = $this->client->category->add($newcategory);
      return $newcategory;
    } catch (\Exception $e) {
      // Prevent pausing execution if cant create new category.
      $this->logger->error("Could not create category. " . $e->getMessage());
      return false;
    }
  }


  /**
   * Copy the given category to a new one with given name.
   * @param object $category the category object to be copied.
   * @param object $parent the parent category of the destination category.
   * @param string $newname the name of the new category
   * @return object|bool the new category or false.
   */
  public function copyCategory($category, $parent, $newname) {
    $model = clone($category);
    $model->name = $newname;
    $model->parentId = $parent->id;
    $newcategory = $this->getCategoryByFullName($parent->fullName . ">" . $newname);
    if ($newcategory !== false) {
      // The destination category already exists.
      // If the category is empty, we just use it. Otherwise we stop the execution.
      if (isset($newcategory->entriesCount) && $newcategory->entriesCount == 0) {
        $this->logger->log("Category $newname already exists ({$newcategory->id}) but is empty, using it.");
      } else {
        $this->logger->error("Category $newname already exists ({$newcategory->id}) and has content, skipping copy.");
        return false;
      }
    } else {
      $newcategory = $this->createCategory($model);
      if ($newcategory === false) {
        $this->logger->error("Could not create category {$newname}.");
        return false;
      }
    }

    $this->copyMedia($category, $newcategory);
    return $newcategory;
  }

  /**
   * Copy all media from one category to another.
   */
  public function copyMedia($fromcategory, $tocategory) {
    $filter = new \KalturaCategoryEntryFilter();
    $filter->categoryIdEqual = $fromcategory->id;

    // Add all media from old category to new category.
    $result = $this->client->categoryEntry->listAction($filter, null);
    $entryids = array_map(function($object) { return $object->entryId; }, $result->objects);
    // Check which entries are already in target category.
    $filter->categoryIdEqual = $tocategory->id;
    $filter->entryIdIn = implode(',', $entryids);
    $existing = $this->client->categoryEntry->listAction($filter, null);
    $existingids = array_map(function($object) { return $object->entryId; }, $existing->objects);

    foreach ($entryids as $id) {
      if (in_array($id, $existingids)) {
        $this->logger->log("Entry id {$id} already in category, no need to add");
      } else {
        $entry = new \KalturaCategoryEntry();
        $entry->categoryId = $tocategory->id;
        $entry->entryId = $id;
        try {
          $this->client->categoryEntry->add($entry);
          $this->logger->log("Added entry {$id} to category {$tocategory->id}");
        } catch (\Exception $e) {
          // Don't pause execution.
          $this->logger->error("Error adding entry {$id} to category {$tocategory->id}. Should be fixed manually! " . $e->getMessage());
        }
      }
    }
  }

  public function deleteCategory($category) {
    return $this->client->category->delete($category->id, \KalturaNullableBoolean::FALSE_VALUE);
  }

  public function getClient() {
    return $this->client;
  }
}
