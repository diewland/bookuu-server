<?
	// database configurations
	$_host 		= 'localhost';
	$_user 		= 'root';
	$_pass 		= '1234';
	$_db_name 	= 'bookuu';
	$pdo = new PDO("mysql:host=$_host;dbname=$_db_name;charset=utf8", $_user, $_pass);

	// utilities
	// http://stackoverflow.com/questions/1634782/what-is-the-most-accurate-way-to-retrieve-a-users-correct-ip-address-in-php?rq=1
	function get_ip_address(){
		foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
			if (array_key_exists($key, $_SERVER) === true){
				foreach (explode(',', $_SERVER[$key]) as $ip){
					$ip = trim($ip); // just to be safe

					if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
						return $ip;
					}
				}
			}
		}
	}

	// APIs
	// GET: list item from isbn
	if($_GET){
		$isbn = $_GET['isbn'];
		if($isbn){
			$item_names = array();
			$sql = "select `title` from `item` where `isbn` = ? order by `created_at` desc;";
			$params = array($isbn);
			$stm = $pdo->prepare($sql);
			$stm->execute($params);
			$rows = $stm->fetchAll(PDO::FETCH_ASSOC);
			foreach($rows as $row){
				array_push($item_names, $row['title']);
			}
			echo json_encode($item_names);
		}
	}
	// POST: create an item
	else if($_POST){
		$isbn = $_POST['isbn'];
		$title = $_POST['title'];
		$ip = get_ip_address();
		if($isbn && $title){
			$sql = "insert into `item` (`isbn`, `title`, `ip`, `created_at`) VALUES (?, ?, ?, ?);";
			$stm = $pdo->prepare($sql);
			$stm->execute(array($isbn, $title, $ip, date('Y-m-d H:i:s')));
		}
	}
?>
