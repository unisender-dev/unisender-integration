<?php
require_once 'config.php';

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}

class UnisenderPlugin
{

	var $PLUGIN_PATH;
	var $PLUGIN_FOLDER_NAME;
	var $viewvars = array();

	var $fieldsOption = 'unisender_fields';
	var $formAction = "";

	function _antiMq($val)
	{
		if (get_magic_quotes_gpc() && !empty($_POST)) {
			$val = stripslashes($val);
		}
		return $val;
	}

	function UnisenderPlugin()
	{
		$this->PLUGIN_PATH = dirname(__FILE__);
		$pathInf = explode(DS, $this->PLUGIN_PATH);
		$this->PLUGIN_FOLDER_NAME = trim($pathInf[count($pathInf) - 1], DS);
		add_action('init', array($this, '_pluginInit'));
		add_action('init', array($this, '_optionsPageInit'));
		add_filter('plugin_row_meta', array($this, 'registerPluginLinks'), 10, 2);
		//$this->formAction = WP_PLUGIN_URL."/unisender/subscribe.php";
		$this->formAction = "";
		$this->set('formAction', $this->formAction);
		$this->set('usf', get_option('unisender_subscribe_form'));
	}

	function _translateJs()
	{
		include($this->PLUGIN_PATH . DS . "js" . DS . "ru" . DS . "unisender" . DS . "i18n.js.php");
	}

	function _pluginInit()
	{
		wp_register_script('ru_unisender_init', WP_PLUGIN_URL . '/' . $this->PLUGIN_FOLDER_NAME . '/js/ru/unisender/init.js');
		wp_enqueue_script('ru_unisender_init');
	}

	function addOptions()
	{
		//update_option($this->fieldsOption, serialize(array()));exit;
		add_option('unisender_list_name');
		add_option('unisender_list_title');
		add_option('unisender_api_key');
		add_option('unisender_subscribe_form');

		add_option($this->fieldsOption);
		$fields = unserialize(get_option($this->fieldsOption));

		$hasEmail = false;
		if (!empty($fields) && is_array($fields)) {
			for ($i = 0, $len = count($fields); $i < $len; $i++) {
				if ($fields[$i]['name'] == "email") {
					$hasEmail = true;
					break;
				}
			}
		}

		if (!$hasEmail) {
			$fields = (is_array($fields) && !empty($fields)) ? $fields : array();
			$fields[count($fields)] = array('name' => 'email', 'title' => __("Email", "unisender"), 'mand' => 1);
			update_option($this->fieldsOption, serialize($fields));
		}
		$this->set('fields', $fields);

	}

	function initAdminMenu()
	{
		add_action('admin_menu', array($this, '_pluginMenu'));
	}

	function _optionsPageInit()
	{
		if (is_admin()) {
			wp_enqueue_script('jquery-ui-core', get_bloginfo('siteurl') . '/wp-includes/js/jquery/ui.core.js', 'jquery');
			wp_enqueue_script('jquery-ui-sortable', get_bloginfo('siteurl') . '/wp-includes/js/jquery/ui.sortable.js', array('jquery', 'jquery-ui-core'));
			wp_enqueue_script('jquery-ui-dialog', get_bloginfo('siteurl') . '/wp-includes/js/jquery/ui.dialog.js', array('jquery', 'jquery-ui-core'));
			wp_register_script('ru_unisender_init_options', WP_PLUGIN_URL . '/' . $this->PLUGIN_FOLDER_NAME . '/js/ru/unisender/init_options.js');
			wp_register_script('ru_unisender_listloader', WP_PLUGIN_URL . '/' . $this->PLUGIN_FOLDER_NAME . '/js/ru/unisender/listloader.js');
			wp_enqueue_script('ru_unisender_listloader', false, array('ru_unisender_init', 'jquery', 'jquery-ui-core', 'jquery-ui-sortable'));

			wp_enqueue_style('unisender-styles', WP_PLUGIN_URL . '/' . $this->PLUGIN_FOLDER_NAME . '/css/style.css');
		} else {
			wp_enqueue_style('unisender-sitestyles', WP_PLUGIN_URL . '/' . $this->PLUGIN_FOLDER_NAME . '/css/sitestyle.css');
			wp_enqueue_script(
				'ru_unisender_siteForm',
				WP_PLUGIN_URL . '/' . $this->PLUGIN_FOLDER_NAME . '/js/ru/unisender/siteform.js',
				null,
				false,
				true
			);
		}
	}

	function _pluginOptions()
	{
		extract($this->viewvars);
		include($this->PLUGIN_PATH . DS . "tmpl" . DS . "options.tpl");
	}

	function _tplFormBuilder()
	{
		extract($this->viewvars);
		include($this->PLUGIN_PATH . DS . "tmpl" . DS . "formbuilder.tpl");
	}

	function _pluginMenu()
	{
		add_options_page(__('Unisender subscribe', "unisender"), __('Unisender subscribe', "unisender"), 'administrator', 'unisender-adminpage-handle', array($this, '_tplFormBuilder'));
		add_options_page(__("Unisender plugin options", "unisender"), __("Unisender plugin", "unisender"), 'administrator', 'unisender-options-handle', array($this, '_pluginOptions'));
		//add_submenu_page('tools.php',__('Unisender settings',"unisender"), __('Unisender settings',"unisender"),'administrator' ,'unisender-adminpage-handle', array($this,'_tplFormBuilder'));
	}

	function _jsonDecode($data)
	{
		//Thanks to www at walidator dot info (http://www.php.net/manual/en/function.json-decode.php#91216)
		if (!function_exists('json_decode')) {
			function json_decode($json)
			{
				$comment = false;
				$out = '$x=';

				for ($i = 0; $i < strlen($json); $i++) {
					if (!$comment) {
						if ($json[$i] == '{') $out .= ' array(';
						else if ($json[$i] == '}') $out .= ')';
						else if ($json[$i] == ':') $out .= '=>';
						else                         $out .= $json[$i];
					} else $out .= $json[$i];
					if ($json[$i] == '"') $comment = !$comment;
				}
				eval($out . ';');
				return $x;
			}
		}
		return json_decode($data);
	}

	function doApiPost($url, $data = "", $optional_headers = null)
	{
		$params = array('http' => array(
			'method' => 'POST',
			'content' => $data
		));
		if ($optional_headers !== null) {
			$params['http']['header'] = $optional_headers;
		}

		$ctx = stream_context_create($params);
		$fp = @fopen($url, 'rb', false, $ctx);
		if (!$fp) {
			return null;
		}
		$response = @stream_get_contents($fp);
		if ($response === false) {
			return null;
		}
		return $response;
	}

	function processForm()
	{
		add_option($this->fieldsOption);
		if (!empty($_POST['unisender_form_email'])) {
			$errors = array();
			if (!empty($_POST['fields'])) {
				update_option($this->fieldsOption, serialize($_POST['fields']));
				$this->set('fields', $_POST['fields']);
			}

			if (!empty($_POST['unisender_subscribe_form'])) {
				add_option('unisender_subscribe_form');
				update_option('unisender_subscribe_form', $this->_antiMq($_POST['unisender_subscribe_form']));
			}

		}

		if (!empty($_POST['unisender_subscribe'])) {

			global $unisender_subscribe_form_errors;
			global $unisender_plugin_config;
			$unisender_subscribe_form_errors = array();
			$fields = unserialize(get_option($this->fieldsOption));

			$params = array();
			if (!empty($fields)) {
				for ($i = 0, $len = count($fields); $i < $len; $i++) {
					$val = !empty($_POST[$fields[$i]['name']]) ? trim($_POST[$fields[$i]['name']]) : '';
					$params[] = "fields[" . $fields[$i]['name'] . "]=" . $val;

					if ($fields[$i]['mand']) {
						if (strlen($val) == 0) {
							$unisender_subscribe_form_errors[] = __("Field", "unisender") . " " . $fields[$i]['title'] . " " . __('is required to be filled', "unisender");
						}
					}

					if ($fields[$i]['name'] == 'email') {
						if (!is_email($val)) {
							$unisender_subscribe_form_errors[] = __("Invalid email format", "unisender");
						}
					}
				}

				if (empty($unisender_subscribe_form_errors)) {
					$url = trim($unisender_plugin_config['api_url'], "/") . "/subscribe?format=json";
					$data4Api = "list_ids=" . get_option("unisender_list_name") . "&" . implode("&", $params) . "&api_key=" . get_option("unisender_api_key");
					$res = $this->doApiPost($url, $data4Api);

					$json = $this->_jsonDecode($res);
					if (empty($json)) {
						$unisender_subscribe_form_errors[] = __("Sorry. Unisender is not available now. Please try again later.", "unisender");
					} else {
						if (!empty($json->error)) {
							$unisender_subscribe_form_errors[] = !empty($json->code) ? $json->code : "";
						} else {
							echo json_encode(array(
								'status' => 'success',
								'message' => $unisender_plugin_config['redirect_before_subscribe']
							));
							exit;
						}
					}
				}
			}
			echo json_encode(array(
				'status' => 'error',
				'message' => $unisender_subscribe_form_errors
			));
			exit;
		}
	}

	function set($name, $val)
	{
		$this->viewvars[$name] = $val;
	}

	function registerPluginLinks($links, $file)
	{
		$path = pathinfo($file);
		if (strcasecmp($path['filename'], 'unisender') == 0) {
			$links[] = '<a href="options-general.php?page=unisender-options-handle">' . __('Common settings', 'unisender') . '</a>';
			$links[] = '<a href="options-general.php?page=unisender-adminpage-handle">' . __('Subscribe form builder', 'unisender') . '</a>';
			$links[] = '<a href="http://www.unisender.com/">' . __('Unisender', 'unisender') . '</a>';
		}
		return $links;
	}

}

?>
