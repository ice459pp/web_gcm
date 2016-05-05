<?php
/*
 * 以PDO物件的方式，對資料庫進行操作
 * 配合之前的DB物件，盡量保留原先的撰寫格式，同時省去重複的程式碼操作
 *
 */

class PDO_DB {
	public $pdo = NULL;

	// 建構子，直接對資料庫進行連線
	public function __construct($dbDrivers, $dbHost, $dbUser, $dbPwd, $dbName) {
		return $this->connect($dbDrivers, $dbHost, $dbUser, $dbPwd, $dbName);
	} // end function __construct

	// 對資料庫進行連線，儲存並回傳PDO物件
	public function connect($dbDrivers, $dbHost, $dbUser, $dbPwd, $dbName) {
		$dsn = $dbDrivers.":dbname=".$dbName.";host=".$dbHost.";";

		try {
			$this->pdo = new PDO($dsn, $dbUser, $dbPwd, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			// 設定查詢出現問題，就丟出例外
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch(Exception $e) {
			throw new Exception("Database error: ".$e->getMessage());
		}

		return $this->pdo;
	} // end function connect

	// 針對Prepared statement的查詢，統一透過這個function執行
	public function execute($query, $values = NULL) {
		$this->chkPDO();

		if(is_null($values)) {
			$values = array();
		} else if(!is_array($values)) {
			$values = array($values);
		}

		$stmt = $this->prepQuery($query);
		
		// 查詢失敗丟出一例外訊息，成功回傳PDOStatement物件
		if($stmt->execute($values)){
			return $stmt;
		}else{
			throw new Exception("Database Query failure");
		}
	} // end function execute

	// 一般的query功能
	public function query($query, $values = NULL){
		return $this->execute($query, $values);
	} // end function query

	// 回傳查詢結果的資料數量
	public function no($stmt){
		return $stmt->rowCount();
	} // end function no

	// 取得一筆查詢結果, 預設是關聯是陣列的方式取回
	public function fetch($stmt, $fetch_style = PDO::FETCH_ASSOC) {
		return $stmt->fetch($fetch_style);
	} // end function fetch

	// 代入Query參數後，直接回傳查詢結果
	public function quickFetch($query, $values, $fetch_style = PDO::FETCH_ASSOC) {
		$stmt = $this->Query($query, $values);
		return $stmt->fetch($fetch_style);
	} // end function quickFetch

	// 代入Query參數後，判斷資料庫是否有資料存在
	public function dataExist($query, $values) {
		$stmt = $this->Query($query, $values);
		return $this->No($stmt) > 0 ? true : false ;
	} // end function dataExist

	// 以陣列的方式，新增一筆資料進資料庫
	public function insert($tbl, $insertList) {
		$colList = array();
		$setList = array();
		$dataList = array();

		foreach($insertList as $k => $v ) {
			$colList[] = "`".$k."`";
			if($v == "now()") {
				$setList[] = $v;
			}else {
				$setList[] = "?";
				$dataList[] = $v;
			}
		}

		$queryStr = "INSERT INTO `".$tbl."` (".implode(", ", $colList).") VALUES (".implode(", ", $setList).")";

		return $this->execute($queryStr, $dataList);
	} // end function insert

	// 在Insert完之後，取得最後一筆的id
	public function last_id() {
		return $this->pdo->lastInsertId();
	} // end function last_id

	// 以陣列的方式，更新一筆資料庫的資料
	public function update($tbl, $updateList, $column, $compare) {
		$setList = array();
		$dataList = array();

		foreach($updateList as $k => $v ) {
			if($v == "now()"){
				$setList[] = "`".$k."` = ".$v;
			} else {
				$setList[] = "`".$k."` = ?";
				$dataList[] = $v;
			}
		}

		$queryStr = "UPDATE `".$tbl."` SET ".implode(", ", $setList)." WHERE `".$column."` = ?";
		$dataList[] = $compare;

		return $this->execute($queryStr, $dataList);
	} // end function update

	// 刪除資料庫的資料
	public function delete($tbl, $column, $compare) {
		$queryStr = "DELETE FROM `".$tbl."` WHERE `".$column."` = ?";

		return $this->execute($queryStr, $compare);	
	} // end function delete

	/*
		私有物件函數，方便類別內進行部分操作
	*/

	// 檢查PDO物件是否為NULL，確保資料庫有連線的情況下進行查詢
	private function chkPDO() {
		if(is_null($this->pdo)) {
			throw new Exception("Database is not connect.");
		}
	} // end function chkPDO

	// 以Prepared statement進行查詢，對query string進行prepare
	private function prepQuery($query){
		return $this->pdo->prepare($query);
	} // end function prepQuery
}
?>