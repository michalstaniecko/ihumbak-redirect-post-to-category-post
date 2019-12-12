<?php




class IHumbak_Updater_ihrptcp {
  public function __construct() {

    add_filter('site_transient_update_plugins', array($this,'ihumbak_push_update'));
    add_action('upgrade_process_complete',array($this,'ihumbak_after_update'), 10, 2);

  }

  private $ihumbak_plugin_name = 'ihumbak-redirect-post-to-category-post';
  private $ihumbak_plugin_current_version = '1.1';

  function ihumbak_push_update( $transient ) {
    if (empty($transient->checked)) {
      return $transient;
    }

    if (false == $remote = get_transient( 'ihumbak_upgrade_' . $this->ihumbak_plugin_name)) {
      $remote = wp_remote_get( 'http://wp-update.ihumbak.website/' . $this->ihumbak_plugin_name . '/info.json', array(
        'timeout' => 10,
        'headers' => array(
          'Accept' => 'application/json'
        )
      ));

      if (!is_wp_error($remote) && isset($remote['response']['code']) && $remote['response']['code'] == 200 && !empty($remote['body'])) {
        set_transient( 'ihumbak_upgrade_' . $this->ihumbak_plugin_name, $remote, 43200);
      }
    }

    if ($remote) {
      $remote = json_decode($remote['body']);

      if ($remote && version_compare($this->ihumbak_plugin_current_version, $remote->version, '<') && version_compare($remote->requires, get_bloginfo('version'), '<')) {
        $res = new stdClass();
        $res->slug = $this->ihumbak_plugin_name;
        $res->plugin = $this->ihumbak_plugin_name . '/' . $this->ihumbak_plugin_name . '.php';
        $res->new_version = $remote->version;
        $res->tested = $remote->tested;
        $res->package = $remote->download_url;
        $res->url = $remote->homepage;
        $transient->response[$res->plugin] = $res;
      }
    }
    return $transient;
  }


  function ihumbak_after_update($upgrade_object, $options) {
    if ($options['action'] =='update' && $options['type'] === 'plugin') {
      delete_transient( 'ihumbak_upgrade_' . $this->ihumbak_plugin_name);
    }
  }

}

new IHumbak_Updater_ihrptcp();