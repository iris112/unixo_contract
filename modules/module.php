<?
	defined('ROOT_DIR') or header('HTTP/1.1 404 Not Found') or header('Status: 404 Not Found') or die;

	if(!function_exists('module')) {
		// Класс Module
		class Module {
			public $__name;
			public $__dir;
			public $__url;

			// Обертка для функций
			public function __call($f, $a) {
				if(is_callable($f = $this->{$f})) {
					return call_user_func_array($f, $a);
				}
			}
		}

		// Получить модуль
		function module($name) {
			static $cache = [];

			if(!isset($cache[$name])) {
				if(file_exists($file = ROOT_DIR.'modules/'.$name.'/index.php') || file_exists($file = ROOT_DIR.'modules/'.$name.'.php')) {
					$module = new Module();
					$module->__name = $name;
					$module->__dir = dirname(realpath($file)).'/';
					$module->__url = '/modules/'.$name.'/';
					$init = function($__file) use(&$module) { return require_once($__file); };
					$res = call_user_func_array($init->bindTo($module), [$file]);
					$config = ($name == 'module.config' ? [] : module('module.config'));
					
					foreach((array)$config[$name] as $k => $v) {
						if(is_callable($module->{$k})) call_user_func_array($module->{$k}, (array)$v);
						else $module->{$k} = $v;
					}

					return ($cache[$name] = is_array($res) || is_callable($res) || is_object($res) ? $res : $module);
				}
				else throw new Exception("Module '$name' not found");
			}
			else return $cache[$name];
		}

		// Проверить существование модуля
		function module_exists($name) {
			return file_exists(ROOT_DIR.'modules/'.$name.'/index.php') || file_exists(ROOT_DIR.'modules/'.$name.'.php') ? true : false;
		}

		// Объединить модули
		function module_extend($from, $to) {
			if(is_string($from)) $from = module($from);
			foreach((is_string($to) ? module($to) : $to) as $k => $v) $from->{$k} = $v;
			return $from;
		}
	}