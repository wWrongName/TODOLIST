<?php
	class ArrayElement {
		public $id;
		public $text;
		public $delete;

		public function __construct($id, $text, $delete) {
			$this->id = $id;
			$this->text = $text;
			$this->delete = $delete;
		}
	}

    $list = json_decode(file_get_contents('list.json'));
    $notDeleted = true;
    if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'DELETE') {
        if (isset($_SERVER['REQUEST_URI']))
            $vars = explode('/', $_SERVER['REQUEST_URI']);
        for ($i = 0; $i < count($list); $i++) {
        	if (isset($vars[3]) && ($vars[3] == $list[$i]->id)) {
        		array_splice($list, $i, 1);
        		$notDeleted = false;
                header("HTTP/1.0 200 OK");
        	}
        }
        if ($notDeleted)
        	header("HTTP/1.0 406 Not Acceptable");
    } elseif (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'PUT') {
    	$vars = json_decode(file_get_contents("php://input"));
		if (isset($vars->text)){
			$floatingElement = 0;
			$insertFlag = false;
    		foreach ($list as &$element) {
    			if ($element->id - $floatingElement > 0) {
    				$insertedElement = array($floatingElement => new ArrayElement($floatingElement, $vars->text, "x"));
    				array_splice($list, $floatingElement, 0, $insertedElement);
    				$insertFlag = true;
    				break 1;
    			} else {
    				$floatingElement++;
    			}
    		}
    		unset($element);
    		if (!$insertFlag){
    			array_push($list, new ArrayElement($floatingElement, $vars->text, "x"));
    		}
    		header("HTTP/1.0 200 OK");
    	}
        else
            header("HTTP/1.0 400 Bad Request");	
    } elseif (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
        $vars = json_decode(file_get_contents("php://input"));
        if (isset($vars->id) && isset($vars->text)) {
            for ($i = 0; $i<count($list); $i++)
                if ($list[$i]->id == $vars->id) {
                    $list[$i]->text = $vars->text;
                    break 1;
                }
            header("HTTP/1.0 200 OK");
        }
        else
            header("HTTP/1.0 405 Invalid input");
    }
    file_put_contents('list.json', json_encode($list));
    header('Content-Type: application/json');
    echo json_encode($list);
?>