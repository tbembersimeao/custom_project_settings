<?php

class cps_lib {
  var $tableName;
  var $conn;
  
  function cps_lib () {
    $host = "localhost";
    $username = "vagrant";
    $password = "";
    $dbname = 'redcap';
    $this->tableName = 'uf_project_settings';
    
    try {
      $this->conn = new PDO('mysql:host='.$this->host . ';dbname=' . $dbname, $username, $password);
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
      echo $e->getMessage();
      die();
    }

    $tableCheck = $this->isTableExists();;
    
    if ($tableCheck) {
      echo 'Table '. $this->tableName . ' exists in db.' . PHP_EOL;
    } else {
      echo 'Table ', $this->tableName . ' does not exist in db.' . PHP_EOL;
      echo 'Creating the table.' . PHP_EOL;
      $this->createTable();
    }
  }

  function createTable() {
    try {
      $sql ="CREATE TABLE IF NOT EXISTS $this->tableName (
      id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      project_id INT(6) NOT NULL,
      attribute_key VARCHAR(50) NOT NULL,
      attribute_value TEXT NOT NULL,
      created_by VARCHAR(50),
      created_on DATETIME DEFAULT CURRENT_TIMESTAMP,
      updated_by VARCHAR(50),
      updated_on DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      UNIQUE KEY ukey_project_id_attr_key (project_id, attribute_key),
      FOREIGN KEY (project_id)
              REFERENCES redcap_projects(project_id)
              ON DELETE CASCADE
      );" ;
     $this->conn->exec($sql);
     print("Created $this->tableName Table.\n");

    } catch(PDOException $e) {
      echo $e->getMessage();
    }
  }

  function isTableExists() {

    $prep_stmt = $this->conn->prepare("SHOW TABLES LIKE :table_name");

    $prep_stmt->bindParam(":table_name", $this->tableName, PDO::PARAM_STR);

    $sqlResult = $prep_stmt->execute();
    if ($sqlResult) {
        $row = $prep_stmt->fetch(PDO::FETCH_NUM);
        if ($row[0]) {
            return true;
        } else {
            return false;
        }
    } else {
        //some PDO error occurred
        echo("Could not check if table exists, Error: ".var_export($this->conn->errorInfo(), true));
        return false;
    }
  }

  function save($records) {
    foreach ($records as $item) {
      if ($item->id === NULL) {
        $this->insertData($item);
      } else {
        $this->updateData($item);
      }
    }
  }

  function getDataByProjectId($project_id) {
    try {
      $query = $this->conn->prepare("SELECT * from $this->tableName where project_id = :project_id");
      $query->bindParam(":project_id", $project_id, PDO::PARAM_INT);
      $query->setFetchMode(PDO::FETCH_CLASS, 'cps');
      $query->execute();
      $res = $query->fetchAll(PDO::FETCH_ASSOC);

      print_r ($res);
      return $res;
    } catch(PDOException $e) {
      echo "Error: " . $e->getMessage();
    }
  }

  function getAttributeData($project_id, $attribute_key) {
    try {
      $query = $this->conn->prepare("SELECT * from $this->tableName where project_id = :project_id and attribute_key = :attribute_key");
      $query->bindParam(":project_id", $project_id, PDO::PARAM_INT);
      $query->bindParam(":attribute_key", $attribute_key, PDO::PARAM_STR);
      $query->setFetchMode(PDO::FETCH_CLASS, 'cps');
      $query->execute();
      $res = $query->fetchAll(PDO::FETCH_ASSOC);

      return $res[0]['attribute_value'];
    } catch(PDOException $e) {
      echo "Error: " . $e->getMessage();
    }
  }  



  function insertData($inputRecord) {
    try {
      $stmt = $this->conn->prepare("INSERT INTO $this->tableName (project_id, attribute_key, attribute_value, created_by)
        VALUES (:project_id, :attribute_key, :attribute_value, :created_by)");
      $stmt->bindParam(":project_id", $inputRecord->project_id, PDO::PARAM_INT);
      $stmt->bindParam(":attribute_key", $inputRecord->attribute_key, PDO::PARAM_STR);
      $stmt->bindParam(":attribute_value", $inputRecord->attribute_value, PDO::PARAM_STR);
      $stmt->bindParam(":created_by", $inputRecord->created_by, PDO::PARAM_STR);
      
      $stmt->execute();

      echo "New record created successfully." . PHP_EOL;
    } catch(PDOException $e) {
      echo "Error: " . $e->getMessage();
    }
  }

  function updateData($inputRecord) {
    try {
      $stmt = $this->conn->prepare("UPDATE $this->tableName SET project_id=:project_id, attribute_key=:attribute_key, 
        attribute_value=:attribute_value, created_by=:created_by, updated_by =:updated_by WHERE id=:id");
      $stmt->bindParam(':project_id', $inputRecord->project_id, PDO::PARAM_INT);
      $stmt->bindParam(':attribute_key', $inputRecord->attribute_key, PDO::PARAM_STR);
      $stmt->bindParam(':attribute_value', $inputRecord->attribute_value, PDO::PARAM_STR);
      $stmt->bindParam(':created_by', $inputRecord->created_by, PDO::PARAM_STR);
      $stmt->bindParam(':updated_by', $inputRecord->updated_by, PDO::PARAM_STR);
      $stmt->bindParam(':id', $inputRecord->id, PDO::PARAM_INT);

      $stmt->execute();

      echo "Record updated successfully with id " . $this->inputRecord->id . PHP_EOL;
    } catch(PDOException $e) {
      echo "Error: " . $e->getMessage();
    }
  }

}

?>