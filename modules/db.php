<?
	defined('ROOT_DIR') or header('HTTP/1.1 404 Not Found') or header('Status: 404 Not Found') or die;

	// Соединение с базой
	$this->connection = null;

	// Коннект к базе
	$this->connect = function($host, $name, $user, $pass) {
		try {
			$this->connection = new PDO("mysql:host=$host;dbname=$name;charset=utf8", $user, $pass);
		}
		catch(PDOException $e) {
			throw new Exception("Error connecting to database");
		}
	};

	// Запрос к базе
	$this->query = function($query, $params = [], $flags = 0, $arg = null, $args = null) {
		if(is_array($params) && count($params)) {
			$res = $this->connection->prepare($query);
			$res->execute(array_map(function($v) { return (string)$v; }, $params));
			return $res;
		}
		return call_user_func_array([$this->connection, 'query'], $args !== null ? [$query, $flags, $arg, $args] : ($arg !== null ? [$query, $flags, $arg] : [$query, $flags]));
	};

	// Экранировать строку
	$this->sql = function($val) {
		return $this->connection->quote($val);
	};

	// Получить строку
	$this->row = function($query, $params = [], $flags = PDO::FETCH_ASSOC) {
		return $this->query($query, $params)->fetch($flags);
	};

	// Получить все строки
	$this->rows = function($query, $params = [], $flags = PDO::FETCH_ASSOC, $arg = null, $args = null) {
		return call_user_func_array([$this->query($query, $params), 'fetchAll'], $args !== null ? [$flags, $arg, $args] : ($arg !== null ? [$flags, $arg] : [$flags]));
	};

	// Получить колонку
	$this->val = function($query, $params = [], $col_number = 0) {
		return $this->query($query, $params)->fetchColumn($col_number);
	};

	// Вставить строку
	$this->insert = function($table, $row, $get_insert_row = false) {
		if(is_string($table) && trim($table) && is_array($row) && count($row)) {
			if($this->query("INSERT INTO `$table` SET `".implode('` = ?, `', array_keys($row))."` = ?", array_values($row)) !== false) {
				return $get_insert_row ? $this->row("SELECT * FROM `$table` WHERE `id` = ?", [$this->connection->lastInsertId()]) : (int)$this->connection->lastInsertId();
			}
		}
		return false;
	};

	// Обновить строку
	$this->update = function($table, $row, $where, $get_update_row = false) {
		if(is_string($table) && trim($table) && is_array($row) && count($row)) {
			if(is_array($where) && count($where)) {
				if(($res = $this->query("UPDATE `$table` SET `".implode('` = ?, `', array_keys($row))."` = ? WHERE `".implode('` = ? AND `', array_keys($where)).'` = ?', array_merge(array_values($row), array_values($where)))) !== false) {
					return $res->rowCount();
				}
			}
			else if(is_numeric($where) && (int)$where > 0) {
				if(($res = $this->query("UPDATE `$table` SET `".implode('` = ?, `', array_keys($row))."` = ? WHERE `id` = ?", array_merge(array_values($row), [$where]))) !== false) {
					return $get_update_row ? $this->row("SELECT * FROM `$table` WHERE `id` = ?", [$where]) : $res->rowCount();
				}
			}
		}
		return false;
	};

	// Удалить строки
	$this->delete = function($table, $where) {
		if(is_string($table) && trim($table)) {
			if(is_array($where) && count($where)) {
				if(($res = $this->query("DELETE FROM `$table` WHERE `".implode('` = ? AND `', array_keys($where)).'` = ?', array_values($where))) !== false) {
					return $res->rowCount();
				}
			}
			else if(is_numeric($where) && (int)$where > 0) {
				if(($res = $this->query("DELETE FROM `$table` WHERE `id` = ?", [$where])) !== false) {
					return $res->rowCount();
				}
			}
		}
		return false;
	};

	// MySQL хранилище
	$this->storage = function($key, $val = null, $time = 0) {
		if(func_num_args() >= 2) {
			$this->query("INSERT INTO `storage` (`key`, `val`, `time`) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE `val` = VALUES(`val`), `time` = VALUES(`time`)", [$key, json_encode($val, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK), (int)$time]);
			return $val;
		}
		else return json_decode($this->val("SELECT `val` FROM `storage` WHERE `key` = ? AND (`time` = 0 OR `time` >= ?)", [$key, time()]), true);
	};